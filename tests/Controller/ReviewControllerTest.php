<?php

namespace App\Tests\Controller;

use App\Entity\Company;
use App\Entity\Review;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\EntityManagerInterface;

class ReviewControllerTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        
        $this->entityManager = static::getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        if ($this->entityManager) {
            $this->entityManager->close();
        }
        parent::tearDown();
    }

    public function testCreateReviewSuccessfully(): void
    {
        $company = new Company();
        $company->setName('Teszt Cég Kft.');
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Mentés')->form([
            'review[company]' => $company->getId(),
            'review[author_email]' => 'user@example.com',
            'review[rating]' => 5,
            'review[review_text]' => 'Ez egy fantasztikus cég, minden szuper volt!',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/', 303);

        $reviewRepository = $this->entityManager->getRepository(Review::class);
        $savedReview = $reviewRepository->findOneBy(['author_email' => 'user@example.com']);

        $this->assertNotNull($savedReview);
        $this->assertSame(5, $savedReview->getRating());
        $this->assertSame('Ez egy fantasztikus cég, minden szuper volt!', $savedReview->getReviewText());
    }

    public function testCreateReviewValidationError(): void
    {
        $company = new Company();
        $company->setName('Validáló Teszt Cég');
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', '/new');
        $this->assertResponseIsSuccessful();

        $form = $crawler->selectButton('Mentés')->form();

        $form['review[company]']->select($company->getId());
        $form['review[rating]'] = 10;
        $form['review[author_email]'] = 'nem-egy-email';
        $form['review[review_text]'] = 'Abc';

        $crawler = $this->client->submit($form);

        $this->assertResponseStatusCodeSame(422);
        
        $responseContent = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('This value is not a valid email address.', $responseContent);
    }
}