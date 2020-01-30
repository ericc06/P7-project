<?php

namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * Reseller
 *
 * @ORM\Table(name="reseller", indexes={
 *     @ORM\Index(name="search_idx_username", columns={"username"}),
 *     @ORM\Index(name="search_idx_email", columns={"email"}),
 * })
 * @ORM\Entity(repositoryClass="App\Repository\ResellerRepository")
 *
 * @UniqueEntity(fields={"email"}, message="EMAIL_IS_ALREADY_IN_USE")
 *
 * @Serializer\ExclusionPolicy("all")
 */
class Reseller extends BaseUser
{
    const ROLE_SUPER_ADMIN = "ROLE_SUPER_ADMIN";
    const ROLE_ADMIN = "ROLE_ADMIN";
    const ROLE_USER = "ROLE_USER";

    /**
     * To validate supported roles
     *
     * @var array
     */
    static public $ROLES_SUPPORTED = [
        self::ROLE_SUPER_ADMIN => self::ROLE_SUPER_ADMIN,
        self::ROLE_ADMIN => self::ROLE_ADMIN,
        self::ROLE_USER => self::ROLE_USER,
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank(message="FIELD_CAN_NOT_BE_EMPTY")
     * @Assert\Email(
     *     message = "INCORRECT_EMAIL_ADDRESS",
     *     checkMX = true
     * )
     */
    protected $email;

    /**
     * @var string
     *
     * @ORM\Column(name="shop_name", type="string", length=50, nullable=true)
     *
     * @Assert\Length(
     *      min = 1,
     *      max = 50,
     *      minMessage = "FIELD_LENGTH_TOO_SHORT",
     *      maxMessage = "FIELD_LENGTH_TOO_LONG"
     * )
     */
    private $shopName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @Assert\Type(
     *     type="bool",
     *     message="FIELD_MUST_BE_BOOLEAN_TYPE"
     * )
     */
    private $deleted;

    /**
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\Client",
     *     inversedBy="reseller",
     *     cascade={"persist", "remove"}
     * )
     * @ORM\JoinColumn(name="client_id", referencedColumnName="id", nullable=false)
     */
    protected $client;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\EndUser",
     *     mappedBy="reseller",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $endUsers;

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->deleted = false;
        $this->endUsers = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set shopName
     *
     * @param string $shopName
     *
     * @return Reseller
     */
    public function setShopName($shopName)
    {
        $this->shopName = $shopName;

        return $this;
    }

    /**
     * Get shopName
     *
     * @return string
     */
    public function getShopName()
    {
        return $this->shopName;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Reseller
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set client
     *
     * @param Client $client
     *
     * @return Reseller
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Add endUser
     *
     * @param EndUser $endUser
     *
     * @return Reseller
     */
    public function addEndUser(EndUser $endUser): self
    {
        $this->endUsers[] = $endUser;
        $endUser->setReseller($this);

        return $this;
    }

    /**
     * Remove endUser
     *
     * @param EndUser $endUser
     */
    public function removeEndUser(EndUser $endUser)
    {
        $this->endUsers->removeElement($endUser);
    }

    /**
     * Get endUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEndUser(): ?Collection
    {
        return $this->endUsers;
    }

    /**
     * Set endUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function setEndUser(?Collection $endUsers): self
    {
        $this->endUsers = $endUsers;

        return $this;
    }
}
