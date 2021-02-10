<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\HealthController;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class HealthControllerTest extends TestCase
{
    private EntityManagerInterface | MockObject $entityManager;

    public function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
    }

    public function testInvoke(): void
    {
        $this->prepareTestInvoke();

        static::assertEquals(
            new Response('ok'),
            (new HealthController($this->entityManager))()
        );
    }

    public function testInvokeError(): void
    {
        $this->prepareTestInvoke(new \Exception());

        static::expectException(ServiceUnavailableHttpException::class);

        try {
            (new HealthController($this->entityManager))();
        } catch (ServiceUnavailableHttpException $exception) {
            static::assertEquals(
                new ServiceUnavailableHttpException(10, 'Service Unavailable'),
                $exception
            );

            throw $exception;
        }
    }

    protected function prepareTestInvoke(\Throwable $errorOnExecuteQuery = null): ResultStatement
    {
        $connection = $this->createMock(Connection::class);
        $resultStatement = $this->createMock(ResultStatement::class);
        $databasePlatform = $this->createMock(AbstractPlatform::class);
        $query = 'SELECT 1';

        $this->entityManager
            ->expects(static::exactly(2))
            ->method('getConnection')
            ->willReturn($connection)
        ;

        $connection
            ->expects(static::once())
            ->method('getDatabasePlatform')
            ->willReturn($databasePlatform)
        ;

        if (true === ($errorOnExecuteQuery instanceof \Throwable)) {
            $connection
                ->expects(static::once())
                ->method('executeQuery')
                ->with($query)
                ->willThrowException(new \Exception())
            ;
        } else {
            $connection
                ->expects(static::once())
                ->method('executeQuery')
                ->with($query)
                ->willReturn($resultStatement)
            ;
        }

        $databasePlatform
            ->expects(static::once())
            ->method('getDummySelectSQL')
            ->willReturn($query)
        ;

        return $resultStatement;
    }
}
