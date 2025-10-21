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

namespace Eccube\Service\Composer;

/**
 * Class OutputParser
 */
class OutputParser
{
    /**
     * Parse to array
     *
     * @param string $output
     *
     * @return array<string, array<string, string>>
     */
    public static function parseRequire($output): array
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $installedLogs = array_filter(
            array_map(
                function ($line) {
                    $matches = [];
                    preg_match('/^  - Installing (.*?) \((.*?)\) .*/', $line, $matches);

                    return $matches;
                },
                $rowArray
            )
        );

        // 'package name' => 'version'
        return ['installed' => array_column($installedLogs, 2, 1)];
    }

    /**
     * Parse to array
     *
     * @param string $output
     *
     * @return array<int|string,array<string, string>|string>
     */
    public static function parseInfo($output): array
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $infoLogs = array_filter(array_map(function ($line) {
            $matches = [];
            preg_match('/^(name|descrip.|keywords|versions|type|license|source|dist|names)\s*:\s*(.*)$/', $line, $matches);

            return $matches;
        }, $rowArray));

        // 'name' => 'value'
        $result = array_column($infoLogs, 2, 1);
        $result['requires'] = self::parseArrayInfoOutput($rowArray, 'requires');
        $result['requires (dev)'] = self::parseArrayInfoOutput($rowArray, 'requires (dev)');

        return $result;
    }

    /**
     * Parse to array
     *
     * @param string $output
     *
     * @return array<int|string, array<int, string>>|null
     */
    public static function parseConfig($output): ?array
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return !preg_match('/^<warning>.*/', $line);
        });

        return $rowArray ? json_decode(array_shift($rowArray), true) : [];
    }

    /**
     * Parse to array
     *
     * @param string $output
     *
     * @return array<mixed>
     */
    public static function parseList($output): array
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rawConfig = array_map(function ($line) {
            $matches = [];
            preg_match('/^\[(.*?)\]\s?(.*)$/', $line, $matches);

            return $matches;
        }, $rowArray);

        $rawConfig = array_column($rawConfig, 2, 1);

        $result = [];

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
     * @param array<mixed> $rowArray
     * @param string $key
     *
     * @return array<string, string>
     */
    private static function parseArrayInfoOutput($rowArray, $key): array
    {
        $result = [];
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
                $parts = explode(' ', (string) $line);
                $result[$parts[0]] = $parts[1];
            }
        }

        return $result;
    }

    /**
     * Parse to composer version
     *
     * @param string $output
     *
     * @return string|null
     */
    public static function parseComposerVersion($output): ?string
    {
        $rowArray = explode(PHP_EOL, str_replace('\r\n', PHP_EOL, $output));
        $rowArray = array_filter($rowArray, function ($line) {
            return preg_match('/^Composer */', $line);
        });

        return array_shift($rowArray);
    }
}
