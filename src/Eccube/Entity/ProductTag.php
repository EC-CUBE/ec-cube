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
         *
         * @return int|null
         */
        public function getTagId(): ?int
        {
            if (empty($this->Tag)) {
                return null;
            }

            return $this->Tag->getId();
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
         * @var \DateTime
         */
        #[ORM\Column(name: 'create_date', type: 'datetimetz')]
        private $create_date;

        /**
         * @var Product|null
         */
        #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'ProductTag')]
        #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
        private $Product;

        /**
         * @var Tag|null
         */
        #[ORM\ManyToOne(targetEntity: Tag::class, inversedBy: 'ProductTag')]
        #[ORM\JoinColumn(name: 'tag_id', referencedColumnName: 'id')]
        private $Tag;

        /**
         * @var Member|null
         */
        #[ORM\ManyToOne(targetEntity: Member::class)]
        #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id')]
        private $Creator;

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
         *
         * @param \DateTime $createDate
         *
         * @return ProductTag
         */
        public function setCreateDate($createDate): ProductTag
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
         * Set product.
         *
         * @param Product|null $product
         *
         * @return ProductTag
         */
        public function setProduct(?Product $product = null): ProductTag
        {
            $this->Product = $product;

            return $this;
        }

        /**
         * Get product.
         *
         * @return Product|null
         */
        public function getProduct(): ?Product
        {
            return $this->Product;
        }

        /**
         * Set tag.
         *
         * @param Tag|null $tag
         *
         * @return ProductTag
         */
        public function setTag(?Tag $tag = null): ProductTag
        {
            $this->Tag = $tag;

            return $this;
        }

        /**
         * Get tag.
         *
         * @return Tag|null
         */
        public function getTag(): ?Tag
        {
            return $this->Tag;
        }

        /**
         * Set creator.
         *
         * @param Member|null $creator
         *
         * @return ProductTag
         */
        public function setCreator(?Member $creator = null): ProductTag
        {
            $this->Creator = $creator;

            return $this;
        }

        /**
         * Get creator.
         *
         * @return Member|null
         */
        public function getCreator(): ?Member
        {
            return $this->Creator;
        }
    }
}
