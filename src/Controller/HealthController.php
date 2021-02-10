<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Routing\Annotation\Route;

class HealthController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route(name: 'health', path: '/health', methods: ['GET'])]
    public function __invoke(): Response
    {
        try {
            $this->entityManager->getConnection()->executeQuery(
                $this->entityManager->getConnection()->getDatabasePlatform()->getDummySelectSQL()
            );
        } catch (\Throwable) {
            throw new ServiceUnavailableHttpException(10, 'Service Unavailable');
        }

        return new Response('ok');
    }
}
