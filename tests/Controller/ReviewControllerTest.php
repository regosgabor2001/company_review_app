<?php

namespace App\Tests\Controller;

use App\Entity\Company;
use App\Entity\Review;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Functional tests for the Review creation and validation workflows.
 */
class ReviewControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    /**
     * Set up the test environment before each test execution.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Initialize the HTTP client to simulate browser requests
        $this->client = static::createClient();

        // Retrieve the entity manager from the dependency injection container
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * Clean up database connections after each test to prevent memory leaks.
     */
    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
        }
        parent::tearDown();
    }

    /**
     * Test successful review submission with valid form data.
     */
    public function testCreateReviewSuccessfully(): void
    {
        // --- ARRANGE ---
        // Create and persist a dummy company to assign the review to
        $company = new Company();
        $company->setName('Teszt Cég Kft.');
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        // --- ACT ---
        // Navigate to the review creation page
        $crawler = $this->client->request('GET', '/new');
        $this->assertResponseIsSuccessful();

        // Fill out the form with valid data
        $form = $crawler->selectButton('Mentés')->form([
            'review[company]' => $company->getId(),
            'review[author_email]' => 'user@example.com',
            'review[rating]' => 5,
            'review[review_text]' => 'Ez egy fantasztikus cég, minden szuper volt!',
        ]);

        // Submit the form
        $this->client->submit($form);

        // --- ASSERT ---
        // Verify the application issues a 303 See Other redirect to the homepage
        $this->assertResponseRedirects('/', 303);

        // Fetch the record directly from the database to ensure it was properly persisted
        $reviewRepository = $this->entityManager->getRepository(Review::class);
        $savedReview = $reviewRepository->findOneBy(['author_email' => 'user@example.com']);

        // Assert database values match the submitted form data
        $this->assertNotNull($savedReview);
        $this->assertSame(5, $savedReview->getRating());
        $this->assertSame('Ez egy fantasztikus cég, minden szuper volt!', $savedReview->getReviewText());
    }

    /**
     * Test form validation constraints when invalid data is provided.
     */
    public function testCreateReviewValidationError(): void
    {
        // --- ARRANGE ---
        // Create and persist a dummy company for the test case
        $company = new Company();
        $company->setName('Validáló Teszt Cég');
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        // --- ACT ---
        // Navigate to the review creation page
        $crawler = $this->client->request('GET', '/new');
        $this->assertResponseIsSuccessful();

        // Select the form and fill it with intentionally invalid values to trigger constraint violations
        $form = $crawler->selectButton('Mentés')->form();
        $form['review[company]']->select($company->getId());
        $form['review[rating]'] = 10;                  // Invalid: Maximum allowed rating is 5
        $form['review[author_email]'] = 'nem-egy-email'; // Invalid: Missing standard email format
        $form['review[review_text]'] = 'Abc';

        // Submit the invalid form
        $crawler = $this->client->submit($form);

        // --- ASSERT ---
        // Verify the response returns HTTP 422 Unprocessable Entity (standard form validation failure status)
        $this->assertResponseStatusCodeSame(422);

        // Check that the standard Symfony validation error message is rendered on the page
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('This value is not a valid email address.', $responseContent);
    }
}
