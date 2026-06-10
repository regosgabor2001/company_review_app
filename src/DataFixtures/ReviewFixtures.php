<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function getDependencies(): array
    {
        return [
            CompanyFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('hu_HU');

        $companies = $manager
            ->getRepository(Company::class)
            ->findAll();

        foreach ($companies as $company) {
            $reviewCount = $faker->numberBetween(3, 8);

            for ($i = 0; $i < $reviewCount; $i++) {
                $review = new Review();

                $review->setCompany($company);
                $review->setRating($faker->numberBetween(1, 5));
                $review->setReviewText(
                    $faker->paragraph($faker->numberBetween(1, 4))
                );
                $review->setAuthorEmail($faker->email());

                $manager->persist($review);
            }
        }

        $manager->flush();
    }
}