<?php

namespace App\Tests\Repository;

use App\Entity\Review;
use App\Entity\Company;
use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ReviewRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
        $this->reviewRepository = $container->get(ReviewRepository::class);

        $this->clearDatabase();
    }

    public function testFindPaginatedWithSearchAndSort(): void
    {
        $company1 = new Company();
        $company1->setName('Google');
        $this->entityManager->persist($company1);

        $company2 = new Company();
        $company2->setName('Stripe');
        $this->entityManager->persist($company2);

        $review1 = (new Review())->setCompany($company1)->setRating(5)->setReviewText('Szuper')->setAuthorEmail('a@test.com');
        $review2 = (new Review())->setCompany($company1)->setRating(2)->setReviewText('Rossz')->setAuthorEmail('b@test.com');
        $review3 = (new Review())->setCompany($company2)->setRating(4)->setReviewText('Kiváló')->setAuthorEmail('c@test.com');

        $this->entityManager->persist($review1);
        $this->entityManager->persist($review2);
        $this->entityManager->persist($review3);
        $this->entityManager->flush();

        $searchResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'desc');
        $this->assertCount(2, $searchResults);
        $this->assertSame('Google', $searchResults[0]->getCompany()->getName());

        $descResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'desc');
        $this->assertSame(5, $descResults[0]->getRating());
        $this->assertSame(2, $descResults[1]->getRating());

        $ascResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'asc');
        $this->assertSame(2, $ascResults[0]->getRating());
        $this->assertSame(5, $ascResults[1]->getRating());

        $paginatedResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 1, 'Google', 'desc');
        $this->assertCount(1, $paginatedResults);

        $count = $this->reviewRepository->countWithSearch('Google');
        $this->assertSame(2, $count);
    }

    private function clearDatabase(): void
    {
        $reviews = $this->reviewRepository->findAll();
        foreach ($reviews as $review) {
            $this->entityManager->remove($review);
        }
        $this->entityManager->flush();

        $companies = $this->entityManager->getRepository(Company::class)->findAll();
        foreach ($companies as $company) {
            $this->entityManager->remove($company);
        }
        $this->entityManager->flush();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}