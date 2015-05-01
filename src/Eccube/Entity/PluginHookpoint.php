<?php

namespace Eccube\Entity;

/**
 * PluginHookpoint
 */
class PluginHookpoint extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $plugin_id;

    /**
     * @var string
     */
    private $hook_point;

    /**
     * @var string
     */
    private $callback;

    /**
     * @var integer
     */
    private $use_flg;

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
     * Set plugin_id
     *
     * @param  integer         $pluginId
     * @return PluginHookpoint
     */
    public function setPluginId($pluginId)
    {
        $this->plugin_id = $pluginId;

        return $this;
    }

    /**
     * Get plugin_id
     *
     * @return integer
     */
    public function getPluginId()
    {
        return $this->plugin_id;
    }

    /**
     * Set hook_point
     *
     * @param  string          $hookPoint
     * @return PluginHookpoint
     */
    public function setHookPoint($hookPoint)
    {
        $this->hook_point = $hookPoint;

        return $this;
    }

    /**
     * Get hook_point
     *
     * @return string
     */
    public function getHookPoint()
    {
        return $this->hook_point;
    }

    /**
     * Set callback
     *
     * @param  string          $callback
     * @return PluginHookpoint
     */
    public function setCallback($callback)
    {
        $this->callback = $callback;

        return $this;
    }

    /**
     * Get callback
     *
     * @return string
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Set use_flg
     *
     * @param  integer         $useFlg
     * @return PluginHookpoint
     */
    public function setUseFlg($useFlg)
    {
        $this->use_flg = $useFlg;

        return $this;
    }

    /**
     * Get use_flg
     *
     * @return integer
     */
    public function getUseFlg()
    {
        return $this->use_flg;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime       $createDate
     * @return PluginHookpoint
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
     * @param  \DateTime       $updateDate
     * @return PluginHookpoint
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
