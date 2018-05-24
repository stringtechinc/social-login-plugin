<?php

namespace Plugin\SnsLogin\Entity;

use Doctrine\ORM\Mapping as ORM;

class SnsLoginConfig extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $public_key;

    /**
     * @var string
     */
    private $secret_key;

    /**
     * Set id
     *
     * @param integer $id
     * @return SnsLoginConfig
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

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
     * Set name
     *
     * @param string $name
     * @return SnsLoginConfig
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set public_key
     *
     * @param string $publicKey
     * @return SnsLoginConfig
     */
    public function setPublicKey($publicKey)
    {
        $this->public_key = $publicKey;
        return $this;
    }

    /**
     * Get public_key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return $this->public_key;
    }

    /**
     * Set secret_key
     *
     * @param string $secretKey
     * @return SnsLoginConfig
     */
    public function setSecretKey($secretKey)
    {
        $this->secret_key = $secretKey;
        return $this;
    }

    /**
     * Get secret_key
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }
}
