<?php

namespace App\Service;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;

class CompanyReviewAiService
{
    public function __construct(
        private AgentInterface $agent,
    ) {
    }

    public function generateSummary(array $reviews): string
    {
        $messages = new MessageBag(
            Message::forSystem('You are a helpful assistant that summarizes company reviews and writes 5 pros and 5 cons based on the reviews. It has to be in Hungarian.'),
            Message::ofUser('Here are the reviews: '.json_encode($reviews))
        );

        $result = $this->agent->call($messages);

        return $result->getContent();
    }
}
