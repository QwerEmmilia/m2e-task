<?php

namespace App\Command;

use App\Entity\Data;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\SerializerInterface;

class LoadDataCommand extends Command
{
    protected static $defaultName = 'app:import-data';

    private $parameterBag;
    private $serializer;
    private $entityManager;

    public function __construct(ParameterBagInterface $parameterBag, SerializerInterface $serializer, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->parameterBag = $parameterBag;
        $this->serializer = $serializer;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Import data from CSV and XML files into the database')
            ->addArgument('csv_file', InputArgument::REQUIRED, 'Path to the CSV file')
            ->addArgument('xml_file', InputArgument::REQUIRED, 'Path to the XML file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $csvFile = $input->getArgument('csv_file');
        $xmlFile = $input->getArgument('xml_file');

        $csvData = $this->readCsvFile($csvFile);
        $xmlData = $this->readXmlFile($xmlFile);

        $mergedData = $this->mergeData($csvData, $xmlData);

        $io = new SymfonyStyle($input, $output);
        $io->title('Importing Data');

        foreach ($mergedData as $data) {
            $id = $data['id'];

            $existingRecord = $this->entityManager->getRepository(Data::class)->find($id);

            if (!$existingRecord) {
                $dataE = new Data();
            } else {
                $dataE = $existingRecord;
            }

            $dataE->setId($data['id']);
            $dataE->setPurchaseDate($data['purchase_date']);
            $dataE->setShipToName($data['ship-to_name']);
            $dataE->setCustomerEmail($data['customer_email']);
            $dataE->setGrantTotal($data['grant_total']);
            $dataE->setStatus($data['status']);

            $this->entityManager->persist($dataE);
        }

        $this->entityManager->flush();

        $io->success('Data imported successfully.');

        return Command::SUCCESS;
    }

    private function readCsvFile($csvFile)
    {
        $csvData = [];

        if (($handle = fopen($csvFile, 'r')) !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                // Рядок CSV перетворюється на асоціативний масив
                $csvData[] = [
                    'id' => $row[0],
                    'purchase_date' => $row[1],
                    'ship-to_name' => $row[2],
                    'customer_email' => $row[3],
                    'grant_total' => $row[4],
                    'status' => $row[5],
                ];
            }
            fclose($handle);
        }

        return $csvData;
    }

    private function readXmlFile($xmlFile)
    {
        $xmlData = [];

        $xml = simplexml_load_file($xmlFile);

        foreach ($xml->Worksheet->Table->Row as $row) {
            // Для XML рядку, потрібно отримати дані з елементів Cell
            $id = (string)$row->Cell[0]->Data;
            $purchaseDate = (string)$row->Cell[1]->Data;
            $shipToName = (string)$row->Cell[2]->Data;
            $customerEmail = (string)$row->Cell[3]->Data;
            $grantTotal = (string)$row->Cell[4]->Data;
            $status = (string)$row->Cell[5]->Data;

            $xmlData[] = [
                'id' => $id,
                'purchase_date' => $purchaseDate,
                'ship-to_name' => $shipToName,
                'customer_email' => $customerEmail,
                'grant_total' => $grantTotal,
                'status' => $status,
            ];
        }

        return $xmlData;
    }

    private function mergeData($csvData, $xmlData)
    {
        // Об'єднуємо дані з CSV і XML, де ключ - це унікальний ідентифікатор "id"
        $mergedData = [];

        foreach ($csvData as $csvRow) {
            $mergedData[$csvRow['id']] = $csvRow;
        }

        foreach ($xmlData as $xmlRow) {
            $mergedData[$xmlRow['id']] = $xmlRow;
        }

        // Повертаємо масив об'єднаних даних
        return array_values($mergedData);
    }

}
