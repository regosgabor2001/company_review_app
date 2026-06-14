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

        $positiveReviews = [
            'Gyors és korrekt ügyintézés, csak ajánlani tudom.',
            'Segítőkész csapat, minden kérdésemre gyors választ kaptam.',
            'Kiváló szolgáltatás, teljes mértékben elégedett vagyok.',
            'Professzionális hozzáállás és pontos munkavégzés.',
            'Megbízható cég, a vállalt határidőket betartották.',
            'Nagyon pozitív tapasztalataim voltak, máskor is őket választanám.',
        ];

        $neutralReviews = [
            'Összességében megfelelő szolgáltatást kaptam.',
            'A munka rendben volt, de lehetne gyorsabb a kommunikáció.',
            'Nem volt különösebb problémám, de kiemelkedő élmény sem.',
            'A szolgáltatás megfelelt az elvárásaimnak.',
            'Átlagos tapasztalataim voltak a céggel.',
        ];

        $negativeReviews = [
            'Lassú kommunikáció és nehézkes ügyintézés.',
            'Nem azt kaptam, amire számítottam.',
            'Többször kellett érdeklődnöm a megrendelés állapotáról.',
            'A szolgáltatás minősége elmaradt a várakozásaimtól.',
            'Csalódott vagyok, legközelebb mást választok.',
            'Sajnos nem voltam elégedett a kapott szolgáltatással.',
        ];

        $companies = $manager
            ->getRepository(Company::class)
            ->findAll();

        foreach ($companies as $company) {
            $reviewCount = $faker->numberBetween(3, 8);

            for ($i = 0; $i < $reviewCount; ++$i) {
                $rating = $faker->numberBetween(1, 5);

                if ($rating >= 4) {
                    $reviewText = $faker->randomElement($positiveReviews);
                } elseif (3 === $rating) {
                    $reviewText = $faker->randomElement($neutralReviews);
                } else {
                    $reviewText = $faker->randomElement($negativeReviews);
                }

                $review = new Review();

                $review->setCompany($company);
                $review->setRating($rating);
                $review->setReviewText($reviewText);
                $review->setAuthorEmail($faker->safeEmail());

                $manager->persist($review);
            }
        }

        $manager->flush();
    }
}
