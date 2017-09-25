<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageLayoutLayout
 *
 * @ORM\Table(name="dtb_page_layout_layout")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PageLayoutLayoutRepository")
 */
class PageLayoutLayout extends AbstractEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="page_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $page_id;

    /**
     * @var integer
     *
     * @ORM\Column(name="layout_id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $layout_id;

    /**
     * @var \Eccube\Entity\PageLayout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Page", inversedBy="PageLayoutLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_id", referencedColumnName="page_id")
     * })
     */
    private $Page;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout", inversedBy="PageLayoutLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="layout_id")
     * })
     */
    private $Layout;


    /**
     * Set pageId
     *
     * @param integer $pageId
     *
     * @return PageLayoutLayout
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set layoutId
     *
     * @param integer $layoutId
     *
     * @return PageLayoutLayout
     */
    public function setLayoutId($layoutId)
    {
        $this->layout_id = $layoutId;

        return $this;
    }

    /**
     * Get layoutId
     *
     * @return integer
     */
    public function getLayoutId()
    {
        return $this->layout_id;
    }

    /**
     * Set pageLayout
     *
     * @param \Eccube\Entity\Page $page
     *
     * @return PageLayoutLayout
     */
    public function setPage(\Eccube\Entity\Page $Page = null)
    {
        $this->Page = $Page;

        return $this;
    }

    /**
     * Get pageLayout
     *
     * @return \Eccube\Entity\PageLayout
     */
    public function getPage()
    {
        return $this->Page;
    }

    /**
     * Set layout
     *
     * @param \Eccube\Entity\Layout $layout
     *
     * @return PageLayoutLayout
     */
    public function setLayout(\Eccube\Entity\Layout $layout = null)
    {
        $this->Layout = $layout;

        return $this;
    }

    /**
     * Get layout
     *
     * @return \Eccube\Entity\Layout
     */
    public function getLayout()
    {
        return $this->Layout;
    }
}

