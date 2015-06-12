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

use Eccube\Controller\AbstractController;
use Eccube\Application;

class PluginController extends AbstractController
{
    public function index(Application $app)
    {
        // 動作試験用
        //# ちゃんと画面を作るときに置き換えてください
        $service = $app['eccube.service.plugin'];
        $service->install('/SampleEntity.tar');

        #$em = $app['orm.em'];
        #$plugin=$em->getRepository('Eccube\Entity\Plugin')->getPluginByCode('SampleEntity');
        #$service->enable($plugin[0]);
        #$service->uninstall($plugin[0]);

        return "<hr>plugin install success<hr>";
    }
}
