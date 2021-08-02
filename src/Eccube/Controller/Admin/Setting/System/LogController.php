<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Setting\System;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\LogType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

class LogController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/setting/system/log", name="admin_setting_system_log", methods={"GET", "POST"})
     * @Template("@admin/Setting/System/log.twig")
     *
     * @return array|Symfony\Component\HttpFoundation\StreamedResponse
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

        if ($form->getClickedButton() && $form->getClickedButton()->getName() === 'download') {
            $bufferSize = 1024 * 50;
            $response = new StreamedResponse();
            $response->headers->set('Content-Length', filesize($logFile));
            $response->headers->set('Content-Disposition', 'attachment; filename='.basename($logFile));
            $response->headers->set('Content-Type', 'application/octet-stream');
            $response->setCallback(function () use ($logFile, $bufferSize) {
                if ($fh = fopen($logFile, 'r')) {
                    while (!feof($fh)) {
                        echo fread($fh, $bufferSize);
                    }
                }
            });
            $response->send();

            return $response;
        } else {
            return [
                'form' => $form->createView(),
                'log' => $this->parseLogFile($logFile, $formData),
            ];
        }
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
