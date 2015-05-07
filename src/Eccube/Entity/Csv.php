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
 * Csv
 */
class Csv extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var integer
     */
    private $no;

    /**
     * @var integer
     */
    private $csv_id;

    /**
     * @var string
     */
    private $col;

    /**
     * @var string
     */
    private $disp_name;

    /**
     * @var integer
     */
    private $rank;

    /**
     * @var integer
     */
    private $rw_flg;

    /**
     * @var integer
     */
    private $status;

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
    private $mb_convert_kana_option;

    /**
     * @var string
     */
    private $size_const_type;

    /**
     * @var string
     */
    private $error_check_types;

    /**
     * Get no
     *
     * @return integer
     */
    public function getNo()
    {
        return $this->no;
    }

    /**
     * Set csv_id
     *
     * @param  integer $csvId
     * @return Csv
     */
    public function setCsvId($csvId)
    {
        $this->csv_id = $csvId;

        return $this;
    }

    /**
     * Get csv_id
     *
     * @return integer
     */
    public function getCsvId()
    {
        return $this->csv_id;
    }

    /**
     * Set col
     *
     * @param  string $col
     * @return Csv
     */
    public function setCol($col)
    {
        $this->col = $col;

        return $this;
    }

    /**
     * Get col
     *
     * @return string
     */
    public function getCol()
    {
        return $this->col;
    }

    /**
     * Set disp_name
     *
     * @param  string $dispName
     * @return Csv
     */
    public function setDispName($dispName)
    {
        $this->disp_name = $dispName;

        return $this;
    }

    /**
     * Get disp_name
     *
     * @return string
     */
    public function getDispName()
    {
        return $this->disp_name;
    }

    /**
     * Set rank
     *
     * @param  integer $rank
     * @return Csv
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set rw_flg
     *
     * @param  integer $rwFlg
     * @return Csv
     */
    public function setRwFlg($rwFlg)
    {
        $this->rw_flg = $rwFlg;

        return $this;
    }

    /**
     * Get rw_flg
     *
     * @return integer
     */
    public function getRwFlg()
    {
        return $this->rw_flg;
    }

    /**
     * Set status
     *
     * @param  integer $status
     * @return Csv
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set create_date
     *
     * @param  \DateTime $createDate
     * @return Csv
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
     * @return Csv
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
     * Set mb_convert_kana_option
     *
     * @param  string $mbConvertKanaOption
     * @return Csv
     */
    public function setMbConvertKanaOption($mbConvertKanaOption)
    {
        $this->mb_convert_kana_option = $mbConvertKanaOption;

        return $this;
    }

    /**
     * Get mb_convert_kana_option
     *
     * @return string
     */
    public function getMbConvertKanaOption()
    {
        return $this->mb_convert_kana_option;
    }

    /**
     * Set size_const_type
     *
     * @param  string $sizeConstType
     * @return Csv
     */
    public function setSizeConstType($sizeConstType)
    {
        $this->size_const_type = $sizeConstType;

        return $this;
    }

    /**
     * Get size_const_type
     *
     * @return string
     */
    public function getSizeConstType()
    {
        return $this->size_const_type;
    }

    /**
     * Set error_check_types
     *
     * @param  string $errorCheckTypes
     * @return Csv
     */
    public function setErrorCheckTypes($errorCheckTypes)
    {
        $this->error_check_types = $errorCheckTypes;

        return $this;
    }

    /**
     * Get error_check_types
     *
     * @return string
     */
    public function getErrorCheckTypes()
    {
        return $this->error_check_types;
    }
}
