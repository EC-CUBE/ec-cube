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

namespace Eccube\Asset;

use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

/**
 * ファイルの最終更新時刻をバージョンとして使用するバージョン戦略
 */
class FilemtimeVersionStrategy implements VersionStrategyInterface
{
    /**
     * @var string
     */
    private $basePath;

    /**
     * @param string $basePath アセットファイルのベースパス
     */
    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, '/');
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(string $path): string
    {
        // パスの先頭のスラッシュを削除
        $path = ltrim($path, '/');
        $fullPath = $this->basePath.'/'.$path;

        // ファイルが存在する場合のみバージョンを返す
        if (@is_file($fullPath)) {
            $mtime = @filemtime($fullPath);
            if (false !== $mtime) {
                return (string) $mtime;
            }
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function applyVersion(string $path): string
    {
        $version = $this->getVersion($path);

        if ('' === $version) {
            return $path;
        }

        return sprintf('%s?v=%s', $path, $version);
    }
}
