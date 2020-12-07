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
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Eccube\Util\CacheUtil;
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
    public function pluginEnable(CacheUtil $cacheUtil, SystemService $systemService, PluginService $pluginService)
    {
        // トランザクションチェックファイルの有効期限を確認する
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!file_exists($projectDir.parent::TRANSACTION_CHECK_FILE)) {
            throw new NotFoundHttpException();
        }

        $transaction_checker = file_get_contents($projectDir.parent::TRANSACTION_CHECK_FILE);
        if ($transaction_checker < time()) {
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
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!file_exists($projectDir.parent::TRANSACTION_CHECK_FILE)) {
            throw new NotFoundHttpException();
        }

        $transaction_checker = file_get_contents($projectDir.parent::TRANSACTION_CHECK_FILE);
        if ($transaction_checker < time()) {
            throw new NotFoundHttpException();
        }

        $cacheUtil->clearCache();

        unlink($projectDir.parent::TRANSACTION_CHECK_FILE);

        return $this->json(['success' => true]);
    }
}
