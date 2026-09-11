<?php

declare(strict_types=1);

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

namespace Eccube\Cache;

/**
 * 書き込めないキャッシュ領域への保存を諦める.
 *
 * 権限を分離した構成では, 実行時キャッシュ (%eccube_runtime_dir%/pools) は Web サーバー所有
 * (レーン W) になるため CLI からは書き込めない. Symfony のアダプタは保存に失敗するたびに
 * 警告を記録するため, CLI を実行するたびに大量の警告が並ぶ.
 *
 * 保存できないことは権限で決まっており実行時には解消できないため, 書き込みは行わない.
 * 読み取りは従来どおり委譲するので, Web サーバーが生成したキャッシュは利用できる.
 * キャッシュの生成・削除は権限のあるユーザー (sudo -u www-data) 側で行う.
 */
trait WriteFailsafeTrait
{
    private bool $cacheWritable = true;

    /**
     * @param string|null $directory キャッシュの出力先 (null なら PHP の一時ディレクトリ)
     */
    private function initWriteFailsafe(?string $directory): void
    {
        if (null === $directory) {
            return;
        }

        // 未作成のことがあるため, 存在する直近の親ディレクトリで判定する
        $path = $directory;
        while (!file_exists($path)) {
            $parent = \dirname($path);
            if ($parent === $path) {
                break;
            }
            $path = $parent;
        }

        $this->cacheWritable = is_writable($path);
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return array<int, string>|bool
     */
    #[\Override]
    protected function doSave(array $values, int $lifetime): array|bool
    {
        return $this->cacheWritable ? parent::doSave($values, $lifetime) : true;
    }
}
