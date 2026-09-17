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

class ExportCsvRow extends AbstractEntity
{
    /**
     * @var array<int, string|null>
     */
    private array $row = [];

    private ?string $data = null;

    /**
     * Set data
     *
     * @param string $data
     */
    public function setData(?string $data = null): ExportCsvRow
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Is data null
     */
    public function isDataNull(): bool
    {
        if (is_null($this->data)) {
            return true;
        }

        return false;
    }

    /**
     * Push data
     */
    public function pushData(): void
    {
        $this->row[] = $this->data;
        $this->data = null;
    }

    /**
     * Get row
     *
     * @return array<int, string|null>
     */
    public function getRow(): array
    {
        return $this->row;
    }
}
