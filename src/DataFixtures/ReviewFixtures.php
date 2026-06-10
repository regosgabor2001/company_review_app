<?php

namespace App\DataFixtures;

use App\Entity\Review;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // A Faker inicializálása (akár magyar nyelvű adatokkal is futhat: 'hu_HU')
        $faker = Factory::create('hu_HU');

        // Fix cégnevek, hogy szép statisztikát kapjunk a csoportosításnál
        $companies = ['Google', 'Facebook Inc.', 'Stripe', 'Slack'];

        foreach ($companies as $companyName) {
            // Minden céghez generálunk valahány (pl. 3 és 8 közötti) véleményt
            $reviewCount = $faker->numberBetween(3, 8);

            for ($i = 0; $i < $reviewCount; $i++) {
                $review = new Review();
                
                $review->setCompanyName($companyName);
                $review->setRating($faker->numberBetween(1, 5)); // 1-5 közötti értékelés [cite: 17, 27]
                
                // Generálunk egy szöveget, néha rövidebbet, néha hosszabbat (a csonkítás teszteléséhez)
                $review->setReviewText($faker->paragraph($faker->numberBetween(1, 4)));
                
                $review->setAuthorEmail($faker->email);

                // Megjegyzés: A created_at és updated_at mezőket nem kell kézzel beállítani,
                // mert az entitásban lévő @PrePersist Lifecycle Callback automatikusan kitölti őket! [cite: 17]

                // Előkészítjük mentésre (mint Laravelben a gyűjtőbe pakolás)
                $manager->persist($review);
            }
        }

        // Egyetlen tranzakcióban kilőjük az összes INSERT-et az adatbázisba (mint a flush)
        $manager->flush();
    }
}