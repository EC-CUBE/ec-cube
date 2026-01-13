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
use Eccube\Repository\ProductTagRepository;

if (!class_exists(ProductTag::class)) {
    /**
     * ProductTag
     */
    #[ORM\Table(name: 'dtb_product_tag')]
    #[ORM\InheritanceType('SINGLE_TABLE')]
    #[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
    #[ORM\HasLifecycleCallbacks]
    #[ORM\Entity(repositoryClass: ProductTagRepository::class)]
    class ProductTag extends AbstractEntity
    {
        /**
         * Get tag_id
         * use csv export
         */
        public function getTagId(): ?int
        {
            if (empty($this->Tag)) {
                return null;
            }

            return $this->Tag->getId();
        }

        #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: 'IDENTITY')]
        /**  @phpstan-ignore-next-line Doctrine ORMによって自動生成されるため、setterは不要 */
        private ?int $id = null;

        #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
        private ?\DateTime $create_date = null;

        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ProductTag')]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private ?Product $Product = null;

        #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'ProductTag')]
        #[ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id')]
        private ?Tag $Tag = null;

        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private ?Member $Creator = null;

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
         * Set createDate.
         */
        public function setCreateDate(\DateTime $createDate): ProductTag
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
         * Set product.
         */
        public function setProduct(?Product $product = null): ProductTag
        {
            $this->Product = $product;

            return $this;
        }

        /**
         * Get product.
         */
        public function getProduct(): ?Product
        {
            return $this->Product;
        }

        /**
         * Set tag.
         */
        public function setTag(?Tag $tag = null): ProductTag
        {
            $this->Tag = $tag;

            return $this;
        }

        /**
         * Get tag.
         */
        public function getTag(): ?Tag
        {
            return $this->Tag;
        }

        /**
         * Set creator.
         */
        public function setCreator(?Member $creator = null): ProductTag
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}
