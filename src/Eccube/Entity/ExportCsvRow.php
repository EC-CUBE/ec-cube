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

class ExportCsvRow extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $row = [];

    /**
     * @var string
     */
    private $data = null;

    /**
     * Set data
     *
     * @param string $data
     *
     * @return \Eccube\Entity\ExportCsvRow
     */
    public function setData($data = null)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Is data null
     *
     * @return boolean
     */
    public function isDataNull()
    {
        if (is_null($this->data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Push data
     */
    public function pushData()
    {
        $this->row[] = $this->data;
        $this->data = null;
    }

    /**
     * Get row
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRow()
    {
        return $this->row;
    }
}
