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

namespace Eccube\Log;

use Monolog\Handler\HandlerInterface;
use Monolog\Handler\HandlerWrapper;
use Monolog\LogRecord;

/**
 * CLI 実行時にファイルへの書き込みを抑止するハンドラ.
 *
 * Web サーバーと CLI で書き込み権限を分離すると, var/log は Web サーバー所有 (レーン W) に
 * なるため CLI からは書き込めない. ログの出力に失敗すると本来のエラーがログ書き込みエラーへ
 * すり替わり, 原因の特定が難しくなる.
 *
 * 本ハンドラは委譲先を呼ばないことで, ファイルのオープン (ディレクトリの作成を含む) 自体を
 * 発生させない. Monolog のストリームは書き込み時に遅延オープンされるため, handle() を
 * 通さなければ権限エラーは起きない.
 *
 * CLI のログはコンソールハンドラ (標準出力) へ出るため, cron 等で記録を残す場合は
 * リダイレクトする.
 *
 * @see \Eccube\DependencyInjection\Compiler\CliFileLogHandlerPass 適用箇所
 */
class CliSuppressibleHandler extends HandlerWrapper
{
    private readonly bool $enabled;

    /**
     * @param bool $logToFileInCli CLI 実行時もファイルへ書き込むか (ECCUBE_CLI_LOG_TO_FILE)
     */
    public function __construct(HandlerInterface $handler, bool $logToFileInCli)
    {
        parent::__construct($handler);

        $this->enabled = $logToFileInCli || \PHP_SAPI !== 'cli';
    }

    #[\Override]
    public function isHandling(LogRecord $record): bool
    {
        return $this->enabled && parent::isHandling($record);
    }

    #[\Override]
    public function handle(LogRecord $record): bool
    {
        return $this->enabled && parent::handle($record);
    }

    /**
     * @param array<int, LogRecord> $records
     */
    #[\Override]
    public function handleBatch(array $records): void
    {
        if ($this->enabled) {
            parent::handleBatch($records);
        }
    }
}
