<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\EventSubscriber;

use App\EventSubscriber\DoctrineValidator;
use App\Exception\BadParameterException;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrineValidatorTest extends TestCase
{
    private MockObject | ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
    }

    public function testGetSubscribedEvents(): void
    {
        static::assertSame(
            [
                Events::prePersist,
                Events::preUpdate,
            ],
            (new DoctrineValidator($this->validator))->getSubscribedEvents()
        );
    }

    public function testValidatePrePersist()
    {
        (new DoctrineValidator($this->validator))->prePersist($this->prepareValidate());
    }

    public function testValidateErrorPrePersist()
    {
        (new DoctrineValidator($this->validator))->prePersist($this->prepareValidateError());
    }

    public function testValidatePreUpdate()
    {
        (new DoctrineValidator($this->validator))->preUpdate($this->prepareValidate());
    }

    public function testValidateErrorPreUpdate()
    {
        (new DoctrineValidator($this->validator))->preUpdate($this->prepareValidateError());
    }

    protected function prepareValidate(): LifecycleEventArgs
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $object = new \stdClass();

        $event
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object)
        ;

        $this->validator
            ->expects(static::once())
            ->method('validate')
            ->with($object)
            ->willReturn(new ConstraintViolationList())
        ;

        return $event;
    }

    protected function prepareValidateError(): LifecycleEventArgs
    {
        $event = $this->createMock(LifecycleEventArgs::class);
        $object = new \stdClass();

        $expectedMessage = [];

        $constraintList = new ConstraintViolationList();
        for ($i = 0; $i <= \mt_rand(10, 20); $i++) {
            $message = \uniqid('message', true);
            $property = \uniqid('property', true);

            $constraint = $this->createMock(ConstraintViolationInterface::class);
            $constraint
                ->expects(static::once())
                ->method('getPropertyPath')
                ->willReturn($property)
            ;

            $constraint
                ->expects(static::once())
                ->method('getMessage')
                ->willReturn($message)
            ;

            $constraintList->add($constraint);
            $expectedMessage[] = $property . ': ' . $message;
        }

        $event
            ->expects(static::once())
            ->method('getObject')
            ->willReturn($object)
        ;

        $this->validator
            ->expects(static::once())
            ->method('validate')
            ->with($object)
            ->willReturn($constraintList)
        ;

        static::expectException(BadParameterException::class);
        static::expectExceptionMessage(\implode(PHP_EOL, $expectedMessage));

        return $event;
    }
}
