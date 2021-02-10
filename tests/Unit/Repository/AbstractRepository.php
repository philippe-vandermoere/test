<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

namespace App\Tests\Unit\Repository;

use App\Exception\EntityNotFound;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV4;

abstract class AbstractRepository extends TestCase
{
    private MockObject | ManagerRegistry $managerRegistry;
    private MockObject | EntityManagerInterface $manager;
    private MockObject | ClassMetadata $classMetadata;

    abstract protected function getRepositoryClassName(): string;
    abstract protected function getEntityClassName(): string;

    protected function setUp(): void
    {
        $this->managerRegistry = $this->createMock(ManagerRegistry::class);
        $this->manager = $this->createMock(EntityManagerInterface::class);
        $this->classMetadata = $this->createMock(ClassMetadata::class);
        $this->classMetadata->name = $this->getEntityClassName();

        $this->managerRegistry
            ->expects(static::once())
            ->method('getManagerForClass')
            ->with($this->getEntityClassName())
            ->willReturn($this->manager)
        ;

        $this->manager
            ->expects(static::once())
            ->method('getClassMetadata')
            ->with($this->getEntityClassName())
            ->willReturn($this->classMetadata)
        ;
    }

    public function testConstruct(): void
    {
        $repositoryClass = $this->getRepositoryClassName();

        static::assertSame(
            $this->getEntityClassName(),
            (new $repositoryClass($this->managerRegistry))->getClassName(),
        );
    }

    public function testFind(): void
    {
        $id = new UuidV4();
        $repositoryClass = $this->getRepositoryClassName();
        $entity = $this->createMock($this->getEntityClassName());

        $this->manager
            ->expects(static::once())
            ->method('find')
            ->with($this->getEntityClassName(), $id)
            ->willReturn($entity)
        ;

        static::assertSame(
            $entity,
            (new $repositoryClass($this->managerRegistry))->find($id),
        );
    }

    public function testFindNotFound(): void
    {
        $id = new UuidV4();
        $repositoryClass = $this->getRepositoryClassName();

        $this->manager
            ->expects(static::once())
            ->method('find')
            ->with($this->getEntityClassName(), $id)
            ->willReturn(null)
        ;

        static::expectException(EntityNotFound::class);
        static::expectExceptionMessage(
            \sprintf(
                'Unable to find %s.',
                \strtolower(\substr(\strrchr($this->getEntityClassName(), '\\'), 1))
            )
        );

        (new $repositoryClass($this->managerRegistry))->find($id);
    }

    public function testFindOneBy(): void
    {
        $repositoryClass = $this->getRepositoryClassName();
        $entity = $this->createMock($this->getEntityClassName());
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $entityPersister = $this->createMock(EntityPersister::class);

        $this->manager
            ->expects(static::once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork)
        ;

        $unitOfWork
            ->expects(static::once())
            ->method('getEntityPersister')
            ->with($this->getEntityClassName())
            ->willReturn($entityPersister)
        ;

        $entityPersister
            ->expects(static::once())
            ->method('load')
            ->willReturn($entity)
        ;

        static::assertSame(
            $entity,
            (new $repositoryClass($this->managerRegistry))->findOneBy(['test' => 'test']),
        );
    }

    public function testFindOneByNotFound(): void
    {
        $repositoryClass = $this->getRepositoryClassName();
        $unitOfWork = $this->createMock(UnitOfWork::class);
        $entityPersister = $this->createMock(EntityPersister::class);

        $this->manager
            ->expects(static::once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork)
        ;

        $unitOfWork
            ->expects(static::once())
            ->method('getEntityPersister')
            ->with($this->getEntityClassName())
            ->willReturn($entityPersister)
        ;

        $entityPersister
            ->expects(static::once())
            ->method('load')
            ->willReturn(null)
        ;

        static::expectException(EntityNotFound::class);
        static::expectExceptionMessage(
            \sprintf(
                'Unable to find %s.',
                \strtolower(\substr(\strrchr($this->getEntityClassName(), '\\'), 1))
            )
        );

        (new $repositoryClass($this->managerRegistry))->findOneBy(['test' => 'test']);
    }

    public function testSave(): void
    {
        $repositoryClass = $this->getRepositoryClassName();
        $entity = $this->createMock($this->getEntityClassName());

        $this->manager
            ->expects(static::once())
            ->method('persist')
            ->with($entity)
        ;

        $this->manager
            ->expects(static::once())
            ->method('flush')
        ;

        (new $repositoryClass($this->managerRegistry))->save($entity);
    }

    public function testDelete(): void
    {
        $repositoryClass = $this->getRepositoryClassName();
        $entity = $this->createMock($this->getEntityClassName());

        $this->manager
            ->expects(static::once())
            ->method('remove')
            ->with($entity)
        ;

        $this->manager
            ->expects(static::once())
            ->method('flush')
        ;

        (new $repositoryClass($this->managerRegistry))->delete($entity);
    }
}
