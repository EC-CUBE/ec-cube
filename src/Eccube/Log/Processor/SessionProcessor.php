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

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionProcessor
{
    /**
     * @var SessionInterface
     */
    protected $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function __invoke(array $records)
    {
        $records['extra']['session_id'] = 'N/A';

        if (!$this->session->isStarted()) {
            return $records;
        }

        $records['extra']['session_id'] = substr(sha1($this->session->getId()), 0, 8);

        return $records;
    }
}
