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
 * Plugin
 */
class Plugin extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $class_name;

    /**
     * @var string
     */
    private $author;

    /**
     * @var string
     */
    private $author_site_url;

    /**
     * @var string
     */
    private $plugin_site_url;

    /**
     * @var string
     */
    private $plugin_version;

    /**
     * @var string
     */
    private $compliant_version;

    /**
     * @var string
     */
    private $plugin_description;

    /**
     * @var integer
     */
    private $priority;

    /**
     * @var integer
     */
    private $enable;

    /**
     * @var string
     */
    private $free_field1;

    /**
     * @var string
     */
    private $free_field2;

    /**
     * @var string
     */
    private $free_field3;

    /**
     * @var string
     */
    private $free_field4;

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
     * Set name
     *
     * @param  string $name
     * @return Plugin
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set code
     *
     * @param  string $code
     * @return Plugin
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set class_name
     *
     * @param  string $className
     * @return Plugin
     */
    public function setClassName($className)
    {
        $this->class_name = $className;

        return $this;
    }

    /**
     * Get class_name
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->class_name;
    }

    /**
     * Set author
     *
     * @param  string $author
     * @return Plugin
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get author
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set author_site_url
     *
     * @param  string $authorSiteUrl
     * @return Plugin
     */
    public function setAuthorSiteUrl($authorSiteUrl)
    {
        $this->author_site_url = $authorSiteUrl;

        return $this;
    }

    /**
     * Get author_site_url
     *
     * @return string
     */
    public function getAuthorSiteUrl()
    {
        return $this->author_site_url;
    }

    /**
     * Set plugin_site_url
     *
     * @param  string $pluginSiteUrl
     * @return Plugin
     */
    public function setPluginSiteUrl($pluginSiteUrl)
    {
        $this->plugin_site_url = $pluginSiteUrl;

        return $this;
    }

    /**
     * Get plugin_site_url
     *
     * @return string
     */
    public function getPluginSiteUrl()
    {
        return $this->plugin_site_url;
    }

    /**
     * Set plugin_version
     *
     * @param  string $pluginVersion
     * @return Plugin
     */
    public function setPluginVersion($pluginVersion)
    {
        $this->plugin_version = $pluginVersion;

        return $this;
    }

    /**
     * Get plugin_version
     *
     * @return string
     */
    public function getPluginVersion()
    {
        return $this->plugin_version;
    }

    /**
     * Set compliant_version
     *
     * @param  string $compliantVersion
     * @return Plugin
     */
    public function setCompliantVersion($compliantVersion)
    {
        $this->compliant_version = $compliantVersion;

        return $this;
    }

    /**
     * Get compliant_version
     *
     * @return string
     */
    public function getCompliantVersion()
    {
        return $this->compliant_version;
    }

    /**
     * Set plugin_description
     *
     * @param  string $pluginDescription
     * @return Plugin
     */
    public function setPluginDescription($pluginDescription)
    {
        $this->plugin_description = $pluginDescription;

        return $this;
    }

    /**
     * Get plugin_description
     *
     * @return string
     */
    public function getPluginDescription()
    {
        return $this->plugin_description;
    }

    /**
     * Set priority
     *
     * @param  integer $priority
     * @return Plugin
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Get priority
     *
     * @return integer
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * Set enable
     *
     * @param  integer $enable
     * @return Plugin
     */
    public function setEnable($enable)
    {
        $this->enable = $enable;

        return $this;
    }

    /**
     * Get enable
     *
     * @return integer
     */
    public function getEnable()
    {
        return $this->enable;
    }

    /**
     * Set free_field1
     *
     * @param  string $freeField1
     * @return Plugin
     */
    public function setFreeField1($freeField1)
    {
        $this->free_field1 = $freeField1;

        return $this;
    }

    /**
     * Get free_field1
     *
     * @return string
     */
    public function getFreeField1()
    {
        return $this->free_field1;
    }

    /**
     * Set free_field2
     *
     * @param  string $freeField2
     * @return Plugin
     */
    public function setFreeField2($freeField2)
    {
        $this->free_field2 = $freeField2;

        return $this;
    }

    /**
     * Get free_field2
     *
     * @return string
     */
    public function getFreeField2()
    {
        return $this->free_field2;
    }

    /**
     * Set free_field3
     *
     * @param  string $freeField3
     * @return Plugin
     */
    public function setFreeField3($freeField3)
    {
        $this->free_field3 = $freeField3;

        return $this;
    }

    /**
     * Get free_field3
     *
     * @return string
     */
    public function getFreeField3()
    {
        return $this->free_field3;
    }

    /**
     * Set free_field4
     *
     * @param  string $freeField4
     * @return Plugin
     */
    public function setFreeField4($freeField4)
    {
        $this->free_field4 = $freeField4;

        return $this;
    }

    /**
     * Get free_field4
     *
     * @return string
     */
    public function getFreeField4()
    {
        return $this->free_field4;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Plugin
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
     * @param  \DateTime $updateDate
     * @return Plugin
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
