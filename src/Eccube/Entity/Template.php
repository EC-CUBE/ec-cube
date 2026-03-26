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

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\TemplateRepository;

if (!class_exists(Template::class)) {
    /**
     * Template
     */
    #[ORM\Table(name: 'dtb_template')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: TemplateRepository::class)]
    class Template extends AbstractEntity implements \Stringable
    {
        /**
         *  初期テンプレートコード
         */
        public const DEFAULT_TEMPLATE_CODE = 'default';

        public function isDefaultTemplate(): bool
        {
            return self::DEFAULT_TEMPLATE_CODE === $this->getCode();
        }

        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private ?int $id = null;

        #[ORM\Column(name: 'template_code', type: Types::STRING, length: 255)]
        private ?string $code = null;

        #[ORM\Column(name: 'template_name', type: Types::STRING, length: 255)]
        private ?string $name = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $create_date = null;

        #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $update_date = null;

        #[ORM\ManyToOne(targetEntity: DeviceType::class)]
        #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
        private ?DeviceType $DeviceType = null;

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
         * Set code.
         */
        public function setCode(string $code): Template
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
         * Set name.
         */
        public function setName(string $name): Template
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
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): Template
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
        public function setUpdateDate(\DateTime $updateDate): Template
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

        /**
         * Set deviceType.
         */
        public function setDeviceType(?DeviceType $deviceType = null): Template
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType.
         */
        public function getDeviceType(): ?DeviceType
        {
            return $this->DeviceType;
        }
    }
}
