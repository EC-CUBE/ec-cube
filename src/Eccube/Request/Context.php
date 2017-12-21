<?php

namespace Eccube\Request;

use Symfony\Component\HttpFoundation\RequestStack;

class Context
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var string
     */
    protected $adminRoute;

    public function __construct(RequestStack $requestStack, $admin_route)  // TODO $eccubeConfigから取得/
    {
        $this->requestStack = $requestStack;
        $this->adminRoute = $admin_route;
    }

    /**
     * 管理画面へのアクセスかどうか.
     *
     * @return bool
     */
    public function isAdmin()
    {
        $request = $this->requestStack->getMasterRequest();

        $pathInfo = \rawurldecode($request->getPathInfo());
        $adminPath = '/'.\trim($this->adminRoute, '/').'/';

        return \strpos($pathInfo, $adminPath) === 0;
    }

    /**
     * フロント画面へのアクセスかどうか.
     *
     * @return bool
     */
    public function isFront()
    {
        return false === $this->isAdmin();
    }
}
