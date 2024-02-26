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

namespace Eccube\Security\Http\Authentication;

use Eccube\Request\Context;
use Eccube\Service\SystemService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\HttpUtils;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;

class EccubeLogoutSuccessHandler extends DefaultLogoutSuccessHandler
{
    /** @var Context */
    protected $context;

    public function __construct(HttpUtils $httpUtils, Context $context, $targetUrl = '/')
    {
        parent::__construct($httpUtils, $targetUrl);
        $this->context = $context;
    }

    public function onLogoutSuccess(Request $request)
    {
        $response = parent::onLogoutSuccess($request);

        if ($this->context->isAdmin()) {
            $response->headers->clearCookie(SystemService::MAINTENANCE_TOKEN_KEY);
        }

        return $response;
    }
}
