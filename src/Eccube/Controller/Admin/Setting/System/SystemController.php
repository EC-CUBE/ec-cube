<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Common\Constant;
use Eccube\Service\SystemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route(service=SystemController::class)
 */
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
