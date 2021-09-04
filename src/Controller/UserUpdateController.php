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

class UserUpdateController
{
    public function __construct(private UserRepository $userRepository)
    {
        if (true === true) {
            $a = 1;
        }
    }

    #[Route(name: 'user_update', path: '/users/{id}', methods: ['PATCH'])]
    public function __invoke(Request $request, string $id): User
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);

        foreach (['firstname', 'lastname'] as $parameter) {
            $value = $request->request->get($parameter);
            if (null !== $value) {
                $prop = new \ReflectionProperty($user, $parameter);
                $prop->setAccessible(true);
                $prop->setValue($user, $value);
            }
        }

        $this->userRepository->save($user);

        return $user;
    }
}
