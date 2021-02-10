<?php

/**
 * @author      Philippe Vandermoere <vandermoere.philippe@gmail.com>
 * @copyright   (c) Philippe Vandermoere
 * @license     MIT
 */

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UuidV4Generator;
use Symfony\Component\Uid\UuidV4;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 */
class User implements \JsonSerializable
{
    /**
     * @var ?UuidV4
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidV4Generator::class)
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=250, nullable=false)
     */
    #[Assert\Type ('string')]
    #[Assert\NotBlank ()]
    #[Assert\Length (min: 4, max: 250)]
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=250, nullable=false)
     */
    #[Assert\Type('string')]
    #[Assert\NotBlank()]
    #[Assert\Length(min: 4, max: 250)]
    private $firstname;

    public function getId(): UuidV4
    {
        if (false === ($this->id instanceof UuidV4)) {
            throw new \LogicException('You must persist and flush entity before getting its id.');
        }

        return $this->id;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): User
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): User
    {
        $this->firstname = $firstname;

        return $this;
    }

    /** @return mixed[] */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->getId(),
            'firstname' => $this->getFirstname(),
            'lastname' => $this->getLastname(),
        ];
    }
}
