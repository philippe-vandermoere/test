<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\RequestJsonDecoder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestJsonDecoderTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            RequestJsonDecoder::getSubscribedEvents(),
            [
                KernelEvents::REQUEST => ['decodeRequest', 10],
            ]
        );
    }

    public function testDecodeJsonRequestContentTypeJson()
    {
        $requestEvent = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(ParameterBag::class);

        $data = ['test' => 'test'];

        $requestEvent
            ->expects(static::once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $request
            ->expects(static::once())
            ->method('getContentType')
            ->willReturn('json')
        ;

        $request
            ->expects(static::once())
            ->method('getContentType')
            ->willReturn('json')
        ;

        $request
            ->expects(static::once())
            ->method('toArray')
            ->willReturn($data)
        ;

        $request->request
            ->expects(static::once())
            ->method('replace')
            ->with($data)
        ;

        (new RequestJsonDecoder())->decodeRequest($requestEvent);
    }

    public function testDecodeJsonRequestNoContentTypeJson()
    {
        $requestEvent = $this->createMock(RequestEvent::class);
        $request = $this->createMock(Request::class);
        $request->request = $this->createMock(ParameterBag::class);

        $requestEvent
            ->expects(static::once())
            ->method('getRequest')
            ->willReturn($request)
        ;

        $request
            ->expects(static::once())
            ->method('getContentType')
            ->willReturn(\array_rand([null, '', 'html', 'xml'], 1))
        ;

        $request
            ->expects(static::never())
            ->method('toArray')
        ;

        $request->request
            ->expects(static::never())
            ->method('replace')
        ;

        (new RequestJsonDecoder())->decodeRequest($requestEvent);
    }
}
