<?php

namespace App\MessageHandler;

use App\Message\GetSetCompanyAiReviewMessage;
use App\Service\CompanyReviewAiService;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetSetCompanyAiReviewMessageHandler
{
    public function __construct(
        private CompanyReviewAiService $aiService,
        private CompanyRepository $companyRepository,
        private EntityManagerInterface $em
    ) {}

    public function __invoke(GetSetCompanyAiReviewMessage $message): void
    {
        $companies = $this->companyRepository->findAllWithReviews();

        foreach ($companies as $company) {
            if (!$company) {
                continue;
            }

            $reviews = $company->getReviews()->toArray();
            $newCount = count($reviews);
            $oldCount = $company->getOldReviewCount();

            $company->setNewReviewCount($newCount);

            if ($newCount === $oldCount) {
                continue;
            }

            $company->setOldReviewCount($newCount);

            $reviewsData = array_map(function($review) {
                return $review->getReviewText();
            }, $reviews);

            $summary = $this->aiService->generateSummary($reviewsData);

            $company->setReviewSummary($summary);
        }

        $this->em->flush();
    }
}
