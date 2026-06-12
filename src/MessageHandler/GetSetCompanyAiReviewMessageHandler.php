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

            $summary = $this->aiService->generateSummary($reviews);

            $company->setReviewSummary($summary);
        }

        $this->em->flush();
    }
}
