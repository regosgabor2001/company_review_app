<?php

namespace App\Scheduler;

use App\Service\CompanyReviewAiService;
use App\Repository\CompanyRepository;
use App\Message\GetSetCompanyAiReviewMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('ai_company_summary')]
final class CompanyAiReviewSchedule implements ScheduleProviderInterface
{
    public function __construct(
        private CacheInterface $cache
    ) {}

    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('1 minute', new GetSetCompanyAiReviewMessage())
            )
            ->stateful($this->cache);
    }
}
