<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\UserUpdateController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Unit\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class UserUpdateControllerTest extends TestCase
{
    private MockObject | UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    /** @dataProvider data */
    public function testInvoke(?string $firstname, ?string $lastname): void
    {
        $id = new UuidV4();
        $currentFirstname = \uniqid('firstname', true);
        $currentLastname = \uniqid('lastname', true);

        $currentUser = (new User())
            ->setFirstname($currentFirstname)
            ->setLastname($currentLastname)
        ;

        $prop = new \ReflectionProperty($currentUser, 'id');
        $prop->setAccessible(true);
        $prop->setValue($currentUser, $id);

        $request = $this->createMock(Request::class);
        $request->request = new ParameterBag(
            [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        );

        $this->userRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->willReturn($currentUser)
        ;

        $user = clone $currentUser;
        if (null !== $firstname) {
            $user->setFirstname($firstname);
        }

        if (null !== $lastname) {
            $user->setLastname($lastname);
        }

        $this->userRepository
            ->expects(static::once())
            ->method('save')
            ->with($user)
        ;

        static::assertEquals(
            $user,
            (new UserUpdateController($this->userRepository))($request, (string) $id),
        );
    }

    public function data(): array
    {
        return [
            [null, null],
            [null, \uniqid('lastname', true)],
            [\uniqid('firstname', true), null],
            [uniqid('firstname', true), \uniqid('lastname', true)],
        ];
    }
}
