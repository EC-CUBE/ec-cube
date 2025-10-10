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

namespace Eccube\Log\Processor;

use Eccube\Session\Session;
use Monolog\LogRecord;
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;

class SessionProcessor
{
    /**
     * @var Session
     */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @param LogRecord $record
     *
     * @return LogRecord
     */
    public function __invoke(LogRecord $record): LogRecord
    {
        $sessionId = 'N/A';

        try {
            if ($this->session->isStarted()) {
                $sessionId = substr(sha1($this->session->getId()), 0, 8);
            }
        } catch (SessionNotFoundException) {
            // Keep default 'N/A'
        }

        return $record->with(extra: array_merge($record->extra, ['session_id' => $sessionId]));
    }
}
