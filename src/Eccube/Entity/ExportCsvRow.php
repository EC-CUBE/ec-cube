<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Entity;

if (!class_exists('\Eccube\Entity\ExportCsvRow')) {
    class ExportCsvRow extends AbstractEntity
    {
        /**
         * @var \Doctrine\Common\Collections\Collection
         */
        private $row = [];

        /**
         * @var string
         */
        private $data;

        /**
         * Set data
         *
         * @param string $data
         *
         * @return ExportCsvRow
         */
        public function setData($data = null)
        {
            $this->data = $data;

            return $this;
        }

        /**
         * Is data null
         *
         * @return bool
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
}
