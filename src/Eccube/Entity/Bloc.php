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
 * Bloc
 */
class Bloc extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $device_type_id;

    /**
     * @var integer
     */
    private $bloc_id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $tpl_path;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var string
     */
    private $php_path;

    /**
     * @var integer
     */
    private $deletable_flg;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $BlocPositions;

    /**
     * @var \Eccube\Entity\Plugin
     */
    private $Plugin;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->BlocPositions = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set device_type_id
     *
     * @param  integer $deviceTypeId
     * @return Bloc
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
     * Set bloc_id
     *
     * @param  integer $blocId
     * @return Bloc
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
     * Set name
     *
     * @param  string $name
     * @return Bloc
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
     * Set tpl_path
     *
     * @param  string $tplPath
     * @return Bloc
     */
    public function setTplPath($tplPath)
    {
        $this->tpl_path = $tplPath;

        return $this;
    }

    /**
     * Get tpl_path
     *
     * @return string
     */
    public function getTplPath()
    {
        return $this->tpl_path;
    }

    /**
     * Set filename
     *
     * @param  string $filename
     * @return Bloc
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Bloc
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
     * @return Bloc
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

    /**
     * Set php_path
     *
     * @param  string $phpPath
     * @return Bloc
     */
    public function setPhpPath($phpPath)
    {
        $this->php_path = $phpPath;

        return $this;
    }

    /**
     * Get php_path
     *
     * @return string
     */
    public function getPhpPath()
    {
        return $this->php_path;
    }

    /**
     * Set deletable_flg
     *
     * @param  integer $deletableFlg
     * @return Bloc
     */
    public function setDeletableFlg($deletableFlg)
    {
        $this->deletable_flg = $deletableFlg;

        return $this;
    }

    /**
     * Get deletable_flg
     *
     * @return integer
     */
    public function getDeletableFlg()
    {
        return $this->deletable_flg;
    }

    /**
     * Add BlocPositions
     *
     * @param  \Eccube\Entity\BlocPosition $blocPositions
     * @return Bloc
     */
    public function addBlocPosition(\Eccube\Entity\BlocPosition $blocPositions)
    {
        $this->BlocPositions[] = $blocPositions;

        return $this;
    }

    /**
     * Remove BlocPositions
     *
     * @param \Eccube\Entity\BlocPosition $blocPositions
     */
    public function removeBlocPosition(\Eccube\Entity\BlocPosition $blocPositions)
    {
        $this->BlocPositions->removeElement($blocPositions);
    }

    /**
     * Get BlocPositions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBlocPositions()
    {
        return $this->BlocPositions;
    }

    /**
     * Set Plugin
     *
     * @param  \Eccube\Entity\Plugin $plugin
     * @return Bloc
     */
    public function setPlugin(\Eccube\Entity\Plugin $plugin = null)
    {
        $this->Plugin = $plugin;

        return $this;
    }

    /**
     * Get Plugin
     *
     * @return \Eccube\Entity\Plugin
     */
    public function getPlugin()
    {
        return $this->Plugin;
    }
}
