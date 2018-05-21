<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PluginEventHandler
 *
 * @ORM\Table(name="dtb_plugin_event_handler")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="Eccube\Repository\PluginEventHandlerRepository")
 */
class PluginEventHandler extends AbstractEntity
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
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned":true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="event", type="string", length=255)
     */
    private $event;

    /**
     * @var int
     *
     * @ORM\Column(name="priority", type="integer", options={"default":0})
     */
    private $priority = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="plugin_id", type="integer", options={"unsigned":true})
     */
    private $plugin_id;

    /**
     * @var string
     *
     * @ORM\Column(name="handler", type="string", length=255)
     */
    private $handler;

    /**
     * @var string
     *
     * @ORM\Column(name="handler_type", type="string", length=255, nullable=false)
     */
    private $handler_type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $create_date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="update_date", type="datetimetz")
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Plugin
     *
     * @ORM\ManyToOne(targetEntity="Eccube\Entity\Plugin", inversedBy="PluginEventHandlers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="plugin_id", referencedColumnName="id")
     * })
     */
    private $Plugin;

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
     * Set event.
     *
     * @param string $event
     *
     * @return PluginEventHandler
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event.
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set priority.
     *
     * @param int $priority
     *
     * @return PluginEventHandler
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set pluginId.
     *
     * @param int $pluginId
     *
     * @return PluginEventHandler
     */
    public function setPluginId($pluginId)
    {
        $this->plugin_id = $pluginId;

        return $this;
    }

    /**
     * Get pluginId.
     *
     * @return int
     */
    public function getPluginId()
    {
        return $this->plugin_id;
    }

    /**
     * Set handler.
     *
     * @param string $handler
     *
     * @return PluginEventHandler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Get handler.
     *
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set handlerType.
     *
     * @param string $handlerType
     *
     * @return PluginEventHandler
     */
    public function setHandlerType($handlerType)
    {
        $this->handler_type = $handlerType;

        return $this;
    }

    /**
     * Get handlerType.
     *
     * @return string
     */
    public function getHandlerType()
    {
        return $this->handler_type;
    }

    /**
     * Set createDate.
     *
     * @param \DateTime $createDate
     *
     * @return PluginEventHandler
     */
    public function setCreateDate($createDate)
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     *
     * @param \DateTime $updateDate
     *
     * @return PluginEventHandler
     */
    public function setUpdateDate($updateDate)
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->update_date;
    }

    /**
     * Set plugin.
     *
     * @param \Eccube\Entity\Plugin|null $plugin
     *
     * @return PluginEventHandler
     */
    public function setPlugin(\Eccube\Entity\Plugin $plugin = null)
    {
        $this->Plugin = $plugin;

        return $this;
    }

    /**
     * Get plugin.
     *
     * @return \Eccube\Entity\Plugin|null
     */
    public function getPlugin()
    {
        return $this->Plugin;
    }
}
