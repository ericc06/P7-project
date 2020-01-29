<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation as Serializer;
use Hateoas\Configuration\Annotation as Hateoas;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EndUserRepository")
 *
 * @Serializer\ExclusionPolicy("ALL")
 *
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email address already exists.",
 *     groups={"creation","update"}
 * )
 * @UniqueEntity(
 *     fields={"phoneNumber"},
 *     message="This phone number already exists.",
 *     ignoreNull=true,
 *     groups={"creation","update"}
 * )
 *
 * @Hateoas\Relation(
 *      "self",
 *      href = @Hateoas\Route(
 *          "api_end_user_show",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "modify",
 *      href = @Hateoas\Route(
 *          "api_end_user_update",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 * @Hateoas\Relation(
 *      "delete",
 *      href = @Hateoas\Route(
 *          "api_end_user_delete",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *      )
 * )
 */
class EndUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=50)
     * @Serializer\Expose
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Email(message = "'{{ value }}' is not a valid email address.")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=20, nullable=true, unique=true)
     * @Assert\NotBlank(allowNull = true)
     * @Serializer\Expose
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="datetime")
     * @Serializer\Expose
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Serializer\Expose
     */
    private $lastUpdateDate;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="App\Entity\Client",
     *     inversedBy="endUsers"
     * )
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * Constructor
     *
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getLastUpdateDate(): ?\DateTimeInterface
    {
        return $this->lastUpdateDate;
    }

    public function setLastUpdateDate(\DateTimeInterface $lastUpdateDate): self
    {
        $this->lastUpdateDate = $lastUpdateDate;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }
}
