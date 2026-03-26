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
     *
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
        $formData['log_level'] = '';
        $formData['keyword'] = '';

        $builder = $this->formFactory
            ->createBuilder(LogType::class);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'data' => $formData,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_LOG_INDEX_INITIALIZE);
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
            $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SYSTEM_LOG_INDEX_COMPLETE);
        }
        $logDir = $this->getParameter('kernel.logs_dir').DIRECTORY_SEPARATOR.$this->getParameter('kernel.environment');
        $logFile = $logDir.'/'.$formData['files'];

        if ($form->getClickedButton() && $form->getClickedButton()->getName() === 'download' && $form->isValid()) {
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

        // ログレベルの階層定義
        $levelHierarchy = [
            'DEBUG' => 100,
            'INFO' => 200,
            'NOTICE' => 250,
            'WARNING' => 300,
            'ERROR' => 400,
            'CRITICAL' => 500,
            'ALERT' => 550,
            'EMERGENCY' => 600,
        ];

        // 最小レベルの閾値を取得
        $minLevelThreshold = null;
        if (!empty($formData['log_level']) && isset($levelHierarchy[$formData['log_level']])) {
            $minLevelThreshold = $levelHierarchy[$formData['log_level']];
        }

        // キーワード（大文字小文字を区別しない）
        $keyword = !empty($formData['keyword']) ? mb_strtolower(trim($formData['keyword'])) : null;

        // ファイルを逆順で読み込み（新しいログが先）
        $lines = array_reverse(file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

        foreach ($lines as $line) {
            // 必要な件数に達したら終了
            if (count($log) >= $formData['line_max']) {
                break;
            }

            // ログレベルを抽出
            $level = $this->extractLogLevel($line);

            // 最小レベルフィルタを適用
            if ($minLevelThreshold !== null) {
                $lineLevel = isset($levelHierarchy[$level]) ? $levelHierarchy[$level] : 0;
                if ($lineLevel < $minLevelThreshold) {
                    continue;
                }
            }

            // キーワードフィルタを適用
            if ($keyword !== null && mb_strpos(mb_strtolower($line), $keyword) === false) {
                continue;
            }

            // フィルタリングされたエントリーを追加
            $log[] = [
                'raw' => $line,
                'level' => $level,
            ];
        }

        return $log;
    }

    /**
     * ログ行からログレベルを抽出
     *
     * @param string $line
     *
     * @return string ログレベル（DEBUG, INFO等）、見つからない場合は空文字列
     */
    private function extractLogLevel($line)
    {
        // Monologフォーマットにマッチする正規表現
        // 例: [2025-01-10 14:23:45] admin.ERROR
        if (preg_match('/\[\d{4}-\d{2}-\d{2}[^\]]*\]\s+\w+\.(DEBUG|INFO|NOTICE|WARNING|ERROR|CRITICAL|ALERT|EMERGENCY)/i', $line, $matches)) {
            return strtoupper($matches[1]);
        }

        // フォールバック: 標準フォーマット以外の場合
        $levels = ['EMERGENCY', 'ALERT', 'CRITICAL', 'ERROR', 'WARNING', 'NOTICE', 'INFO', 'DEBUG'];
        foreach ($levels as $level) {
            if (stripos($line, '.'.$level) !== false) {
                return $level;
            }
        }

        return '';
    }
}
