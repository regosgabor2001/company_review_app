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

        // Ensure a clean state before each test by removing existing rows.
        $this->clearDatabase();
    }

    public function testFindPaginatedWithSearchAndSort(): void
    {
        // Create two companies and persist them to the database.
        $company1 = new Company();
        $company1->setName('Google');
        $this->entityManager->persist($company1);

        $company2 = new Company();
        $company2->setName('Stripe');
        $this->entityManager->persist($company2);

        // Create reviews for each company for search and sort assertions.
        $review1 = (new Review())
            ->setCompany($company1)
            ->setRating(5)
            ->setReviewText('Szuper')
            ->setAuthorEmail('a@test.com');
        $review2 = (new Review())
            ->setCompany($company1)
            ->setRating(2)
            ->setReviewText('Rossz')
            ->setAuthorEmail('b@test.com');
        $review3 = (new Review())
            ->setCompany($company2)
            ->setRating(4)
            ->setReviewText('Kiváló')
            ->setAuthorEmail('c@test.com');

        $this->entityManager->persist($review1);
        $this->entityManager->persist($review2);
        $this->entityManager->persist($review3);
        $this->entityManager->flush();

        // Search by company name and verify the returned items are filtered correctly.
        $searchResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'desc');
        $this->assertCount(2, $searchResults);
        $this->assertSame('Google', $searchResults[0]->getCompany()->getName());

        // Verify descending rating order for the matching reviews.
        $descResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'desc');
        $this->assertSame(5, $descResults[0]->getRating());
        $this->assertSame(2, $descResults[1]->getRating());

        // Verify ascending rating order when sort direction is changed.
        $ascResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 10, 'Google', 'asc');
        $this->assertSame(2, $ascResults[0]->getRating());
        $this->assertSame(5, $ascResults[1]->getRating());

        // Verify pagination returns only a single review when limit is 1.
        $paginatedResults = $this->reviewRepository->findPaginatedWithSearchAndSort(1, 1, 'Google', 'desc');
        $this->assertCount(1, $paginatedResults);

        // Verify the count method honors the search filter.
        $count = $this->reviewRepository->countWithSearch('Google');
        $this->assertSame(2, $count);
    }

    private function clearDatabase(): void
    {
        // Remove all reviews before removing companies to preserve foreign key constraints.
        $reviews = $this->reviewRepository->findAll();
        foreach ($reviews as $review) {
            $this->entityManager->remove($review);
        }
        $this->entityManager->flush();

        // Remove all companies after reviews are cleared.
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