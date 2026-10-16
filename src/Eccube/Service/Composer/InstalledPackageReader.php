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
 * composer が導入済みのパッケージを記録したファイル (vendor/composer/installed.json) を読む.
 *
 * composer を実行すると書き換わるため, 呼ぶたびに読み直す.
 */
class InstalledPackageReader
{
    public function __construct(
        private readonly string $installedJsonPath,
    ) {
    }

    /**
     * 導入済みのパッケージを返す. パッケージ名の大文字小文字は区別しない.
     *
     * ファイルが無い・読めない場合は null を返す.
     *
     * @return array<string, mixed>|null
     */
    public function find(string $packageName): ?array
    {
        $json = @file_get_contents($this->installedJsonPath);
        if ($json === false) {
            return null;
        }
        $installed = json_decode($json, true);
        if (!is_array($installed)) {
            return null;
        }
        // composer 2 は packages の下に並べる. composer 1 の形式 (トップレベルが配列) も composer 自身と同じく読む
        $packages = $installed['packages'] ?? $installed;
        if (!is_array($packages)) {
            return null;
        }

        foreach ($packages as $package) {
            if (is_array($package) && is_string($package['name'] ?? null)
                && strtolower($package['name']) === strtolower($packageName)) {
                return $package;
            }
        }

        return null;
    }
}
