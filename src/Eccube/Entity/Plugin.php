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
use Eccube\Repository\PluginRepository;

if (!class_exists(Plugin::class)) {
    /**
     * Plugin
     */
    #[ORM\Table(name: 'dtb_plugin')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: PluginRepository::class)]
    class Plugin extends AbstractEntity
    {
        /**
         * @var int
         */
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private $id;

        /**
         * @var string
         */
        #[ORM\Column(name: 'name', type: 'string', length: 255)]
        private $name;

        /**
         * @var string
         */
        #[ORM\Column(name: 'code', type: 'string', length: 255)]
        private $code;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'enabled', type: 'boolean', options: ['default' => false])]
        private $enabled = false;

        /**
         * @var string
         */
        #[ORM\Column(name: 'version', type: 'string', length: 255)]
        private $version;

        /**
         * @var string
         */
        #[ORM\Column(name: 'source', type: 'string', length: 255)]
        private $source;

        /**
         * @var bool
         */
        #[ORM\Column(name: 'initialized', type: 'boolean', options: ['default' => false])]
        private $initialized = false;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var \DateTime
         */
        #[ORM\Column(name: 'update_date', type: 'datetimetz')]
        private $update_date;

        /**
         * Get id.
         *
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * Set name.
         */
        public function setName(string $name): Plugin
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         */
        public function getName(): string
        {
            return $this->name;
        }

        /**
         * Set code.
         */
        public function setCode(string $code): Plugin
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Get code.
         */
        public function getCode(): string
        {
            return $this->code;
        }

        /**
         * Set enabled.
         */
        public function setEnabled(bool $enabled): Plugin
        {
            $this->enabled = $enabled;

            return $this;
        }

        /**
         * Get enabled.
         */
        public function isEnabled(): bool
        {
            return $this->enabled;
        }

        /**
         * Set version.
         */
        public function setVersion(string $version): Plugin
        {
            $this->version = $version;

            return $this;
        }

        /**
         * Get version.
         */
        public function getVersion(): string
        {
            return $this->version;
        }

        /**
         * Set source.
         */
        public function setSource(string|int $source): Plugin
        {
            $this->source = $source;

            return $this;
        }

        /**
         * Get source.
         */
        public function getSource(): string
        {
            return $this->source;
        }

        /**
         * Get initialized.
         */
        public function isInitialized(): bool
        {
            return $this->initialized;
        }

        /**
         * Set initialized.
         */
        public function setInitialized(bool $initialized): Plugin
        {
            $this->initialized = $initialized;

            return $this;
        }

        /**
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Plugin
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         */
        public function getCreateDate(): ?\DateTime
        {
            return $this->create_date;
        }

        /**
         * Set updateDate.
         */
        public function setUpdateDate(\DateTime $updateDate): Plugin
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }
    }
}
