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

        for ($i = 0; $i < count(explode(DIRECTORY_SEPARATOR, $root)); $i++) {
            $path = '..' . DIRECTORY_SEPARATOR . $path;
            if (false !== $absolute = realpath($root . DIRECTORY_SEPARATOR . $path)) {
                return $absolute;
            }
        }

        return null;
    }
}
