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
 * Class OutputParser
 * @package Eccube\Service\Composer
 */
class OutputParser
{
    /**
     * Parse to array
     *
     * @param string $output
     * @return array
     */
    public static function parseRequire($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $installedLogs = array_filter(
            array_map(
                function ($line) {
                    $matches = array();
                    preg_match('/^  - Installing (.*?) \((.*?)\) .*/', $line, $matches);

                    return $matches;
                },
                $rowArray
            )
        );

        // 'package name' => 'version'
        return array('installed' => array_column($installedLogs, 2, 1));
    }

    /**
     * Parse to array
     *
     * @param string $output
     * @return array
     */
    public static function parseInfo($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $infoLogs = array_filter(array_map(function ($line) {
            $matches = array();
            preg_match('/^(name|descrip.|keywords|versions|type|license|source|dist|names)\s*:\s*(.*)$/', $line, $matches);

            return $matches;
        }, $rowArray));

        // 'name' => 'value'
        $result = array_column($infoLogs, 2, 1);
        $result['requires'] = static::parseArrayInfoOutput($rowArray, 'requires');
        $result['requires (dev)'] = static::parseArrayInfoOutput($rowArray, 'requires (dev)');

        return $result;
    }

    /**
     * Parse to array
     *
     * @param string $output
     * @return array|mixed
     */
    public static function parseConfig($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return !preg_match('/^<warning>.*/', $line);
        });

        return $rowArray ? json_decode(array_shift($rowArray), true) : array();
    }

    /**
     * Parse to array
     *
     * @param string $output
     * @return array
     */
    public static function parseList($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rawConfig = array_map(function ($line) {
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

    /**
     * @param $rowArray
     * @param $key
     * @return array
     */
    private static function parseArrayInfoOutput($rowArray, $key)
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

    /**
     * Parse to composer version
     * @param string $output
     * @return array|mixed|string
     */
    public static function parseComposerVersion($output)
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return !preg_match('/^<warning>.*/', $line);
        });

        return array_shift($rowArray);
    }
}
