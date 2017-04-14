<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Block
 *
 * @ORM\Table(name="dtb_block", uniqueConstraints={@ORM\UniqueConstraint(name="device_type_id", columns={"device_type_id", "file_name"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\BlockRepository")
 */
class Block extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    const UNUSED_BLOCK_ID = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="block_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="block_name", type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $file_name;

    /**
     * @var int
     *
     * @ORM\Column(name="logic_flg", type="smallint", options={"unsigned":true,"default":1})
     */
    private $logic_flg = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="deletable_flg", type="smallint", options={"unsigned":true,"default":1})
     */
    private $deletable_flg = 1;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetime")
     */
    private $update_date;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\BlockPosition", mappedBy="Block", cascade={"persist","remove"})
     */
    private $BlockPositions;

    /**
     * @var \Eccube\Entity\Master\DeviceType
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\DeviceType")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
     * })
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
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param integer $id
     *
     * @return integer
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return Block
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fileName.
     *
     * @param string $fileName
     *
     * @return Block
     */
    public function setFileName($fileName)
    {
        $this->file_name = $fileName;

        return $this;
    }

    /**
     * Get fileName.
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->file_name;
    }

    /**
     * Set logicFlg.
     *
     * @param int $logicFlg
     *
     * @return Block
     */
    public function setLogicFlg($logicFlg)
    {
        $this->logic_flg = $logicFlg;

        return $this;
    }

    /**
     * Get logicFlg.
     *
     * @return int
     */
    public function getLogicFlg()
    {
        return $this->logic_flg;
    }

    /**
     * Set deletableFlg.
     *
     * @param int $deletableFlg
     *
     * @return Block
     */
    public function setDeletableFlg($deletableFlg)
    {
        $this->deletable_flg = $deletableFlg;

        return $this;
    }

    /**
     * Get deletableFlg.
     *
     * @return int
     */
    public function getDeletableFlg()
    {
        return $this->deletable_flg;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return Block
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
     * @return Block
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
     * Add blockPosition.
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     *
     * @return Block
     */
    public function addBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        $this->BlockPositions[] = $blockPosition;

        return $this;
    }

    /**
     * Remove blockPosition.
     *
     * @param \Eccube\Entity\BlockPosition $blockPosition
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeBlockPosition(\Eccube\Entity\BlockPosition $blockPosition)
    {
        return $this->BlockPositions->removeElement($blockPosition);
    }

    /**
     * Get blockPositions.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlockPositions()
    {
        return $this->BlockPositions;
    }

    /**
     * Set deviceType.
     *
     * @param \Eccube\Entity\Master\DeviceType|null $deviceType
     *
     * @return Block
     */
    public function setDeviceType(\Eccube\Entity\Master\DeviceType $deviceType = null)
    {
        $this->DeviceType = $deviceType;

        return $this;
    }

    /**
     * Get deviceType.
     *
     * @return \Eccube\Entity\Master\DeviceType|null
     */
    public function getDeviceType()
    {
        return $this->DeviceType;
    }
}
