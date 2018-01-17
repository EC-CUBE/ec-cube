<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
    public function log($level, $message, array $context = array())
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
