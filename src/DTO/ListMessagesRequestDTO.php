<?php

declare(strict_types=1);

namespace App\DTO;

use App\Entity\Message;
use Symfony\Component\Validator\Constraints as Assert;

readonly class ListMessagesRequestDTO
{
    public function __construct(
        #[Assert\Choice(choices: [Message::STATUS_READ, Message::STATUS_SENT], message: 'Choose a valid status [sent, read]!')]
        public ?string $status = null,
    ) {
    }

}
