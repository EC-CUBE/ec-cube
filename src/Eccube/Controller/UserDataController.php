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

use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\PageLayoutRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig_Loader_Chain;

/**
 * @Component
 * @Route(service=UserDataController::class)
 */
class UserDataController
{
    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("twig.loader")
     * @var Twig_Loader_Chain
     */
    protected $twigLoaderChain;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(PageLayoutRepository::class)
     * @var PageLayoutRepository
     */
    protected $pageLayoutRepository;

    /**
     * @Inject(DeviceTypeRepository::class)
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @Route("/{_user_data}/{route}", name="user_data", requirements={"route": "([0-9a-zA-Z_\-]+\/?)+(?<!\/)"})
     */
    public function index(Application $app, Request $request, $route)
    {
        $DeviceType = $this->deviceTypeRepository
            ->find(DeviceType::DEVICE_TYPE_PC);

        $PageLayout = $this->pageLayoutRepository->findOneBy(
            array(
                'url' => $route,
                'DeviceType' => $DeviceType,
                'edit_flg' => PageLayout::EDIT_FLG_USER,
            )
        );

        if (is_null($PageLayout)) {
            throw new NotFoundHttpException();
        }

        // user_dataディレクトリを探索パスに追加.
        $paths = array();
        $paths[] = $this->appConfig['user_data_realdir'];
        $this->twigLoaderChain->addLoader(new \Twig_Loader_Filesystem($paths));

        $file = $PageLayout->getFileName().'.twig';

        $event = new EventArgs(
            array(
                'DeviceType' => $DeviceType,
                'PageLayout' => $PageLayout,
                'file' => $file,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::FRONT_USER_DATA_INDEX_INITIALIZE, $event);

        return $app->render($file);
    }
}
