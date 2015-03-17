<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bloc
 */
class Bloc
{
    /**
     * @var integer
     */
    private $device_type_id;

    /**
     * @var integer
     */
    private $bloc_id;

    /**
     * @var string
     */
    private $bloc_name;

    /**
     * @var string
     */
    private $tpl_path;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $php_path;

    /**
     * @var integer
     */
    private $deletable_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BlocPositions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlocPositions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set device_type_id
     *
     * @param integer $deviceTypeId
     * @return Bloc
     */
    public function setDeviceTypeId($deviceTypeId)
    {
        $this->device_type_id = $deviceTypeId;

        return $this;
    }

    /**
     * Get device_type_id
     *
     * @return integer 
     */
    public function getDeviceTypeId()
    {
        return $this->device_type_id;
    }

    /**
     * Set bloc_id
     *
     * @param integer $blocId
     * @return Bloc
     */
    public function setBlocId($blocId)
    {
        $this->bloc_id = $blocId;

        return $this;
    }

    /**
     * Get bloc_id
     *
     * @return integer 
     */
    public function getBlocId()
    {
        return $this->bloc_id;
    }

    /**
     * Set bloc_name
     *
     * @param string $blocName
     * @return Bloc
     */
    public function setBlocName($blocName)
    {
        $this->bloc_name = $blocName;

        return $this;
    }

    /**
     * Get bloc_name
     *
     * @return string 
     */
    public function getBlocName()
    {
        return $this->bloc_name;
    }

    /**
     * Set tpl_path
     *
     * @param string $tplPath
     * @return Bloc
     */
    public function setTplPath($tplPath)
    {
        $this->tpl_path = $tplPath;

        return $this;
    }

    /**
     * Get tpl_path
     *
     * @return string 
     */
    public function getTplPath()
    {
        return $this->tpl_path;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Bloc
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Bloc
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
     * @return Bloc
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
     * Set php_path
     *
     * @param string $phpPath
     * @return Bloc
     */
    public function setPhpPath($phpPath)
    {
        $this->php_path = $phpPath;

        return $this;
    }

    /**
     * Get php_path
     *
     * @return string 
     */
    public function getPhpPath()
    {
        return $this->php_path;
    }

    /**
     * Set deletable_flg
     *
     * @param integer $deletableFlg
     * @return Bloc
     */
    public function setDeletableFlg($deletableFlg)
    {
        $this->deletable_flg = $deletableFlg;

        return $this;
    }

    /**
     * Get deletable_flg
     *
     * @return integer 
     */
    public function getDeletableFlg()
    {
        return $this->deletable_flg;
    }

    /**
     * Add BlocPositions
     *
     * @param \Eccube\Entity\BlocPosition $blocPositions
     * @return Bloc
     */
    public function addBlocPosition(\Eccube\Entity\BlocPosition $blocPositions)
    {
        $this->BlocPositions[] = $blocPositions;

        return $this;
    }

    /**
     * Remove BlocPositions
     *
     * @param \Eccube\Entity\BlocPosition $blocPositions
     */
    public function removeBlocPosition(\Eccube\Entity\BlocPosition $blocPositions)
    {
        $this->BlocPositions->removeElement($blocPositions);
    }

    /**
     * Get BlocPositions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlocPositions()
    {
        return $this->BlocPositions;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreateDateAuto()
    {
        // Add your code here
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdateDateAuto()
    {
        // Add your code here
    }
}
