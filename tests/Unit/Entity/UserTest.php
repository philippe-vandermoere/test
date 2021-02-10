<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

class UserTest extends TestCase
{
    public function testId(): void
    {
        $id = new UuidV4();
        $user = new User();
        $prop = new \ReflectionProperty($user, 'id');
        $prop->setAccessible(true);
        $prop->setValue($user, $id);

        static::assertSame($id, $user->getId());

        $user = new User();
        static::expectException(\LogicException::class);
        static::expectExceptionMessage('You must persist and flush entity before getting its id.');
        $user->getId();
    }

    public function testFirstname(): void
    {
        $firstname = \uniqid('fisrtname', true);

        $user = new User();
        static::assertSame($user, $user->setFirstname($firstname));
        static::assertSame($firstname, $user->getFirstname());
    }

    public function testLastname(): void
    {
        $lastname = \uniqid('lastname', true);

        $user = new User();
        static::assertSame($user, $user->setLastname($lastname));
        static::assertSame($lastname, $user->getLastname());
    }

    public function testJsonSerialize(): void
    {

        $id = new UuidV4();
        $firstname = \uniqid('fisrtname', true);
        $lastname = \uniqid('lastname', true);

        $user = (new User())
            ->setFirstname($firstname)
            ->setLastname($lastname)
        ;

        $prop = new \ReflectionProperty($user, 'id');
        $prop->setAccessible(true);
        $prop->setValue($user, $id);

        static::assertSame(
            [
                'id' => $id,
                'firstname' => $firstname,
                'lastname' => $lastname,
            ],
            $user->jsonSerialize(),
        );
    }
}
