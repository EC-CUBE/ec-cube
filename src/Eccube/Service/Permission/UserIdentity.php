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
 * 実行ユーザーの uid / gid.
 *
 * ext-posix は composer.json の require に含まれないため, ユーザー名は解決せず uid / gid のまま扱う.
 */
final readonly class UserIdentity
{
    /**
     * @param string $source uid / gid をどこから判定したかを示す説明 (診断結果の根拠として表示する)
     */
    public function __construct(
        public int $uid,
        public int $gid,
        public string $source,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'uid' => $this->uid,
            'gid' => $this->gid,
            'source' => $this->source,
        ];
    }
}
