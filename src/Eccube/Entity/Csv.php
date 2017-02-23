<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Util\EntityUtil;

/**
 * Csv
 */
class Csv extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $entity_name;

    /**
     * @var string
     */
    private $field_name;

    /**
     * @var string
     */
    private $reference_field_name;

    /**
     * @var string
     */
    private $disp_name;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $enable_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Master\CsvType
     */
    private $CsvType;

    /**
     * @var \Eccube\Entity\Member
     */
    private $Creator;


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
     * Set entity_name
     *
     * @param string $entityName
     * @return Csv
     */
    public function setEntityName($entityName)
    {
        $this->entity_name = $entityName;

        return $this;
    }

    /**
     * Get entity_name
     *
     * @return string
     */
    public function getEntityName()
    {
        return $this->entity_name;
    }

    /**
     * Set field_name
     *
     * @param string $fieldName
     * @return Csv
     */
    public function setFieldName($fieldName)
    {
        $this->field_name = $fieldName;

        return $this;
    }

    /**
     * Get field_name
     *
     * @return string 
     */
    public function getFieldName()
    {
        return $this->field_name;
    }

    /**
     * Set reference_field_name
     *
     * @param string $feferenceFieldName
     * @return Csv
     */
    public function setReferenceFieldName($referenceFieldName)
    {
        $this->reference_field_name = $referenceFieldName;

        return $this;
    }

    /**
     * Get reference_field_name
     *
     * @return string 
     */
    public function getReferenceFieldName()
    {
        return $this->reference_field_name;
    }

    /**
     * Set disp_name
     *
     * @param string $dispName
     * @return Csv
     */
    public function setDispName($dispName)
    {
        $this->disp_name = $dispName;

        return $this;
    }

    /**
     * Get disp_name
     *
     * @return string 
     */
    public function getDispName()
    {
        return $this->disp_name;
    }

    /**
     * Set rank
     *
     * @param integer $rank
     * @return Csv
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

    /**
     * Set enable_flg
     *
     * @param integer $enableFlg
     * @return Csv
     */
    public function setEnableFlg($enableFlg)
    {
        $this->enable_flg = $enableFlg;

        return $this;
    }

    /**
     * Get enable_flg
     *
     * @return integer 
     */
    public function getEnableFlg()
    {
        return $this->enable_flg;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Csv
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return Csv
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

   /**
     * Set CsvType
     *
     * @param \Eccube\Entity\Master\CsvType $csvType
     * @return Csv
     */
    public function setCsvType(\Eccube\Entity\Master\CsvType $csvType)
    {
        $this->CsvType = $csvType;

        return $this;
    }

    /**
     * Get CsvType
     *
     * @return \Eccube\Entity\Master\CsvType
     */
    public function getCsvType()
    {
        return $this->CsvType;
    }

    /**
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return Csv
     */
    public function setCreator(\Eccube\Entity\Member $creator)
    {
        $this->Creator = $creator;

        return $this;
    }

    /**
     * Get Creator
     *
     * @return \Eccube\Entity\Member 
     */
    public function getCreator()
    {
        if (EntityUtil::isEmpty($this->Creator)) {
            return null;
        }
        return $this->Creator;
    }
}
