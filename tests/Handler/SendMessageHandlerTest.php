<?php

declare(strict_types=1);

namespace App\Tests\Handler;

use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class SendMessageHandlerTest extends KernelTestCase
{
    use InteractsWithMessenger;

    public function test_invoke(): void
    {
        self::bootKernel();

        $bus = $this->getContainer()->get(MessageBusInterface::class);

        /** @phpstan-ignore-next-line */
        $bus->dispatch(new SendMessage('Hello world test!'));

        //this sohuld fire the handler
        $this->transport('sync')->processOrFail();


        //check if message is saved in handler invoke method
        /** @phpstan-ignore-next-line */
        $message = $this->getContainer()->get(MessageRepository::class)->findOneBy(['text' => 'Hello world test!']);
        $this->assertNotNull($message);
        $this->assertSame('sent', $message->getStatus());

        /** @phpstan-ignore-next-line */
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->remove($message);
        $em->flush();

    }
}
