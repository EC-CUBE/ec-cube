<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

if (!class_exists('\Eccube\Entity\Block')) {
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
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
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
         * @var boolean
         *
         * @ORM\Column(name="use_controller", type="boolean", options={"default":false})
         */
        private $use_controller = false;

        /**
         * @var boolean
         *
         * @ORM\Column(name="deletable", type="boolean", options={"default":true})
         */
        private $deletable = true;

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
         * Set id
         *
         * @param integer $id
         *
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
         *
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
         * Set fileName
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
         * Get fileName
         *
         * @return string
         */
        public function getFileName()
        {
            return $this->file_name;
        }

        /**
         * Set useController
         *
         * @param boolean $useController
         *
         * @return Block
         */
        public function setUseController($useController)
        {
            $this->use_controller = $useController;

            return $this;
        }

        /**
         * Get useController
         *
         * @return boolean
         */
        public function isUseController()
        {
            return $this->use_controller;
        }

        /**
         * Set deletable
         *
         * @param boolean $deletable
         *
         * @return Block
         */
        public function setDeletable($deletable)
        {
            $this->deletable = $deletable;

            return $this;
        }

        /**
         * Get deletable
         *
         * @return boolean
         */
        public function isDeletable()
        {
            return $this->deletable;
        }

        /**
         * Set createDate
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
         * Get createDate
         *
         * @return \DateTime
         */
        public function getCreateDate()
        {
            return $this->create_date;
        }

        /**
         * Set updateDate
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
         * Get updateDate
         *
         * @return \DateTime
         */
        public function getUpdateDate()
        {
            return $this->update_date;
        }

        /**
         * Add blockPosition
         *
         * @param \Eccube\Entity\BlockPosition $blockPosition
         *
         * @return Block
         */
        public function addBlockPosition(BlockPosition $blockPosition)
        {
            $this->BlockPositions[] = $blockPosition;

            return $this;
        }

        /**
         * Remove blockPosition
         *
         * @param \Eccube\Entity\BlockPosition $blockPosition
         */
        public function removeBlockPosition(BlockPosition $blockPosition)
        {
            $this->BlockPositions->removeElement($blockPosition);
        }

        /**
         * Get blockPositions
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getBlockPositions()
        {
            return $this->BlockPositions;
        }

        /**
         * Set deviceType
         *
         * @param \Eccube\Entity\Master\DeviceType $deviceType
         *
         * @return Block
         */
        public function setDeviceType(Master\DeviceType $deviceType = null)
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType
         *
         * @return \Eccube\Entity\Master\DeviceType
         */
        public function getDeviceType()
        {
            return $this->DeviceType;
        }
    }
}
