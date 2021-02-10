<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Exception;

use App\Exception\BadParameterException;
use PHPUnit\Framework\TestCase;

class BadParameterExceptionTest extends TestCase
{
    public function testConstruct(): void
    {
        $message = \uniqid('message', true);
        $code = \mt_rand(0, 255);
        $previous = new \Exception();
        $exception = new BadParameterException($message, $code, $previous);

        static::assertSame($message, $exception->getMessage());
        static::assertSame($code, $exception->getCode());
        static::assertSame($previous, $exception->getPrevious());

        $exception = new BadParameterException($message);

        static::assertSame($message, $exception->getMessage());
        static::assertSame(0, $exception->getCode());
        static::assertSame(null, $exception->getPrevious());
    }
}
