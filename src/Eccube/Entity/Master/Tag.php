<?php

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Table(name="mtb_tag")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\Master\TagRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Tag extends \Eccube\Entity\Master\AbstractMasterEntity
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Eccube\Entity\ProductTag", mappedBy="Tag")
     */
    protected $ProductTag;

    /**
     * Add productTag.
     *
     * @param \Eccube\Entity\ProductTag $productTag
     *
     * @return Tag
     */
    public function addProductTag(\Eccube\Entity\ProductTag $productTag)
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
    public function removeProductTag(\Eccube\Entity\ProductTag $productTag)
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
