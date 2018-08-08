<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Request;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Customer;
use Eccube\Entity\Member;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Context
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(RequestStack $requestStack, EccubeConfig $eccubeConfig, TokenStorage $tokenStorage)
    {
        $this->requestStack = $requestStack;
        $this->eccubeConfig = $eccubeConfig;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * 管理画面へのアクセスかどうか.
     *
     * @return bool
     */
    public function isAdmin()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return false;
        }

        $pathInfo = \rawurldecode($request->getPathInfo());
        $adminPath = $this->eccubeConfig->get('eccube_admin_route');
        $adminPath = '/'.\trim($adminPath, '/').'/';

        return \strpos($pathInfo, $adminPath) === 0;
    }

    /**
     * フロント画面へのアクセスかどうか.
     *
     * @return bool
     */
    public function isFront()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return false;
        }

        return false === $this->isAdmin();
    }

    /**
     * @return Member|Customer|null
     */
    public function getCurrentUser()
    {
        $request = $this->requestStack->getMasterRequest();

        if (null === $request) {
            return null;
        }

        return $this->tokenStorage->getToken()->getUser();
    }
}
