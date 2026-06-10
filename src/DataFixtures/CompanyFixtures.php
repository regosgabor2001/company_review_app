<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CompanyFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $companies = [
            'Google',
            'Facebook Inc.',
            'Stripe',
            'Slack',
        ];

        foreach ($companies as $companyName) {
            $company = new Company();
            $company->setName($companyName);

            $manager->persist($company);
        }

        $manager->flush();
    }
}
