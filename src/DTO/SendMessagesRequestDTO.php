<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class SendMessagesRequestDTO
{
    public function __construct(
        #[Assert\NotBlank]
        public string $text
    ) {
    }

}
