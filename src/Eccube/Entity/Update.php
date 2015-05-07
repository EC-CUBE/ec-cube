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
 * Update
 */
class Update extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $module_id;

    /**
     * @var string
     */
    private $module_name;

    /**
     * @var string
     */
    private $now_version;

    /**
     * @var string
     */
    private $latest_version;

    /**
     * @var string
     */
    private $module_explain;

    /**
     * @var string
     */
    private $main_php;

    /**
     * @var string
     */
    private $extern_php;

    /**
     * @var string
     */
    private $install_sql;

    /**
     * @var string
     */
    private $uninstall_sql;

    /**
     * @var string
     */
    private $other_files;

    /**
     * @var integer
     */
    private $del_flg;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \DateTime
     */
    private $release_date;

    /**
     * Get module_id
     *
     * @return integer
     */
    public function getModuleId()
    {
        return $this->module_id;
    }

    /**
     * Set module_name
     *
     * @param  string $moduleName
     * @return Update
     */
    public function setModuleName($moduleName)
    {
        $this->module_name = $moduleName;

        return $this;
    }

    /**
     * Get module_name
     *
     * @return string
     */
    public function getModuleName()
    {
        return $this->module_name;
    }

    /**
     * Set now_version
     *
     * @param  string $nowVersion
     * @return Update
     */
    public function setNowVersion($nowVersion)
    {
        $this->now_version = $nowVersion;

        return $this;
    }

    /**
     * Get now_version
     *
     * @return string
     */
    public function getNowVersion()
    {
        return $this->now_version;
    }

    /**
     * Set latest_version
     *
     * @param  string $latestVersion
     * @return Update
     */
    public function setLatestVersion($latestVersion)
    {
        $this->latest_version = $latestVersion;

        return $this;
    }

    /**
     * Get latest_version
     *
     * @return string
     */
    public function getLatestVersion()
    {
        return $this->latest_version;
    }

    /**
     * Set module_explain
     *
     * @param  string $moduleExplain
     * @return Update
     */
    public function setModuleExplain($moduleExplain)
    {
        $this->module_explain = $moduleExplain;

        return $this;
    }

    /**
     * Get module_explain
     *
     * @return string
     */
    public function getModuleExplain()
    {
        return $this->module_explain;
    }

    /**
     * Set main_php
     *
     * @param  string $mainPhp
     * @return Update
     */
    public function setMainPhp($mainPhp)
    {
        $this->main_php = $mainPhp;

        return $this;
    }

    /**
     * Get main_php
     *
     * @return string
     */
    public function getMainPhp()
    {
        return $this->main_php;
    }

    /**
     * Set extern_php
     *
     * @param  string $externPhp
     * @return Update
     */
    public function setExternPhp($externPhp)
    {
        $this->extern_php = $externPhp;

        return $this;
    }

    /**
     * Get extern_php
     *
     * @return string
     */
    public function getExternPhp()
    {
        return $this->extern_php;
    }

    /**
     * Set install_sql
     *
     * @param  string $installSql
     * @return Update
     */
    public function setInstallSql($installSql)
    {
        $this->install_sql = $installSql;

        return $this;
    }

    /**
     * Get install_sql
     *
     * @return string
     */
    public function getInstallSql()
    {
        return $this->install_sql;
    }

    /**
     * Set uninstall_sql
     *
     * @param  string $uninstallSql
     * @return Update
     */
    public function setUninstallSql($uninstallSql)
    {
        $this->uninstall_sql = $uninstallSql;

        return $this;
    }

    /**
     * Get uninstall_sql
     *
     * @return string
     */
    public function getUninstallSql()
    {
        return $this->uninstall_sql;
    }

    /**
     * Set other_files
     *
     * @param  string $otherFiles
     * @return Update
     */
    public function setOtherFiles($otherFiles)
    {
        $this->other_files = $otherFiles;

        return $this;
    }

    /**
     * Get other_files
     *
     * @return string
     */
    public function getOtherFiles()
    {
        return $this->other_files;
    }

    /**
     * Set del_flg
     *
     * @param  integer $delFlg
     * @return Update
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
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Update
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
     * @return Update
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
     * Set release_date
     *
     * @param  \DateTime $releaseDate
     * @return Update
     */
    public function setReleaseDate($releaseDate)
    {
        $this->release_date = $releaseDate;

        return $this;
    }

    /**
     * Get release_date
     *
     * @return \DateTime
     */
    public function getReleaseDate()
    {
        return $this->release_date;
    }
}
