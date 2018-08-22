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

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Common\Constant;
use Eccube\Service\SystemService;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SystemController
{
    /**
     * @var SystemService
     */
    protected $systemService;

    /**
     * SystemController constructor.
     *
     * @param SystemService $systemService
     */
    public function __construct(SystemService $systemService)
    {
        $this->systemService = $systemService;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/system", name="admin_setting_system_system")
     * @Template("@admin/Setting/System/system.twig")
     */
    public function index(Request $request)
    {
        $info = [];
        $info[] = ['title' => 'EC-CUBE', 'value' => Constant::VERSION];
        $info[] = ['title' => trans('system.label.server_os'), 'value' => php_uname()];
        $info[] = ['title' => trans('system.label.db_server'), 'value' => $this->systemService->getDbversion()];
        $info[] = ['title' => trans('system.label.web_server'), 'value' => $request->server->get('SERVER_SOFTWARE')];

        $value = phpversion().' ('.implode(', ', get_loaded_extensions()).')';
        $info[] = ['title' => 'PHP', 'value' => $value];
        $info[] = ['title' => trans('system.label.user_agent'), 'value' => $request->headers->get('User-Agent')];

        return [
            'info' => $info,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/system/system/phpinfo", name="admin_setting_system_system_phpinfo")
     */
    public function phpinfo(Request $request)
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        return new Response($phpinfo);
    }
}
