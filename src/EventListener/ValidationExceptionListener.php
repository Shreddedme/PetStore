<?php

namespace App\EventListener;

use App\Exception\EntityNotFoundException;
use App\Exception\ValidationException;
use App\Model\ErrorHandling\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\SerializerInterface;

class ValidationExceptionListener
{
    public function __construct(
        private SerializerInterface $serializer,
    )
    {}

    public const JSON = 'application/json';
    public function onKernelException(ExceptionEvent $event): void
    {
        $acceptHeader = $event->getRequest()->headers->get('Accept');
        $exception = $event->getThrowable();

        if ($acceptHeader !== self::JSON) {
            return;
        }

        if ($exception instanceof ValidationException) {
            $response = new JsonResponse($this->buildData($exception), Response::HTTP_BAD_REQUEST, [], true);
        } elseif ($exception instanceof EntityNotFoundException) {
            $response = new JsonResponse(['message' => $exception->getMessage()], Response::HTTP_NOT_FOUND, [], true);
        } else {
           return;
        }

        $event->stopPropagation();
        $event->setResponse($response);
    }

    public function buildData(ValidationException $exception): string
    {
       $errorResponse = new ErrorResponse();

       $errorResponse->addError($exception->getValidationErrors());

       return $this->serializer->serialize($errorResponse, 'json');
    }
}