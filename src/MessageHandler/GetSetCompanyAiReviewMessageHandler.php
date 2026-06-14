<?php

namespace App\MessageHandler;

use App\Message\GetSetCompanyAiReviewMessage;
use App\Repository\CompanyRepository;
use App\Service\CompanyReviewAiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Message handler that processes the asynchronous AI review summary generation.
 * Triggered automatically by the Symfony Scheduler / Messenger components.
 */
#[AsMessageHandler]
final class GetSetCompanyAiReviewMessageHandler
{
    /**
     * Injecting necessary dependencies via constructor promotion.
     */
    public function __construct(
        private CompanyReviewAiService $aiService,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em,
    ) {
    }

    /**
     * Execution point invoked when a GetSetCompanyAiReviewMessage is consumed from the queue.
     * Evaluates company reviews and updates AI summaries if change is detected.
     */
    public function __invoke(GetSetCompanyAiReviewMessage $message): void
    {
        // Fetch all companies eager-loading their reviews to avoid N+1 query issues
        $companies = $this->companyRepository->findAllWithReviews();

        foreach ($companies as $company) {
            // Guard clause to handle potentially null entities safely
            if (!$company) {
                continue;
            }

            $reviews = $company->getReviews()->toArray();
            $newCount = count($reviews);
            $oldCount = $company->getOldReviewCount();

            // Synchronize the current actual review count state
            $company->setNewReviewCount($newCount);

            // Optimization: Skip expensive external AI API calls if no new reviews have been submitted since the last execution
            if ($newCount === $oldCount) {
                continue;
            }

            // Update the baseline tracking counter for future comparisons
            $company->setOldReviewCount($newCount);

            // Extract only the text payloads from the review entities for the AI prompt context
            $reviewsData = array_map(function ($review) {
                return $review->getReviewText();
            }, $reviews);

            // Call the isolated AI wrapper service to generate a concise summary
            $summary = $this->aiService->generateSummary($reviewsData);

            // Persist the generated summary string into the database
            $company->setReviewSummary($summary);
        }

        // Execute a single transaction flush for performance and batch processing efficiency
        $this->em->flush();
    }
}
