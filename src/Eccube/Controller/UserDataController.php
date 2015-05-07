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


namespace Eccube\Controller;

use Eccube\Application;

class UserDataController
{

    public function index(Application $app)
    {
        $url = ltrim($app['request']->getRequestUri(), '/');
        $device_type_id = $this->getDeviceTypeId($app);
        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(
            array('url'=> $url, 'device_type_id' => $device_type_id)
        );

        // テンプレートファイルの取得
        $templatePath = $app['eccube.repository.page_layout']
            ->getTemplatePath($device_type_id, true);
        $file = $templatePath . $PageLayout->getFileName() . '.twig';

        return $app['twig']->render($file);
    }

    /**
     * FIXME: アクセスしたデバイスによっての切替を実装する？
     * @param $app
     * @return integer
     */
    public function getDeviceTypeId($app)
    {
        return $app['config']['device_type_pc'];
    }
}
