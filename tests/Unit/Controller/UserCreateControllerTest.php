<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\UserCreateController;
use App\Entity\User;
use App\Exception\BadParameterException;
use App\Repository\UserRepository;
use App\Tests\Unit\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserCreateControllerTest extends TestCase
{

    private MockObject | UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testInvoke(): void
    {
        $firstname = \uniqid('firstname', true);
        $lastname = \uniqid('lastname', true);

        $request = $this->createMock(Request::class);
        $request->request = new ParameterBag(
            [
                'firstname' => $firstname,
                'lastname' => $lastname,
            ]
        );

        $this->userRepository
            ->expects(static::once())
            ->method('save')
        ;

        $user = (new User())
            ->setFirstname($firstname)
            ->setLastname($lastname)
        ;

        static::assertEquals(
            $user,
            (new UserCreateController($this->userRepository))($request)
        );
    }
}
