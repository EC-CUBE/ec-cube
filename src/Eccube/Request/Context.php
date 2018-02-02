<?php

namespace Eccube\Request;

use Eccube\Common\EccubeConfig;
use Symfony\Component\HttpFoundation\RequestStack;

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

    public function __construct(RequestStack $requestStack, EccubeConfig $eccubeConfig)
    {
        $this->requestStack = $requestStack;
        $this->eccubeConfig = $eccubeConfig;
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
}
