<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PageLayout
 *
 * @ORM\Table(name="dtb_page_layout")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PageLayoutRepository")
 */
class PageLayout extends AbstractEntity
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
     * @var int
     *
     * @ORM\Column(name=“sort_no”, type=“smallint”, nullable=true, options={“unsigned”:true})
     */
    private $sort_no;

    /**
     * @var \Eccube\Entity\Page
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Page", inversedBy="PageLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     * })
     */
    private $Page;

    /**
     * @var \Eccube\Entity\Layout
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Layout", inversedBy="PageLayouts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="layout_id", referencedColumnName="id")
     * })
     */
    private $Layout;


    /**
     * Set pageId
     *
     * @param integer $pageId
     *
     * @return PageLayout
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
     * @return PageLayout
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
     * Set sort_no
     *
     * @param int $sortNo
     * @return Page
     */
    public function setSortNo($sortNo)
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    /**
     * Get sort_no
     *
     * @return int
     */
    public function getSortNo()
    {
        return $this->sort_no;
    }

    /**
     * Set pageLayout
     *
     * @param \Eccube\Entity\Page $page
     *
     * @return PageLayout
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
     * @return PageLayout
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

