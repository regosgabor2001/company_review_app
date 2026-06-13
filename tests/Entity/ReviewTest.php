<?php

namespace App\Tests\Entity;

use App\Entity\Review;
use App\Entity\Company;
use PHPUnit\Framework\TestCase;

class ReviewTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $review = new Review();
        $company = new Company();

        $review->setCompany($company);
        $review->setRating(5);
        $review->setReviewText('Minden szuper volt!');
        $review->setAuthorEmail('teszt@example.com');

        $this->assertSame($company, $review->getCompany());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('Minden szuper volt!', $review->getReviewText());
        $this->assertSame('teszt@example.com', $review->getAuthorEmail());
    }

    public function testPrePersistSetsDatesAutomatically(): void
    {
        $review = new Review();

        $this->assertNull($review->getCreatedAt());
        $this->assertNull($review->getUpdatedAt());

        $review->setCreatedAtValue();

        $this->assertNotNull($review->getCreatedAt());
        $this->assertNotNull($review->getUpdatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $review->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $review->getUpdatedAt());

        $this->assertSame(
            $review->getCreatedAt()->getTimestamp(), 
            $review->getUpdatedAt()->getTimestamp()
        );
    }

    public function testPreUpdateChangesOnlyUpdatedAt(): void
    {
        $review = new Review();
        
        $pastDate = new \DateTimeImmutable('2026-01-01 10:00:00');
        $review->setCreatedAt($pastDate);
        $review->setUpdatedAt($pastDate);

        $review->setUpdatedAtValue();

        $this->assertSame($pastDate, $review->getCreatedAt());

        $this->assertNotSame($pastDate, $review->getUpdatedAt());
        $this->assertGreaterThan(
            $pastDate->getTimestamp(), 
            $review->getUpdatedAt()->getTimestamp()
        );
    }
}