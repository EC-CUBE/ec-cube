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

if (!class_exists('\Eccube\Entity\TradeLaw')) {
    /**
     * TradeLaw
     *
     * @ORM\Table(name="dtb_tradelaw")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\TradeLawRepository")
     */
    class TradeLaw extends AbstractEntity
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        private int $id;

        /**
         * @var ?string
         *
         * @ORM\Column(name="name", type="string", length=255, nullable=true)
         */
        private ?string $name;

        /**
         * @var ?string
         *
         * @ORM\Column(name="description", type="string", length=4000, nullable=true)
         */
        private ?string $description;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="smallint", nullable=false)
         */
        private int $sortNo;

        /**
         * @var boolean
         *
         * @ORM\Column(name="display_order_screen", type="boolean")
         */
        private bool $displayOrderScreen = false;

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->getName();
        }

        /**
         * @param int $id
         *
         * @return TradeLaw
         */
        public function setId(int $id): TradeLaw
        {
            $this->id = $id;

            return $this;
        }

        /**
         * @return int
         */
        public function getId(): int
        {
            return $this->id;
        }

        /**
         * @param string $name
         *
         * @return TradeLaw
         */
        public function setName(?string $name): TradeLaw
        {
            $this->name = $name ?: '';

            return $this;
        }

        /**
         * @return string
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * @param string $description
         *
         * @return TradeLaw
         */
        public function setDescription(?string $description): TradeLaw
        {
            $this->description = $description ?: '';

            return $this;
        }

        /**
         * @return string
         */
        public function getDescription(): ?string
        {
            return $this->description;
        }

        /**
         * @param int $sortNo
         *
         * @return TradeLaw
         */
        public function setSortNo(int $sortNo): TradeLaw
        {
            $this->sortNo = $sortNo;

            return $this;
        }

        /**
         * @return int
         */
        public function getSortNo(): int
        {
            return $this->sortNo;
        }

        /**
         * @param bool $displayOrderScreen
         *
         * @return TradeLaw
         */
        public function setDisplayOrderScreen(bool $displayOrderScreen): TradeLaw
        {
            $this->displayOrderScreen = $displayOrderScreen;

            return $this;
        }

        /**
         * @return bool
         */
        public function isDisplayOrderScreen(): bool
        {
            return $this->displayOrderScreen;
        }
    }
}
