<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Response data
        $responseData = [
            'status' => 'Error',
            'message' => $exception->getMessage(),
        ];

        // If HttpException
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        } else {
            $statusCode = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
        }

        $responseData['code'] = $statusCode;

        // JSON response
        $response = new JsonResponse($responseData, $statusCode);

        // Set response
        $event->setResponse($response);
    }
}
