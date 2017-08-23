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

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Service\SystemService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=SystemController::class)
 */
class SystemController
{
    /**
     * @Inject(SystemService::class)
     * @var SystemService
     */
    protected $systemService;

    /**
     * @Route("/{_admin}/setting/system/system", name="admin_setting_system_system")
     * @Template("Setting/System/system.twig")
     */
    public function index(Application $app, Request $request)
    {
        $info = [];
        $info[] = ['title' => 'EC-CUBE', 'value' => Constant::VERSION];
        $info[] = ['title' => 'サーバーOS', 'value' => php_uname()];
        $info[] = ['title' => 'DBサーバー', 'value' => $this->systemService->getDbversion()];
        $info[] = ['title' => 'WEBサーバー', 'value' => $request->server->get("SERVER_SOFTWARE")];

        $value = phpversion().' ('.implode(', ', get_loaded_extensions()).')';
        $info[] = ['title' => 'PHP', 'value' => $value];
        $info[] = ['title' => 'HTTPユーザーエージェント', 'value' => $request->headers->get('User-Agent')];

        return [
            'info' => $info,
        ];
    }

    /**
     * @Route("/{_admin}/setting/system/system/phpinfo", name="admin_setting_system_system_phpinfo")
     */
    public function phpinfo(Application $app, Request $request)
    {
        ob_start();
        phpinfo();
        $phpinfo = ob_get_contents();
        ob_end_clean();

        return $phpinfo;
    }
}
