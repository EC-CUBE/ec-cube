<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Module
 */
class Module extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sub_data;

    /**
     * @var integer
     */
    private $auto_update_flg;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ModuleUpdateLogs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->ModuleUpdateLogs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Module
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Module
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
     * Set sub_data
     *
     * @param string $subData
     * @return Module
     */
    public function setSubData($subData)
    {
        $this->sub_data = $subData;

        return $this;
    }

    /**
     * Get sub_data
     *
     * @return string 
     */
    public function getSubData()
    {
        return $this->sub_data;
    }

    /**
     * Set auto_update_flg
     *
     * @param integer $autoUpdateFlg
     * @return Module
     */
    public function setAutoUpdateFlg($autoUpdateFlg)
    {
        $this->auto_update_flg = $autoUpdateFlg;

        return $this;
    }

    /**
     * Get auto_update_flg
     *
     * @return integer 
     */
    public function getAutoUpdateFlg()
    {
        return $this->auto_update_flg;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return Module
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Module
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
     * @return Module
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
     * Add ModuleUpdateLogs
     *
     * @param \Eccube\Entity\ModuleUpdateLog $moduleUpdateLogs
     * @return Module
     */
    public function addModuleUpdateLog(\Eccube\Entity\ModuleUpdateLog $moduleUpdateLogs)
    {
        $this->ModuleUpdateLogs[] = $moduleUpdateLogs;

        return $this;
    }

    /**
     * Remove ModuleUpdateLogs
     *
     * @param \Eccube\Entity\ModuleUpdateLog $moduleUpdateLogs
     */
    public function removeModuleUpdateLog(\Eccube\Entity\ModuleUpdateLog $moduleUpdateLogs)
    {
        $this->ModuleUpdateLogs->removeElement($moduleUpdateLogs);
    }

    /**
     * Get ModuleUpdateLogs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModuleUpdateLogs()
    {
        return $this->ModuleUpdateLogs;
    }
}
