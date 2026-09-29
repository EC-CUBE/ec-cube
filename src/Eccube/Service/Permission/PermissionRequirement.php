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

namespace Eccube\Service\Permission;

/**
 * 1 パスに対する権限の期待値.
 */
final readonly class PermissionRequirement
{
    /**
     * @param string      $path     判定対象の絶対パス
     * @param WriteLane   $lane     期待するレーン
     * @param string      $label    表示用のプロジェクトルート相対パス
     * @param bool        $optional 実行時に生成されるため, 未作成でも問題としない
     * @param string|null $note     運用上の注意書き
     */
    public function __construct(
        public string $path,
        public WriteLane $lane,
        public string $label,
        public bool $optional = false,
        public ?string $note = null,
    ) {
    }
}
