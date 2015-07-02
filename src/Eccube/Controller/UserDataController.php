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
use Eccube\Entity\Master\DeviceType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserDataController
{
    public function index(Application $app, $route)
    {
        $DeviceType = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(
            array('url' => $route, 'DeviceType' => $DeviceType)
        );

        if (is_null($PageLayout)) {
            throw new NotFoundHttpException();
        }

        // user_dataディレクトリを探索パスに追加.
        $paths = array();
        $paths[] = $app['config']['user_data_realdir'];
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

        $file = $PageLayout->getFileName() . '.twig';

        return $app['twig']->render($file);
    }
}
