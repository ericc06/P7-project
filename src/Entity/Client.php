<?php

namespace App\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="oauth2_clients")
 * @ORM\Entity
 */
class Client extends BaseClient
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=150, nullable=true)
     */
    protected $type;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\EndUser",
     *     mappedBy="client",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $endUsers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->endUsers = new ArrayCollection();
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Add endUser
     *
     * @param EndUser $endUser
     *
     * @return Client
     */
    public function addEndUser(EndUser $endUser): self
    {
        $this->endUsers[] = $endUser;
        $endUser->setClient($this);

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
