<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\DataRepository;

class DataController extends AbstractController
{

    #[Route('/find', name: 'app_find', methods: 'GET')]
    public function findData(Request $request, DataRepository $dataRepository): JsonResponse
    {
        $shipToName = $request->query->get('shipToName');
        $customerEmail = $request->query->get('customerEmail');
        $status = $request->query->get('status');
        $limit = $request->query->getInt('limit', 10);
        $page = $request->query->getInt('page', 1);

        $criteria = [];
        if ($shipToName) {
            $criteria['shipToName'] = $shipToName;
        }
        if ($customerEmail) {
            $criteria['customerEmail'] = $customerEmail;
        }
        if ($status) {
            $criteria['status'] = $status;
        }

        $data = $dataRepository->findByCriteria($criteria, $limit, $page);

        return $this->json($data);
    }
}
