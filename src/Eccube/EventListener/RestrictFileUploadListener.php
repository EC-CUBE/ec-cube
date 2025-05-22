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

namespace Eccube\EventListener;

use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RestrictFileUploadListener implements EventSubscriberInterface
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var Context
     */
    protected $requestContext;

    public function __construct(EccubeConfig $eccubeConfig, Context $requestContext)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->requestContext = $requestContext;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->requestContext->isAdmin()) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');
        $restrictUrls = $this->eccubeConfig['eccube_restrict_file_upload_urls'];
        if ($this->eccubeConfig['eccube_restrict_file_upload'] === '1' && in_array($route, $restrictUrls)) {
            throw new AccessDeniedHttpException(trans('exception.error_message_restrict_url'));
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => ['onKernelRequest', 7], // RouterListener より必ず後で実行する
        ];
    }
}
