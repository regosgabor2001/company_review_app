<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/companies')]
final class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_companies')]
    public function index(CompanyRepository $companyRepository): Response
    {
        $companyStatistics = $companyRepository->getCompanyStatistics();

        return $this->render('company/index.html.twig', [
            'companies' => $companyStatistics,
        ]);
    }
}
