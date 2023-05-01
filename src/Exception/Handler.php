<?php

declare(strict_types=1);

namespace App\Exception;

use App\Module\CurrencyConverter\Exception\UnknownCurrencyException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\Exception\ValidationFailedException;


class Handler
{
    /**
     * @param ExceptionEvent $event
     * @return void
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $response = new JsonResponse();
        $response->headers->set('Content-Type', 'application/json');

        if ($exception instanceof ValidationFailedException) {
            $violations = $exception->getViolations();
            $errors = [];

            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            $response = new JsonResponse([
                'message' => 'Validation Failed',
                'details' => $errors
            ], Response::HTTP_BAD_REQUEST);
        } elseif ($exception instanceof UnknownCurrencyException) {
            $response->setStatusCode($exception->getCode());
            $response->setData([
                'message' => $exception->getMessage()
            ]);
        } else {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
            $response->setData([
                'message' => $exception->getMessage(),
                'stack' => $exception->getTraceAsString()
            ]);
        }

        $event->setResponse($response);
    }
}