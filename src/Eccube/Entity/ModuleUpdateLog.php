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
 * ModuleUpdateLog
 */
class ModuleUpdateLog extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $buckup_path;

    /**
     * @var integer
     */
    private $error_flg;

    /**
     * @var string
     */
    private $error;

    /**
     * @var string
     */
    private $ok;

    /**
     * @var \DateTime
     */
    private $create_date;

    /**
     * @var \DateTime
     */
    private $update_date;

    /**
     * @var \Eccube\Entity\Module
     */
    private $Module;

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
     * Set buckup_path
     *
     * @param  string          $buckupPath
     * @return ModuleUpdateLog
     */
    public function setBuckupPath($buckupPath)
    {
        $this->buckup_path = $buckupPath;

        return $this;
    }

    /**
     * Get buckup_path
     *
     * @return string
     */
    public function getBuckupPath()
    {
        return $this->buckup_path;
    }

    /**
     * Set error_flg
     *
     * @param  integer         $errorFlg
     * @return ModuleUpdateLog
     */
    public function setErrorFlg($errorFlg)
    {
        $this->error_flg = $errorFlg;

        return $this;
    }

    /**
     * Get error_flg
     *
     * @return integer
     */
    public function getErrorFlg()
    {
        return $this->error_flg;
    }

    /**
     * Set error
     *
     * @param  string          $error
     * @return ModuleUpdateLog
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * Get error
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set ok
     *
     * @param  string          $ok
     * @return ModuleUpdateLog
     */
    public function setOk($ok)
    {
        $this->ok = $ok;

        return $this;
    }

    /**
     * Get ok
     *
     * @return string
     */
    public function getOk()
    {
        return $this->ok;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime       $createDate
     * @return ModuleUpdateLog
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
     * @return ModuleUpdateLog
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
     * Set Module
     *
     * @param  \Eccube\Entity\Module $module
     * @return ModuleUpdateLog
     */
    public function setModule(\Eccube\Entity\Module $module)
    {
        $this->Module = $module;

        return $this;
    }

    /**
     * Get Module
     *
     * @return \Eccube\Entity\Module
     */
    public function getModule()
    {
        return $this->Module;
    }
}
