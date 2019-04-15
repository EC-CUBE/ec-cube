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
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var LoggerInterface
     */
    protected $frontLogger;

    /**
     * @var LoggerInterface
     */
    protected $adminLogger;

    public function __construct(
        Context $context,
        LoggerInterface $logger,
        LoggerInterface $frontLogger,
        LoggerInterface $adminLogger
    ) {
        $this->context = $context;
        $this->logger = $logger;
        $this->frontLogger = $frontLogger;
        $this->adminLogger = $adminLogger;
    }

    /**
     * ログ出力を行う。アクセスされている画面によりログ出力先を分けている。
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = [])
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
