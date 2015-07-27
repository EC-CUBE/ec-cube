<?php

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * CsvType
 */
class CsvType extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    const CSV_TYPE_PRODUCT = 1;

    /**
     * @var integer
     */
    const CSV_TYPE_CUSTOMER = 2;

     /**
     * @var integer
     */
    const CSV_TYPE_ORDER = 3;

     /**
     * @var integer
     */
    const CSV_TYPE_SHIPPING = 4;

     /**
     * @var integer
     */
    const CSV_TYPE_CATEGORY = 5;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var integer
     */
    private $rank;


    /**
     * Set id
     *
     * @param integer $id
     * @return CsvType
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
     * @return CsvType
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
     * Set rank
     *
     * @param integer $rank
     * @return CsvType
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }
}
