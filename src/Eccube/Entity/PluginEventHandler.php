<?php

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PluginEventHandler
 */
class PluginEventHandler
{
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
    public function setPriority(\int $priority)
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
    public function setPluginId(\int $pluginId)
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
}
