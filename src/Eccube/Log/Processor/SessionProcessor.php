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
use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function __invoke(array $records)
    {
        $records['extra']['session_id'] = 'N/A';

        try {
            if (!$this->session->isStarted()) {
                return $records;
            }
        } catch (SessionNotFoundException $e) {
            return $records;
        }

        $records['extra']['session_id'] = substr(sha1($this->session->getId()), 0, 8);

        return $records;
    }
}
