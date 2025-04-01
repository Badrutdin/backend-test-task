<?php

namespace App\EventListener;

use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Throwable;

#[AsEventListener]
class ExceptionListener
{
    private string|array $message = 'Something went wrong';
    private int $statusCode = 500;
    private Throwable $exception;

    public function __construct(private LoggerInterface $logger)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $this->exception = $event->getThrowable();
        $this->logError($this->exception);
        if ($this->exception instanceof HttpExceptionInterface || $this->exception instanceof InvalidArgumentException) {


            $this->message = $this->exception->getMessage();
            $this->statusCode = $this->exception instanceof HttpExceptionInterface ? $this->exception->getStatusCode() :
                ($this->exception->getCode() ?: $this->statusCode);;

        }
        $this->checkValidationFailedException();
        $response = new JsonResponse(['error' => $this->message], $this->statusCode);
        $event->setResponse($response);
    }

    private function logError(Throwable $exception): void
    {
        $message = $this->getExceptionMessage($exception);
        $this->logger->error($message);
    }

    private function getExceptionMessage(Throwable $exception): string
    {
        $message = sprintf(
            "[%s] %s (Code: %d)\nStack Trace:\n%s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getTraceAsString()
        );

        if ($previous = $exception->getPrevious()) {
            $message .= "\nPrevious: ".$this->getExceptionMessage($previous);
        }

        return $message;
    }

    private function checkValidationFailedException(): void
    {
        $exception = $this->exception instanceof ValidationFailedException
            ? $this->exception
            : ($this->exception->getPrevious() instanceof ValidationFailedException
                ? $this->exception->getPrevious()
                : null);

        if ($exception === null) {
            return;
        }
        $violations = $exception->getViolations();
        foreach ($violations as $violation) {
            $errorMessages[$violation->getPropertyPath()] = $violation->getMessage().' Received: '
                .($violation->getInvalidValue() ? $violation->getInvalidValue() : 'NULL');
        }
        if (isset($errorMessages)) {
            $this->message = $errorMessages;
            $this->statusCode = 400;
        }
    }

}