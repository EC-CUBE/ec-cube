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

namespace Eccube\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class EventArgs extends GenericEvent
{
    /**
     * @var Request|null
     */
    private ?Request $request = null;
    /**
     * @var Response|null
     */
    private ?Response $response = null;

    /**
     * EventArgs constructor.
     *
     * @param array<mixed> $arguments
     */
    public function __construct(array $arguments = [], ?Request $request = null)
    {
        parent::__construct(null, $arguments);
        $this->request = $request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function getRequest(): ?Request
    {
        return $this->request;
    }

    public function setResponse(?Response $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function hasResponse(): bool
    {
        return $this->response !== null;
    }
}
