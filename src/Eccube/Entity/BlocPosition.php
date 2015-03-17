<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BlocPosition
 */
class BlocPosition
{
    /**
     * @var integer
     */
    private $device_type_id;

    /**
     * @var integer
     */
    private $page_id;

    /**
     * @var integer
     */
    private $target_id;

    /**
     * @var integer
     */
    private $bloc_id;

    /**
     * @var integer
     */
    private $bloc_row;

    /**
     * @var integer
     */
    private $anywhere;

    /**
     * @var \Eccube\Entity\Bloc
     */
    private $Bloc;

    /**
     * @var \Eccube\Entity\PageLayout
     */
    private $PageLayout;


    /**
     * Set device_type_id
     *
     * @param integer $deviceTypeId
     * @return BlocPosition
     */
    public function setDeviceTypeId($deviceTypeId)
    {
        $this->device_type_id = $deviceTypeId;

        return $this;
    }

    /**
     * Get device_type_id
     *
     * @return integer 
     */
    public function getDeviceTypeId()
    {
        return $this->device_type_id;
    }

    /**
     * Set page_id
     *
     * @param integer $pageId
     * @return BlocPosition
     */
    public function setPageId($pageId)
    {
        $this->page_id = $pageId;

        return $this;
    }

    /**
     * Get page_id
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->page_id;
    }

    /**
     * Set target_id
     *
     * @param integer $targetId
     * @return BlocPosition
     */
    public function setTargetId($targetId)
    {
        $this->target_id = $targetId;

        return $this;
    }

    /**
     * Get target_id
     *
     * @return integer 
     */
    public function getTargetId()
    {
        return $this->target_id;
    }

    /**
     * Set bloc_id
     *
     * @param integer $blocId
     * @return BlocPosition
     */
    public function setBlocId($blocId)
    {
        $this->bloc_id = $blocId;

        return $this;
    }

    /**
     * Get bloc_id
     *
     * @return integer 
     */
    public function getBlocId()
    {
        return $this->bloc_id;
    }

    /**
     * Set bloc_row
     *
     * @param integer $blocRow
     * @return BlocPosition
     */
    public function setBlocRow($blocRow)
    {
        $this->bloc_row = $blocRow;

        return $this;
    }

    /**
     * Get bloc_row
     *
     * @return integer 
     */
    public function getBlocRow()
    {
        return $this->bloc_row;
    }

    /**
     * Set anywhere
     *
     * @param integer $anywhere
     * @return BlocPosition
     */
    public function setAnywhere($anywhere)
    {
        $this->anywhere = $anywhere;

        return $this;
    }

    /**
     * Get anywhere
     *
     * @return integer 
     */
    public function getAnywhere()
    {
        return $this->anywhere;
    }

    /**
     * Set Bloc
     *
     * @param \Eccube\Entity\Bloc $bloc
     * @return BlocPosition
     */
    public function setBloc(\Eccube\Entity\Bloc $bloc = null)
    {
        $this->Bloc = $bloc;

        return $this;
    }

    /**
     * Get Bloc
     *
     * @return \Eccube\Entity\Bloc 
     */
    public function getBloc()
    {
        return $this->Bloc;
    }

    /**
     * Set PageLayout
     *
     * @param \Eccube\Entity\PageLayout $pageLayout
     * @return BlocPosition
     */
    public function setPageLayout(\Eccube\Entity\PageLayout $pageLayout = null)
    {
        $this->PageLayout = $pageLayout;

        return $this;
    }

    /**
     * Get PageLayout
     *
     * @return \Eccube\Entity\PageLayout 
     */
    public function getPageLayout()
    {
        return $this->PageLayout;
    }
}
