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

namespace Eccube\Log;

use Eccube\Request\Context;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;

class Logger extends AbstractLogger
{
    public function __construct(protected Context $context, protected LoggerInterface $logger, protected LoggerInterface $frontLogger, protected LoggerInterface $adminLogger)
    {
    }

    /**
     * ログ出力を行う。アクセスされている画面によりログ出力先を分けている。
     *
     * @param mixed $level
     * @param string $message
     * @param array<string, mixed> $context
     */
    #[\Override]
    public function log($level, $message, array $context = []): void
    {
        if ($this->context->isAdmin()) {
            $this->adminLogger->log($level, $message, $context);
        } elseif ($this->context->isFront()) {
            $this->frontLogger->log($level, $message, $context);
        } else {
            $this->logger->log($level, $message, $context);
        }
    }
}
