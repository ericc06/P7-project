<?php

namespace App\Entity;

use FOS\OAuthServerBundle\Entity\Client as BaseClient;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\OneToOne(
     *     targetEntity="App\Entity\Reseller",
     *     mappedBy="client",
     *     cascade={"persist", "remove"}
     * )
     */
    protected $reseller;

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set reseller
     *
     * @param Reseller $reseller
     *
     * @return Client
     */
    public function setReseller($reseller)
    {
        $this->reseller = $reseller;

        return $this;
    }

    /**
     * Get reseller
     *
     * @return Reseller
     */
    public function getReseller()
    {
        return $this->reseller;
    }
}
