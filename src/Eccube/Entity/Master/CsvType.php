<?php

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * CsvType
 *
 * @ORM\Table(name="mtb_csv_type")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\CsvTypeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
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
     * @var int
     *
     * @ORM\Column(name="id", type="smallint", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint", options={"unsigned":true})
     */
    private $rank;


    /**
     * Set id.
     *
     * @param int $id
     *
     * @return CsvType
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return CsvType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return CsvType
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank.
     *
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }
}
