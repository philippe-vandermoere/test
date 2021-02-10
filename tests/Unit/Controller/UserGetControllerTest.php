<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\UserGetController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Unit\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\UuidV4;

class UserGetControllerTest extends TestCase
{
    private MockObject | UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testInvoke(): void
    {
        $id = new UuidV4();
        $user = new User();

        $this->userRepository
            ->expects(static::once())
            ->method('findOneBy')
            ->with(['id' => $id])
            ->willReturn($user)
        ;

        static::assertEquals(
            $user,
            (new UserGetController($this->userRepository))((string) $id)
        );
    }
}