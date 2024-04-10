<?php

declare(strict_types=1);

namespace Repository;

use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MessageRepositoryTest extends KernelTestCase
{
    public function test_it_has_connection(): void
    {
        self::bootKernel();

        $messages = self::getContainer()->get(MessageRepository::class);

        // php stan is not able to detect symfony repositories, could maybe be fixed with PHPStan’s Doctrine extension
        /** @phpstan-ignore-next-line */
        $this->assertSame([], $messages->findAll());
    }
}
