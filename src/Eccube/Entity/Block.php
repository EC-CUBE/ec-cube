<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 */
class Block extends \Eccube\Entity\AbstractEntity
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
    private $file_name;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var integer
     */
    private $logic_flg;

    /**
     * @var integer
     */
    private $deletable_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BlockPositions;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     */
    private $DeviceType;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlockPositions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set id
     *
     * @param string $id
     * @return Block
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
     * @return Block
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
     * Set file_name
     *
     * @param string $fileName
     * @return Block
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get file_name
     *
     * @return string 
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return Block
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
     * @return Block
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
     * @param integer $logic_flg
     * @return Block
     */
    public function setLogicFlg($logic_flg)
    {
        $this->logic_flg = $logic_flg;

        return $this;
    }

    /**
     * Get logic_flg
     *
     * @return string
     */
    public function getLogicFlg()
    {
        return $this->logic_flg;
    }

    /**
     * Set deletable_flg
     *
     * @param integer $deletableFlg
     * @return Block
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
     * @param \Eccube\Entity\BlockPosition $blocPositions
     * @return Block
     */
    public function addBlockPosition(\Eccube\Entity\BlockPosition $blockPositions)
    {
        $this->BlockPositions[] = $blockPositions;

        return $this;
    }

    /**
     * Remove BlockPositions
     *
     * @param \Eccube\Entity\BlockPosition $blockPositions
     */
    public function removeBlockPosition(\Eccube\Entity\BlockPosition $blockPositions)
    {
        $this->BlockPositions->removeElement($blockPositions);
    }

    /**
     * Get BlockPositions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getBlockPositions()
    {
        return $this->BlockPositions;
    }

    /**
     * Set DeviceType
     *
     * @param \Eccube\Entity\Master\DeviceType $deviceType
     * @return Block
     */
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get DeviceType
     *
     * @return \Eccube\Entity\Master\DeviceType 
     */
    public function getDeviceType()
    {
        return $this->DeviceType;
    }
}
