<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserDataController
{
    public function index(Application $app, Request $request, $route)
    {
        $DeviceType = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $app['eccube.repository.page_layout']->findOneBy(array(
            'url' => $route,
            'DeviceType' => $DeviceType,
            'edit_flg' => PageLayout::EDIT_FLG_USER,
        ));

        if (is_null($PageLayout)) {
            throw new NotFoundHttpException();
        }

        // user_dataディレクトリを探索パスに追加.
        $paths = array();
        $paths[] = $app['config']['user_data_realdir'];
        $app['twig.loader']->addLoader(new \Twig_Loader_Filesystem($paths));

        $file = $PageLayout->getFileName() . '.twig';

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'PageLayout' => $PageLayout,
                'file' => $file,
            ),
            $request
        );
        $app['eccube.event.dispatcher']->dispatch(EccubeEvents::FRONT_USER_DATA_INDEX_INITIALIZE, $event);

        return $app->render($file);
    }
}
