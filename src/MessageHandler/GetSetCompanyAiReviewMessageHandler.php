<?php

namespace App\MessageHandler;

use App\Message\GetSetCompanyAiReviewMessage;
use App\Service\CompanyReviewAiService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSetCompanyAiReviewMessageHandler
{
    public function __construct(
        private CompanyReviewAiService $aiService
    ) {}

    public function __invoke(GetSetCompanyAiReviewMessage $message): void
    {
        $companies = $message->companies;

        foreach ($companies as $company) {
            if (!$company) {
                continue;
            }

            $reviews = $company->getReviews()->toArray();

            $summary = $this->aiService->generateSummary($reviews);

            $company->setAiSummary($summary);
        }
    }
}
