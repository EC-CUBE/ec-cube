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
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\LogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=LogController::class)
 */
class LogController
{
    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Route("/{_admin}/setting/system/log", name="admin_setting_system_log")
     * @Template("Setting/System/log.twig")
     */
    public function index(Application $app, Request $request)
    {
        $formData = array();
        // default
        $formData['files'] = 'site_'.date('Y-m-d').'.log';
        $formData['line_max'] = '50';

        $builder = $this->formFactory
            ->createBuilder(LogType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'data' => $formData,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_LOG_INDEX_INITIALIZE, $event);
        $formData = $event->getArgument('data');

        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $formData = $form->getData();
            }
            $event = new EventArgs(
                array(
                    'form' => $form,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_LOG_INDEX_COMPLETE, $event);
        }

        $logFile = $this->appConfig['root_dir'].'/app/log/'.$formData['files'];

        return [
            'form' => $form->createView(),
            'log' => $this->parseLogFile($logFile, $formData),
        ];
    }

    private function parseLogFile($logFile, $formData)
    {
        $log = array();

        if (!file_exists($logFile)) {
            return $log;
        }

        foreach (array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) as $line) {
            // 上限に達した場合、処理を抜ける
            if (count($log) >= $formData['line_max']) {
                break;
            }

            $log[] = $line;
        }

        return $log;
    }
}
