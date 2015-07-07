<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CsvProduct
 */
class CsvProduct extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $col_name;

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
     * Set col_name
     *
     * @param string $colName
     * @return CsvProduct
     */
    public function setColName($colName)
    {
        $this->col_name = $colName;

        return $this;
    }

    /**
     * Get col_name
     *
     * @return string 
     */
    public function getColName()
    {
        return $this->col_name;
    }

    /**
     * Set disp_name
     *
     * @param string $dispName
     * @return CsvProduct
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
     * @return CsvProduct
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
     * @return CsvProduct
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
     * @return CsvProduct
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
     * @return CsvProduct
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
     * Set Creator
     *
     * @param \Eccube\Entity\Member $creator
     * @return CsvProduct
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
        return $this->Creator;
    }
}
