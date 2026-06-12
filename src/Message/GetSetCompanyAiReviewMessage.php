<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

final class GetSetCompanyAiReviewMessage
{
     public function __construct(
        public readonly array $companies
    ) {}
}
