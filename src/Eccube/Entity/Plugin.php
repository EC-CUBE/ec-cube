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

if (!class_exists('\Eccube\Entity\Plugin')) {
    /**
     * Plugin
     *
     * @ORM\Table(name="dtb_plugin")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\PluginRepository")
     */
    class Plugin extends \Eccube\Entity\AbstractEntity
    {
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
         * @ORM\Column(name="name", type="string", length=255)
         */
        private $name;

        /**
         * @var string
         *
         * @ORM\Column(name="code", type="string", length=255)
         */
        private $code;

        /**
         * @var boolean
         *
         * @ORM\Column(name="enabled", type="boolean", options={"default":false})
         */
        private $enabled = false;

        /**
         * @var string
         *
         * @ORM\Column(name="version", type="string", length=255)
         */
        private $version;

        /**
         * @var string
         *
         * @ORM\Column(name="source", type="string", length=255)
         */
        private $source;

        /**
         * @var boolean
         * @ORM\Column(name="initialized", type="boolean", options={"default":false})
         */
        private $initialized = false;

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
         * @return Plugin
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
         * Set code.
         *
         * @param string $code
         *
         * @return Plugin
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
         * Set enabled.
         *
         * @param boolean $enabled
         *
         * @return Plugin
         */
        public function setEnabled($enabled)
        {
            $this->enabled = $enabled;

            return $this;
        }

        /**
         * Get enabled.
         *
         * @return boolean
         */
        public function isEnabled()
        {
            return $this->enabled;
        }

        /**
         * Set version.
         *
         * @param string $version
         *
         * @return Plugin
         */
        public function setVersion($version)
        {
            $this->version = $version;

            return $this;
        }

        /**
         * Get version.
         *
         * @return string
         */
        public function getVersion()
        {
            return $this->version;
        }

        /**
         * Set source.
         *
         * @param string $source
         *
         * @return Plugin
         */
        public function setSource($source)
        {
            $this->source = $source;

            return $this;
        }

        /**
         * Get source.
         *
         * @return string
         */
        public function getSource()
        {
            return $this->source;
        }

        /**
         * Get initialized.
         *
         * @return bool
         */
        public function isInitialized(): bool
        {
            return $this->initialized;
        }

        /**
         * Set initialized.
         *
         * @param bool $initialized
         *
         * @return Plugin
         */
        public function setInitialized(bool $initialized)
        {
            $this->initialized = $initialized;

            return $this;
        }

        /**
         * Set createDate.
         *
         * @param \DateTime $createDate
         *
         * @return Plugin
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
         * @return Plugin
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
    }
}
