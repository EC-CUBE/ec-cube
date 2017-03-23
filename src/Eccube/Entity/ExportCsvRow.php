<?php

namespace Eccube\Entity;

class ExportCsvRow extends \Eccube\Entity\AbstractEntity
{
    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $row = array();

    /**
     * @var string
     */
    private $data = null;

    /**
     * Set data
     *
     * @param string $data
     * @return \Eccube\Entity\ExportCsvRow
     */
    public function setData($data = null) {
        $this->data = $data;
        return $this;
    }

    /**
     * Is data null
     * @return boolean
     */
    public function isDataNull() {
        if (is_null($this->data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Push data
     */
    public function pushData() {
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
