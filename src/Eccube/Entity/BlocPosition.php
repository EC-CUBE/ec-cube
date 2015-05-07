<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Entity;

/**
 * BlocPosition
 */
class BlocPosition extends \Eccube\Entity\AbstractEntity
{

    const UNUSED = 0;
    const HEAD = 7;
    const HEAD_TOP = 8;
    const HEAD_INTERNAL = 10;
    const TOP = 5;
    const LEFT = 1;
    const MAIN_HEAD = 2;
    const RIGHT = 3;
    const MAIN_FOOT = 4;
    const BOTTOM = 6;
    const FOOT = 9;

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
     * @param  integer      $deviceTypeId
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
     * @param  integer      $pageId
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
     * @param  integer      $targetId
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
     * @param  integer      $blocId
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
     * @param  integer      $blocRow
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
     * @param  integer      $anywhere
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
     * @param  \Eccube\Entity\Bloc $bloc
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
     * @param  \Eccube\Entity\PageLayout $pageLayout
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
