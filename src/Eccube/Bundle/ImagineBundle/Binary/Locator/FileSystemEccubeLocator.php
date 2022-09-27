<?php

namespace Eccube\Bundle\ImagineBundle\Binary\Locator;

use Liip\ImagineBundle\Binary\Locator\FileSystemLocator;

class FileSystemEccubeLocator extends FileSystemLocator
{
    protected function generateAbsolutePath(string $root, string $path): ?string
    {
        if (false !== $absolute = realpath($root . DIRECTORY_SEPARATOR . $path)) {
            return $absolute;
        }

        $path = explode(DIRECTORY_SEPARATOR, $path);
        $path = array_merge(array_diff(explode(DIRECTORY_SEPARATOR, $root), $path), $path);
        if (false !== $absolute = realpath(implode(DIRECTORY_SEPARATOR, $path))) {
            return $absolute;
        }

        return null;
    }
}
