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
 * Class ConfigListParser
 * @package Eccube\Service\Composer
 */
class ConfigListParser
{
    private $output;

    /**
     * ConfigListParser constructor.
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
        $rawConfig = array_map(function($line) {
            $matches = array();
            preg_match('/^\[(.*?)\]\s?(.*)$/', $line, $matches);

            return $matches;
        }, $rowArray);

        $rawConfig = array_column($rawConfig, 2, 1);

        $result = array();

        foreach ($rawConfig as $path => $value) {
            $arr = &$result;
            $keys = explode('.', $path);
            foreach ($keys as $key) {
                $arr = &$arr[$key];
            }
            $arr = $value;
        }

        return $result;
    }
}
