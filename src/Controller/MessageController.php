<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\ListMessagesRequestDTO;
use App\DTO\SendMessagesRequestDTO;
use App\Message\SendMessage;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */

    // Could also extract bussines logic to service (MessageService for getting and sending) but since it is simple  we can leave it here.

    #[Route('/messages')]
    public function list(#[MapQueryString] ?ListMessagesRequestDTO $listRequest, MessageRepository $messages, SerializerInterface $serializer): JsonResponse
    {

        // Initial code lacks validation - now validation iz done by  requestDTO which is automaticaly created from request.
        // query in repository class is not needed since we have same functionality with oneliner

        $messages = $listRequest ? $messages->findBy(['status' => $listRequest->status]) : $messages->findAll();

        //  No need to make our own array since symfony has powerfull serializer and we can define in enitity
        //  which porperties will be visible in response via groups
        $data = ['messages' => $messages];
        $data = $serializer->serialize($data, 'json', ['groups' => ['list']]);

        // We cen directly return JsonResponse
        return new JsonResponse($data, 200, [], true);

    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(#[MapQueryString] SendMessagesRequestDTO $sendRequest, MessageBusInterface $bus): Response
    {
        // Validation is extracted from contoller and moved to requestDTO
        $bus->dispatch(new SendMessage($sendRequest->text));

        return new Response('Successfully sent', 204);
    }
}
