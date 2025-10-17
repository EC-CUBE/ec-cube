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

        /**
         * @return bool
         */
        public function isDefaultTemplate(): bool
        {
            return self::DEFAULT_TEMPLATE_CODE === $this->getCode();
        }

        /**
         * @return string
         */
        #[\Override]
        public function __toString(): string
        {
            return $this->getName();
        }

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
        #[ORM\Column(name: 'template_code', type: 'string', length: 255)]
        private $code;

        /**
         * @var string
         */
        #[ORM\Column(name: 'template_name', type: 'string', length: 255)]
        private $name;

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
         * @var DeviceType|null
         */
        #[ORM\ManyToOne(targetEntity: DeviceType::class)]
        #[ORM\JoinColumn(name: 'device_type_id', referencedColumnName: 'id')]
        private $DeviceType;

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
         *
         * @param string $code
         *
         * @return Template
         */
        public function setCode($code): Template
        {
            $this->code = $code;

            return $this;
        }

        /**
         * Get code.
         *
         * @return string
         */
        public function getCode(): string
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
        public function setName($name): Template
        {
            $this->name = $name;

            return $this;
        }

        /**
         * Get name.
         *
         * @return string
         */
        public function getName(): string
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
        public function setCreateDate($createDate): Template
        {
            $this->create_date = $createDate;

            return $this;
        }

        /**
         * Get createDate.
         *
         * @return \DateTime|null
         */
        public function getCreateDate(): ?\DateTime
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
        public function setUpdateDate($updateDate): Template
        {
            $this->update_date = $updateDate;

            return $this;
        }

        /**
         * Get updateDate.
         *
         * @return \DateTime|null
         */
        public function getUpdateDate(): ?\DateTime
        {
            return $this->update_date;
        }

        /**
         * Set deviceType.
         *
         * @param DeviceType|null $deviceType
         *
         * @return Template
         */
        public function setDeviceType(?DeviceType $deviceType = null): Template
        {
            $this->DeviceType = $deviceType;

            return $this;
        }

        /**
         * Get deviceType.
         *
         * @return DeviceType|null
         */
        public function getDeviceType(): ?DeviceType
        {
            return $this->DeviceType;
        }
    }
}
