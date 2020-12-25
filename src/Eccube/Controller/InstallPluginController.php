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

namespace Eccube\Controller;

use Eccube\Controller\Install\InstallController;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginException;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Eccube\Util\CacheUtil;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class InstallPluginController extends InstallController
{
    /**
     * プラグインを有効にします。
     *
     * @Route("/install/plugin/enable", name="install_plugin_enable")
     *
     * @return JsonResponse
     *
     * @throws PluginException
     */
    public function pluginEnable(SystemService $systemService, PluginService $pluginService)
    {
        // トランザクションチェックファイルの有効期限を確認する
        if (!$this->isValidTransaction()) {
            throw new NotFoundHttpException();
        }

        $Plugin = $this->entityManager->getRepository(Plugin::class)->findOneBy(['code' => 'Api']);
        $log = null;
        // プラグインが存在しない場合は無視する
        if ($Plugin !== null) {
            $systemService->switchMaintenance(true); // auto_maintenanceと設定されたファイルを生成
            $systemService->disableMaintenance(SystemService::AUTO_MAINTENANCE);

            try {
                ob_start();

                $pluginService->installWithCode($Plugin->getCode());

                $pluginService->enable($Plugin);
            } finally {
                $log = ob_get_clean();
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
            }
        }

        return $this->json(['success' => true, 'log' => $log]);
    }

    /**
     * プラグインを有効にします。
     *
     * @Route("/install/cache/clear", name="install_cache_clear")
     *
     * @return JsonResponse
     */
    public function cacheClear(CacheUtil $cacheUtil)
    {
        // トランザクションチェックファイルの有効期限を確認する
        if (!$this->isValidTransaction()) {
            throw new NotFoundHttpException();
        }

        $cacheUtil->clearCache();

        // トランザクションファイルを削除する
        $projectDir = $this->getParameter('kernel.project_dir');
        unlink($projectDir.parent::TRANSACTION_CHECK_FILE);

        return $this->json(['success' => true]);
    }

    /**
     * トランザクションチェックファイルの有効期限を確認する
     *
     * @return bool
     */
    public function isValidTransaction()
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!file_exists($projectDir.parent::TRANSACTION_CHECK_FILE)) {
            return false;
        }

        $transaction_checker = file_get_contents($projectDir.parent::TRANSACTION_CHECK_FILE);

        return $transaction_checker >= time();
    }
}
