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

namespace Eccube\Tests;

/**
 * 権限による書き込み不可を再現するテストのためのヘルパ.
 *
 * root はパーミッションビットを無視するため, 書き込み不可を前提としたテストは成立しない.
 * 実効 uid の判定に getmyuid() は使えない. 返るのは実行プロセスではなく
 * スクリプトファイルの所有者で, root で非 root 所有の作業ツリーを実行した場合
 * (bind mount した checkout を root のコンテナで動かす等) に root を検出できない.
 *
 * @see \Eccube\Service\Permission\WebServerUserResolver
 */
trait EffectiveUserTrait
{
    /**
     * root で実行している場合にテストをスキップする.
     *
     * ext-posix が無い環境では実効 uid を確認できないため, 同じくスキップする
     * (root かどうか分からないまま検証すると偽陽性・偽陰性のどちらも起こりうる).
     */
    protected function skipIfRoot(): void
    {
        if (!function_exists('posix_geteuid')) {
            self::markTestSkipped('ext-posix が無いため実効 uid を確認できません.');
        }

        if (0 === posix_geteuid()) {
            self::markTestSkipped('root は書き込み権限の検査を通過するため検証できません.');
        }
    }
}
