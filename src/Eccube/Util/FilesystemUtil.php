<?php
namespace Eccube\Util;

class FilesystemUtil
{
    /**
     * Format file size to human readable
     * @param $size
     * @param int $decimals
     *
     * @return string
     */
    public static function sizeToHumanReadable($size, $decimals = 0)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        $power = $size > 0 ? floor(log($size, 1024)) : 0;

        return number_format($size / pow(1024, $power), $decimals, '.', ',') . ' ' . $units[$power];
    }
}