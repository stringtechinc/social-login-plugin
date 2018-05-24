<?php

namespace Plugin\SnsLogin\Entity;

use Doctrine\ORM\Mapping as ORM;

class SnsLoginCustomer extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $customer_id;

    /**
     * @var string
     */
    private $union_id;

    /**
     * @var string
     */
    private $info;

    /**
     * Set id
     *
     * @param integer $id
     * @return SnsLoginCustomer
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
     * Set customer_id
     *
     * @param integer $customer_id
     * @return SnsLoginCustomer
     */
    public function setCustomerId($customer_id)
    {
        $this->customer_id = $customer_id;
        return $this;
    }

    /**
     * Get customer_id
     *
     * @return integer
     */
    public function getCustomerId()
    {
        return $this->customer_id;
    }

    /**
     * Set union_id
     *
     * @param string $union_id
     * @return SnsLoginCustomer
     */
    public function setUnionId($union_id)
    {
        $this->union_id = $union_id;
        return $this;
    }

    /**
     * Get union_id
     *
     * @return string
     */
    public function getUnionId()
    {
        return $this->union_id;
    }

    /**
     * Set info
     *
     * @param string $info
     * @return SnsLoginCustomer
     */
    public function setInfo($info)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Get info
     *
     * @return string
     */
    public function getInfo()
    {
        return $this->info;
    }
}
