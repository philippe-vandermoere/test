<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Tests\Unit\Controller;

use App\Controller\UserListController;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Unit\Assert;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Routing\Annotation\Route;

class UserListControllerTest extends TestCase
{
    private MockObject | UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
    }

    public function testInvoke(): void
    {
        $users = [];
        for ($i = 0; $i <= 10; $i++) {
            $users[] = new User();
        }

        $this->userRepository
            ->expects(static::once())
            ->method('findAll')
            ->willReturn($users)
        ;

        static::assertEquals(
            $users,
            (new UserListController($this->userRepository))()
        );
    }
}
