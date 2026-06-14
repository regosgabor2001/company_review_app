<?php

namespace App\Tests\Entity;

use App\Entity\Review;
use App\Entity\Company;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for the Review entity.
 * Verifies standard data structure (getners/setters) and Doctrine lifecycle callbacks.
 */
class ReviewTest extends TestCase
{
    /**
     * Test the basic getter and setter functionality to verify data integrity.
     */
    public function testGettersAndSetters(): void
    {
        // --- ARRANGE ---
        $review = new Review();
        $company = new Company();

        // --- ACT ---
        // Set values using the entity mutator methods
        $review->setCompany($company);
        $review->setRating(5);
        $review->setReviewText('Minden szuper volt!');
        $review->setAuthorEmail('teszt@example.com');

        // --- ASSERT ---
        // Assert that the returned values exactly match the injected data
        $this->assertSame($company, $review->getCompany());
        $this->assertSame(5, $review->getRating());
        $this->assertSame('Minden szuper volt!', $review->getReviewText());
        $this->assertSame('teszt@example.com', $review->getAuthorEmail());
    }

    /**
     * Test the PrePersist lifecycle callback.
     * Verifies that timestamps are automatically initialized when they are null.
     */
    public function testPrePersistSetsDatesAutomatically(): void
    {
        // --- ARRANGE ---
        $review = new Review();

        // Check initial state (timestamps must be uninitialized/null)
        $this->assertNull($review->getCreatedAt());
        $this->assertNull($review->getUpdatedAt());

        // --- ACT ---
        // Manually trigger the lifecycle callback method normally managed by Doctrine ORM
        $review->setCreatedAtValue();

        // --- ASSERT ---
        // Verify both timestamps are generated and are of correct instance type
        $this->assertNotNull($review->getCreatedAt());
        $this->assertNotNull($review->getUpdatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $review->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $review->getUpdatedAt());

        // Ensure that during initial creation, the created and updated timestamps are identical
        $this->assertSame(
            $review->getCreatedAt()->getTimestamp(), 
            $review->getUpdatedAt()->getTimestamp()
        );
    }

    /**
     * Test the PreUpdate lifecycle callback.
     * Verifies that only the updated_at field changes, while created_at remains untouched.
     */
    public function testPreUpdateChangesOnlyUpdatedAt(): void
    {
        // --- ARRANGE ---
        $review = new Review();
        
        // Mock a specific historical timestamp for both fields
        $pastDate = new \DateTimeImmutable('2026-01-01 10:00:00');
        $review->setCreatedAt($pastDate);
        $review->setUpdatedAt($pastDate);

        // --- ACT ---
        // Manually trigger the pre-update callback method
        $review->setUpdatedAtValue();

        // --- ASSERT ---
        // Verify that the creation date remains strictly unchanged
        $this->assertSame($pastDate, $review->getCreatedAt());

        // Verify that the modification date was successfully updated to a newer timestamp
        $this->assertNotSame($pastDate, $review->getUpdatedAt());
        $this->assertGreaterThan(
            $pastDate->getTimestamp(), 
            $review->getUpdatedAt()->getTimestamp()
        );
    }
}