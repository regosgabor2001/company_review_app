<?php

namespace App\Twig\Extension;

use App\Repository\CompanyRepository;
use App\Twig\Runtime\AppExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function __construct(
        private CompanyRepository $companyRepository
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('companies', [$this, 'getCompanies']),
        ];
    }

    public function getCompanies(): array
    {
        return $this->companyRepository->findAll();
    }
}
