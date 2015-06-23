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

use Doctrine\ORM\Mapping as ORM;

/**
 * PluginEventHandler
 */
class PluginEventHandler
{

    const EVENT_PRIORITY_LATEST = -500; // ハンドラテーブルに登録されていない場合の優先度
    const EVENT_PRIORITY_DISABLED = 0; // ハンドラを無効にする

    const EVENT_PRIORITY_NORMAL_START = 400; // 先発、後発、通常の各型毎の優先度範囲
    const EVENT_PRIORITY_NORMAL_END = -399;

    const EVENT_PRIORITY_FIRST_START = 500;
    const EVENT_PRIORITY_FIRST_END = 401;

    const EVENT_PRIORITY_LAST_START = -400;
    const EVENT_PRIORITY_LAST_END = -499;

    const EVENT_HANDLER_TYPE_NORMAL = 'NORMAL';
    const EVENT_HANDLER_TYPE_FIRST = 'FIRST';
    const EVENT_HANDLER_TYPE_LAST = 'LAST';

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $event;

    /**
     * @var int
     */
    private $priority;

    /**
     * @var int
     */
    private $plugin_id;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var string
     */
    private $handler;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    private $Plugin;

    private $handler_type;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set event
     *
     * @param string $event
     * @return PluginEventHandler
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set priority
     *
     * @param \int $priority
     * @return PluginEventHandler
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return \int 
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set plugin_id
     *
     * @param \int $pluginId
     * @return PluginEventHandler
     */
    public function setPluginId($pluginId)
    {
        $this->plugin_id = $pluginId;

        return $this;
    }

    /**
     * Get plugin_id
     *
     * @return \int 
     */
    public function getPluginId()
    {
        return $this->plugin_id;
    }

    /**
     * Set del_flg
     *
     * @param integer $delFlg
     * @return PluginEventHandler
     */
    public function setDelFlg($delFlg)
    {
        $this->del_flg = $delFlg;

        return $this;
    }

    /**
     * Get del_flg
     *
     * @return integer 
     */
    public function getDelFlg()
    {
        return $this->del_flg;
    }

    /**
     * Set handler
     *
     * @param string $handler
     * @return PluginEventHandler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get handler
     *
     * @return string 
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set create_date
     *
     * @param \DateTime $createDate
     * @return PluginEventHandler
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get create_date
     *
     * @return \DateTime 
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    public function setHandlerType($handlerType)
    {
        $this->handler_type = $handlerType;

        return $this;
    }

    public function getHandlerType()
    {
        return $this->handler_type;
    }

    /**
     * Set update_date
     *
     * @param \DateTime $updateDate
     * @return PluginEventHandler
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get update_date
     *
     * @return \DateTime 
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    public function getPlugin()
    {
        return $this->Plugin;
    }
    public function setPlugin(\Eccube\Entity\Plugin $Plugin)
    {
        $this->Plugin = $Plugin;
        return $this;
    }

}
