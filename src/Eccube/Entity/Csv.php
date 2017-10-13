<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Csv
 *
 * @ORM\Table(name="dtb_csv")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\CsvRepository")
 */
class Csv extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var int
     *
     * @ORM\Column(name="csv_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="entity_name", type="string", length=255)
     */
    private $entity_name;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=255)
     */
    private $field_name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="reference_field_name", type="string", length=255, nullable=true)
     */
    private $reference_field_name;

    /**
     * @var string
     *
     * @ORM\Column(name="disp_name", type="string", length=255)
     */
    private $disp_name;

    /**
     * @var int
     *
     * @ORM\Column(name="rank", type="smallint", options={"unsigned":true})
     */
    private $rank;

    /**
     * @var boolean
     *
     * @ORM\Column(name="enable_flg", type="boolean", options={"default":true})
     */
    private $enable_flg = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Master\CsvType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\CsvType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="csv_type", referencedColumnName="id")
     * })
     */
    private $CsvType;

    /**
     * @var \Eccube\Entity\Member
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Member")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="creator_id", referencedColumnName="member_id")
     * })
     */
    private $Creator;


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
     * Set entityName.
     *
     * @param string $entityName
     *
     * @return Csv
     */
    public function setEntityName($entityName)
    {
        $this->entity_name = $entityName;

        return $this;
    }

    /**
     * Get entityName.
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entity_name;
    }

    /**
     * Set fieldName.
     *
     * @param string $fieldName
     *
     * @return Csv
     */
    public function setFieldName($fieldName)
    {
        $this->field_name = $fieldName;

        return $this;
    }

    /**
     * Get fieldName.
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Set referenceFieldName.
     *
     * @param string|null $referenceFieldName
     *
     * @return Csv
     */
    public function setReferenceFieldName($referenceFieldName = null)
    {
        $this->reference_field_name = $referenceFieldName;

        return $this;
    }

    /**
     * Get referenceFieldName.
     *
     * @return string|null
     */
    public function getReferenceFieldName()
    {
        return $this->reference_field_name;
    }

    /**
     * Set dispName.
     *
     * @param string $dispName
     *
     * @return Csv
     */
    public function setDispName($dispName)
    {
        $this->disp_name = $dispName;

        return $this;
    }

    /**
     * Get dispName.
     *
     * @return string
     */
    public function getDispName()
    {
        return $this->disp_name;
    }

    /**
     * Set rank.
     *
     * @param int $rank
     *
     * @return Csv
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

    /**
     * Set enableFlg.
     *
     * @param boolean $enableFlg
     *
     * @return Csv
     */
    public function setEnableFlg($enableFlg)
    {
        $this->enable_flg = $enableFlg;

        return $this;
    }

    /**
     * Get enableFlg.
     *
     * @return boolean
     */
    public function getEnableFlg()
    {
        return $this->enable_flg;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Csv
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return Csv
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set csvType.
     *
     * @param \Eccube\Entity\Master\CsvType|null $csvType
     *
     * @return Csv
     */
    public function setCsvType(\Eccube\Entity\Master\CsvType $csvType = null)
    {
        $this->CsvType = $csvType;

        return $this;
    }

    /**
     * Get csvType.
     *
     * @return \Eccube\Entity\Master\CsvType|null
     */
    public function getCsvType()
    {
        return $this->CsvType;
    }

    /**
     * Set creator.
     *
     * @param \Eccube\Entity\Member|null $creator
     *
     * @return Csv
     */
    public function setCreator(\Eccube\Entity\Member $creator = null)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get creator.
     *
     * @return \Eccube\Entity\Member|null
     */
    public function getCreator()
    {
        return $this->Creator;
    }
}
