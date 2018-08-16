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

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\LogType;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class LogController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/setting/system/log", name="admin_setting_system_log")
     * @Template("@admin/Setting/System/log.twig")
     *
     * @return array
     */
    public function index(Request $request)
    {
        $formData = [];
        // default
        $formData['files'] = 'site_'.date('Y-m-d').'.log';
        $formData['line_max'] = '50';

        $builder = $this->formFactory
            ->createBuilder(LogType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'data' => $formData,
            ],
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
                [
                    'form' => $form,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SYSTEM_LOG_INDEX_COMPLETE, $event);
        }
        $logDir = $this->getParameter('kernel.logs_dir').DIRECTORY_SEPARATOR.$this->getParameter('kernel.environment');
        $logFile = $logDir.'/'.$formData['files'];

        return [
            'form' => $form->createView(),
            'log' => $this->parseLogFile($logFile, $formData),
        ];
    }

    /**
     * parse log file
     *
     * @param string $logFile
     * @param $formData
     *
     * @return array
     */
    private function parseLogFile($logFile, $formData)
    {
        $log = [];

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
