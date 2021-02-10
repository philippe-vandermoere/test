<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\BadParameterException;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DoctrineValidator implements EventSubscriberInterface
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    /** @inheritDoc */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $event): void
    {
        $this->validate($event->getObject());
    }

    public function preUpdate(LifecycleEventArgs $event): void
    {
        $this->validate($event->getObject());
    }

    protected function validate(object $object): void
    {
        $violations = $this->validator->validate($object);
        if (0 === \count($violations)) {
            return ;
        }

        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($violations as $violation) {
            $errors[] = \sprintf(
                '%s: %s',
                $violation->getPropertyPath(),
                $violation->getMessage(),
            );
        }

        throw new BadParameterException(\implode(PHP_EOL, $errors));
    }
}
