<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserCreateController
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    #[Route(name: 'user_create', path: '/users', methods: ['POST'])]
    public function __invoke(Request $request): User
    {
        $user = new User();
        foreach (['firstname', 'lastname'] as $parameter) {
            $prop = new \ReflectionProperty($user, $parameter);
            $prop->setAccessible(true);
            $prop->setValue($user, $request->request->get($parameter));
        }

        $this->userRepository->save($user);

        return $user;
    }
}
