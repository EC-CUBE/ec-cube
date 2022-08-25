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

use Symfony\Component\HttpFoundation\Exception\SessionNotFoundException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionProcessor
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function __invoke(array $records)
    {
        $records['extra']['session_id'] = 'N/A';

        try {
            $session = $this->requestStack->getSession();
        } catch (SessionNotFoundException $e) {
            return $records;
        }

        if (!$session->isStarted()) {
            return $records;
        }

        $records['extra']['session_id'] = substr(sha1($session->getId()), 0, 8);

        return $records;
    }
}
