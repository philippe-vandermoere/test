<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\BadParameterException;
use App\Exception\EntityNotFound;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseJsonEncoder implements EventSubscriberInterface
{
    /** @inheritdoc */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onView', 10],
            KernelEvents::EXCEPTION => ['onException', -127],
        ];
    }

    public function onView(ViewEvent $event): void
    {
        $request = $event->getRequest();

        if (false === $this->isAcceptJson($request)) {
            return ;
        }

        $event->setResponse(
            new JsonResponse(
                $event->getControllerResult(),
                match ($request->getMethod()) {
                    Request::METHOD_POST => 201,
                    Request::METHOD_DELETE => 204,
                    default => 200,
                },
            )
        );
    }

    public function onException(ExceptionEvent $event): void
    {
        if (false === $this->isAcceptJson($event->getRequest())) {
            return ;
        }

        $throwable = $event->getThrowable();

        $event->setResponse(
            new JsonResponse(
                [
                    'error' => [
                        'code' => $throwable->getCode(),
                        'message' => $throwable->getMessage(),
                    ],
                ],
                $this->getStatusCode($throwable)
            )
        );
    }

    protected function isAcceptJson(Request $request): bool
    {
        foreach ($request->getAcceptableContentTypes() as $contentType) {
            if ('application/json' === $contentType) {
                return true;
            }
        }

        return false;
    }

    protected function getStatusCode(\Throwable $throwable): int
    {
        if ($throwable instanceof HttpException) {
            return $throwable->getStatusCode();
        }

        return match ($throwable::class) {
            BadParameterException::class => 400,
            EntityNotFound::class => 404,
            default => 500,
        };
    }
}
