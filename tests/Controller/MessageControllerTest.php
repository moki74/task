<?php

declare(strict_types=1);

namespace Controller;

use App\Entity\Message;
use App\Message\SendMessage;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;

    private EntityManagerInterface $em;
    private KernelBrowser $client;


    //  Better solution would be to use dama/doctrine-test-bundle, but for the sake of simplicity I will use
    //  the ORM purger for cleaning DB after tests
    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        /** @phpstan-ignore-next-line */
        $this->em = $this->client->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function test_list(): void
    {
        $this->populateMessages();

        $this->client->request('GET', '/messages');

        $this->assertResponseIsSuccessful();

        $response = $this->client->getResponse();

        $responseData = json_decode(strval($response->getContent()), true);

        $result = is_array($responseData) ? $responseData['messages'] : [];

        $this->assertCount(3, $result);
    }

    public function test_list_status_read(): void
    {
        $this->populateMessages();

        $this->client->request('GET', '/messages?status=read');

        $this->assertResponseIsSuccessful();

        $response = $this->client->getResponse();

        $responseData = json_decode(strval($response->getContent()), true);

        $result = is_array($responseData) ? $responseData['messages'] : [];

        $this->assertCount(1, $result);
    }

    public function test_list_status_sent(): void
    {
        $this->populateMessages();

        $this->client->request('GET', '/messages?status=sent');

        $this->assertResponseIsSuccessful();

        $response = $this->client->getResponse();

        $responseData = json_decode(strval($response->getContent()), true);

        $result = is_array($responseData) ? $responseData['messages'] : [];

        $this->assertCount(2, $result);
    }

    public function test_list_wrong_status(): void
    {
        $this->populateMessages();

        $this->client->request('GET', '/messages?status=dummy');

        $this->assertPageTitleContains('Choose a valid status [sent, read]!');

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_that_it_sends_a_message(): void
    {
        $this->client->request('GET', '/messages/send', [
            'text' => 'Hello World',
        ]);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }

    private function populateMessages(): void
    {
        // Create 3 messages - 2 sent and 1 read
        $message1 = (new Message())
            ->setUuid('121')
            ->setText('Hello World1 - sent')
            ->setStatus('sent');

        $message2 = (new Message())
            ->setUuid('122')
            ->setText('Hello World2 - sent')
            ->setStatus('sent');

        $message3 = (new Message())
            ->setUuid('123')
            ->setText('Hello World3 - read')
            ->setStatus('read');


        $this->em->persist($message1);
        $this->em->flush();
        $this->em->persist($message2);
        $this->em->flush();
        $this->em->persist($message3);
        $this->em->flush();
    }

    protected function tearDown(): void
    {
        $purger = new ORMPurger($this->em);
        $purger->purge();
    }
}
