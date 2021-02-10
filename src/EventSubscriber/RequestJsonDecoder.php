<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestJsonDecoder implements EventSubscriberInterface
{
    /** @inheritdoc */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['decodeRequest', 10],
        ];
    }

    public function decodeRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if ('json' === $request->getContentType()) {
            $request->request->replace($request->toArray());
        }
    }
}
