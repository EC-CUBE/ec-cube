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

if (!class_exists('\Eccube\Entity\Template')) {
    /**
     * Template
     *
     * @ORM\Table(name="dtb_template")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\TemplateRepository")
     */
    class Template extends \Eccube\Entity\AbstractEntity
    {
        /**
         *  初期テンプレートコード
         */
        const DEFAULT_TEMPLATE_CODE = 'default';

        /**
         * @return bool
         */
        public function isDefaultTemplate()
        {
            return self::DEFAULT_TEMPLATE_CODE === $this->getCode();
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return (string) $this->getName();
        }

        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private $id;

        /**
         * @var string
         *
         * @ORM\Column(name="template_code", type="string", length=255)
         */
        private $code;

        /**
         * @var string
         *
         * @ORM\Column(name="template_name", type="string", length=255)
         */
        private $name;

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
         * @var \Eccube\Entity\Master\DeviceType
         *
         * @ORM\ManyToOne(targetEntity="Eccube\Entity\Master\DeviceType")
         * @ORM\JoinColumns({
         *   @ORM\JoinColumn(name="device_type_id", referencedColumnName="id")
         * })
         */
        private $DeviceType;

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
         * Set code.
         *
         * @param string $code
         *
         * @return Template
         */
        public function setCode($code)
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Get code.
         *
         * @return string
         */
        public function getCode()
        {
            return $this->code;
        }

        /**
         * Set name.
         *
         * @param string $name
         *
         * @return Template
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
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Template
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
         * @return Template
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
         * Set deviceType.
         *
         * @param \Eccube\Entity\Master\DeviceType|null $deviceType
         *
         * @return Template
         */
        public function setDeviceType(Master\DeviceType $deviceType = null)
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
}
