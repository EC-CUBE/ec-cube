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
namespace Eccube\Service\Composer;

/**
 * Class InfoCommandParser
 * @package Eccube\Service\Composer
 */
class InfoCommandParser
{
    private $output;

    /**
     * InfoCommandParser constructor.
     * @param string $output
     */
    public function __construct($output)
    {
        $this->output = $output;
    }

    /**
     * Parse to array
     *
     * @return array
     */
    public function parse()
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $this->output));
        $infoLogs = array_filter(array_map(function($line) {
            $matches = array();
            preg_match('/^(name|descrip.|keywords|versions|type|license|source|dist|names)\s*:\s*(.*)$/', $line, $matches);

            return $matches;
        }, $rowArray));

        // 'name' => 'value'
        $result = array_column($infoLogs, 2, 1);
        $result['requires'] = $this->parseArrayOutput($rowArray, 'requires');
        $result['requires (dev)'] = $this->parseArrayOutput($rowArray, 'requires (dev)');

        return $result;
    }

    /**
     * @param $rowArray
     * @param $key
     * @return array
     */
    private function parseArrayOutput($rowArray, $key)
    {
        $result = array();
        $start = false;
        foreach ($rowArray as $line) {
            if ($line === $key) {
                $start = true;
                continue;
            }
            if ($start) {
                if (empty($line)) {
                    break;
                }
                $parts = explode(' ', $line);
                $result[$parts[0]] = $parts[1];
            }
        }

        return $result;
    }
}
