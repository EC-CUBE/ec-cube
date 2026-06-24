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

namespace Eccube\Tests\Doctrine\Logging;

use Psr\Log\AbstractLogger;

/**
 * DBAL 4 の Doctrine\DBAL\Logging\Middleware から渡されるログを集計し,
 * 実行されたクエリ数をカウントするテスト用ロガー.
 *
 * DBAL 3 までの Doctrine\DBAL\Logging\DebugStack の代替として利用する.
 */
class QueryCountLogger extends AbstractLogger
{
    private int $count = 0;

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $message = (string) $message;
        // Logging\Connection / Logging\Statement はクエリ実行時に
        // 「Executing query: ...」「Executing statement: ...」を debug 出力する.
        if (str_starts_with($message, 'Executing query') || str_starts_with($message, 'Executing statement')) {
            $this->count++;
        }
    }

    /**
     * これまでに実行されたクエリ数を返す.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * カウンタをリセットする.
     */
    public function reset(): void
    {
        $this->count = 0;
    }
}
