<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\ResponseJsonEncoder;
use App\Exception\BadParameterException;
use App\Exception\EntityNotFound;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseJsonEncoderTest extends TestCase
{
    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            ResponseJsonEncoder::getSubscribedEvents(),
            [
                KernelEvents::VIEW => ['onView', 10],
                KernelEvents::EXCEPTION => ['onException', -127],
            ]
        );
    }

    /** @dataProvider dataView */
    public function testOnViewAcceptJson(mixed $controllerResult, string $httpMethod, int $httpCode): void
    {
        $request = $this->createMock(Request::class);
        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $controllerResult,
        );

        $request
            ->expects(static::once())
            ->method('getAcceptableContentTypes')
            ->willReturn(['application/json'])
        ;

        $request
            ->expects(static::once())
            ->method('getMethod')
            ->willReturn($httpMethod)
        ;

        (new ResponseJsonEncoder())->onView($event);

        static::assertEquals(
            new JsonResponse($controllerResult, $httpCode),
            $event->getResponse(),
        );
    }

    public function testOnViewNotAcceptJson(): void
    {
        $request = $this->createMock(Request::class);
        $event = new ViewEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            '',
        );

        $request
            ->expects(static::once())
            ->method('getAcceptableContentTypes')
            ->willReturn([])
        ;

        (new ResponseJsonEncoder())->onView($event);

        static::assertSame(
            null,
            $event->getResponse(),
        );
    }

    /** @dataProvider dataException */
    public function testOnExceptionAcceptJson(\Throwable $throwable, int $httpCode): void
    {
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            $throwable,
        );

        $request
            ->expects(static::once())
            ->method('getAcceptableContentTypes')
            ->willReturn(['application/json'])
        ;

        (new ResponseJsonEncoder())->onException($event);

        static::assertEquals(
            new JsonResponse(
                [
                    'error' => [
                        'code' => $throwable->getCode(),
                        'message' => $throwable->getMessage(),
                    ],
                ],
                $httpCode,
            ),
            $event->getResponse(),
        );
    }

    public function testOnExceptionNotAcceptJson(): void
    {
        $request = $this->createMock(Request::class);
        $event = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MASTER_REQUEST,
            new \Exception(),
        );

        $request
            ->expects(static::once())
            ->method('getAcceptableContentTypes')
            ->willReturn([])
        ;

        (new ResponseJsonEncoder())->onException($event);

        static::assertSame(
            null,
            $event->getResponse()
        );
    }

    public function dataView(): array
    {
        return [
            [['test' => 'test'], Request::METHOD_GET, 200],
            [['test' => 'test'], Request::METHOD_POST, 201],
            [['test' => 'test'], Request::METHOD_DELETE, 204],
            [['test' => 'test'], Request::METHOD_PATCH, 200],
            [['test' => 'test'], Request::METHOD_PUT, 200],
            [['test' => 'test'], Request::METHOD_HEAD, 200],
            [['test' => 'test'], Request::METHOD_OPTIONS, 200],
            [['test' => 'test'], Request::METHOD_CONNECT, 200],
            [['test' => 'test'], Request::METHOD_PURGE, 200],
            [['test' => 'test'], Request::METHOD_TRACE, 200],
            [new \stdClass(), Request::METHOD_GET, 200],
            [[new \stdClass(), new \stdClass(), new \stdClass()], Request::METHOD_GET, 200],
        ];
    }

    public function dataException(): array
    {
        return [
            [new \Exception(\uniqid('error', true), \mt_rand(0, 255)), 500],
            [new \LogicException(\uniqid('error', true), \mt_rand(0, 255)), 500],
            [new EntityNotFound(\uniqid('error', true), \mt_rand(0, 255)), 404],
            [new BadParameterException(\uniqid('error', true), \mt_rand(0, 255)), 400],
            [new NotFoundHttpException(), 404],
            [new ServiceUnavailableHttpException(), 503],
        ];
    }
}
