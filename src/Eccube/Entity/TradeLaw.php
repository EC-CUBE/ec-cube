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
use Eccube\Repository\TradeLawRepository;

if (!class_exists(TradeLaw::class)) {
    /**
     * TradeLaw
     */
    #[ORM\Table(name: 'dtb_tradelaw')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: TradeLawRepository::class)]
    class TradeLaw extends AbstractEntity implements \Stringable
    {
        #[ORM\Column(name: 'id', type: 'integer', options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        private int $id;

        #[ORM\Column(name: 'name', type: 'string', length: 255, nullable: true)]
        private ?string $name = null;

        #[ORM\Column(name: 'description', type: 'string', length: 4000, nullable: true)]
        private ?string $description = null;

        #[ORM\Column(name: 'sort_no', type: 'smallint', nullable: false)]
        private int $sortNo;

        #[ORM\Column(name: 'display_order_screen', type: 'boolean')]
        private bool $displayOrderScreen = false;

        #[\Override]
        public function __toString(): string
        {
            return $this->getName() ?? '';
        }

        public function setId(int $id): TradeLaw
        {
            $this->id = $id;

            return $this;
        }

        /**
         * @return int
         */
        public function getId(): ?int
        {
            return $this->id;
        }

        /**
         * @param string $name
         */
        public function setName(?string $name): TradeLaw
        {
            $this->name = $name ?: '';

            return $this;
        }

        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * @param string $description
         */
        public function setDescription(?string $description): TradeLaw
        {
            $this->description = $description ?: '';

            return $this;
        }

        public function getDescription(): ?string
        {
            return $this->description;
        }

        public function setSortNo(int $sortNo): TradeLaw
        {
            $this->sortNo = $sortNo;

            return $this;
        }

        public function getSortNo(): int
        {
            return $this->sortNo;
        }

        public function setDisplayOrderScreen(bool $displayOrderScreen): TradeLaw
        {
            $this->displayOrderScreen = $displayOrderScreen;

            return $this;
        }

        public function isDisplayOrderScreen(): bool
        {
            return $this->displayOrderScreen;
        }
    }
}
