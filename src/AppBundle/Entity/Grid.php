<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="grids")
 */
class Grid
{
    /**
    * @ORM\Column(type="integer")
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $sessionId;

    /**
     * @ORM\Column(type="text")
     */
    protected $serialized;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set sessionId
     *
     * @param string $sessionId
     *
     * @return Grid
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;

        return $this;
    }

    /**
     * Get sessionId
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Set serialized
     *
     * @param string $serialized
     *
     * @return Grid
     */
    public function setSerialized($serialized)
    {
        $this->serialized = $serialized;

        return $this;
    }

    /**
     * Get serialized
     *
     * @return string
     */
    public function getSerialized()
    {
        return $this->serialized;
    }
}
