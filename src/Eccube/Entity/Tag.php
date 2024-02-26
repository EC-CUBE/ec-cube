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

if (!class_exists('\Eccube\Entity\Tag')) {
    /**
     * Tag
     *
     * @ORM\Table(name="dtb_tag")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\TagRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    class Tag extends \Eccube\Entity\AbstractEntity
    {
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
        protected $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        protected $name;

        /**
         * @var int
         *
         * @ORM\Column(name="sort_no", type="smallint", options={"unsigned":true})
         */
        protected $sort_no;

        /**
         * @var \Doctrine\Common\Collections\Collection
         *
         * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductTag", mappedBy="Tag")
         */
        protected $ProductTag;

        /**
         * Constructor
         */
        public function __construct()
        {
            $this->ProductTag = new \Doctrine\Common\Collections\ArrayCollection();
        }

        /**
         * Set id.
         *
         * @param int $id
         *
         * @return $this
         */
        public function setId($id)
        {
            $this->id = $id;

            return $this;
        }

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
         * @return $this
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
         * Set sort_no.
         *
         * @param int $sort_no
         *
         * @return $this
         */
        public function setSortNo($sort_no)
        {
            $this->sort_no = $sort_no;

            return $this;
        }

        /**
         * Get sort_no.
         *
         * @return int
         */
        public function getSortNo()
        {
            return $this->sort_no;
        }

        /**
         * Add productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return Tag
         */
        public function addProductTag(ProductTag $productTag)
        {
            $this->ProductTag[] = $productTag;

            return $this;
        }

        /**
         * Remove productTag.
         *
         * @param \Eccube\Entity\ProductTag $productTag
         *
         * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
         */
        public function removeProductTag(ProductTag $productTag)
        {
            return $this->ProductTag->removeElement($productTag);
        }

        /**
         * Get productTag.
         *
         * @return \Doctrine\Common\Collections\Collection
         */
        public function getProductTag()
        {
            return $this->ProductTag;
        }
    }
}
