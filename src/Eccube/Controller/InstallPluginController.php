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
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Service\SystemService;
use Eccube\Util\CacheUtil;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;

class InstallPluginController extends InstallController
{
    /** @var CacheUtil */
    protected $cacheUtil;

    /** @var PluginRepository */
    protected $pluginReposigoty;

    public function __construct(CacheUtil $cacheUtil, PluginRepository $pluginRespository)
    {
        $this->cacheUtil = $cacheUtil;
        $this->pluginReposigoty = $pluginRespository;
    }

    /**
     * 有効化可能なプラグイン一覧を返します.
     *
     * @Route("/install/plugins", name="install_plugins",  methods={"GET"})
     *
     * @param Request $request
     * @param string $code
     *
     * @return JsonResponse
     */
    public function plugins(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        // トランザクションチェックファイルの有効期限を確認する
        $token = $request->headers->get('ECCUBE-CSRF-TOKEN');
        if (!$this->isValidTransaction($token)) {
            throw new NotFoundHttpException();
        }

        $Plugins = $this->pluginReposigoty->findAll();

        return $this->json($Plugins);
    }

    /**
     * プラグインを有効にします。
     *
     * @Route("/install/plugin/{code}/enable", requirements={"code" = "\w+"}, name="install_plugin_enable",  methods={"PUT"})
     *
     * @param Request $request
     * @param SystemService $systemService
     * @param PluginService $pluginService
     * @param string $code
     * @param EventDispatcherInterface $dispatcher
     *
     * @return JsonResponse
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws PluginException
     */
    public function pluginEnable(Request $request, SystemService $systemService, PluginService $pluginService, $code, EventDispatcherInterface $dispatcher)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        // トランザクションチェックファイルの有効期限を確認する
        $token = $request->headers->get('ECCUBE-CSRF-TOKEN');
        if (!$this->isValidTransaction($token)) {
            throw new NotFoundHttpException();
        }

        /** @var Plugin $Plugin */
        $Plugin = $this->entityManager->getRepository(Plugin::class)->findOneBy(['code' => $code]);
        $log = null;
        // プラグインが存在しない場合は無視する
        if ($Plugin !== null) {
            $systemService->switchMaintenance(true); // auto_maintenanceと設定されたファイルを生成
            $systemService->disableMaintenance(SystemService::AUTO_MAINTENANCE);

            try {
                ob_start();

                if ($Plugin->isEnabled()) {
                    $pluginService->disable($Plugin);
                } else {
                    if (!$Plugin->isInitialized()) {
                        $pluginService->installWithCode($Plugin->getCode());
                    }
                    $pluginService->enable($Plugin);
                }
            } finally {
                $log = ob_get_clean();
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
            }

            // KernelEvents::TERMINATE で強制的にキャッシュを削除する
            // see https://github.com/EC-CUBE/ec-cube/issues/5498#issuecomment-1205904083
            $dispatcher->addListener(KernelEvents::TERMINATE, function () {
                $fs = new Filesystem();
                $fs->remove($this->getParameter('kernel.project_dir').'/var/cache/'.env('APP_ENV', 'prod'));
            });

            return $this->json(['success' => true, 'log' => $log]);
        } else {
            return $this->json(['success' => false, 'log' => $log]);
        }
    }

    /**
     * トランザクションファイルを削除し, 管理画面に遷移します.
     *
     * @Route("/install/plugin/redirect", name="install_plugin_redirect", methods={"GET"})
     *
     * @return RedirectResponse
     */
    public function redirectAdmin()
    {
        $this->cacheUtil->clearCache();

        // トランザクションファイルを削除する
        $projectDir = $this->getParameter('kernel.project_dir');
        $transaction = $projectDir.parent::TRANSACTION_CHECK_FILE;
        if (file_exists($transaction)) {
            unlink($transaction);
        }

        return $this->redirectToRoute('admin_login');
    }

    /**
     * トランザクションチェックファイルの有効期限を確認する
     *
     * @return bool
     */
    public function isValidTransaction($token)
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!file_exists($projectDir.parent::TRANSACTION_CHECK_FILE)) {
            return false;
        }

        $transaction_checker = file_get_contents($projectDir.parent::TRANSACTION_CHECK_FILE);
        list($expire, $validToken) = explode(':', $transaction_checker);
        if ($token !== $validToken) {
            return false;
        }

        return $expire >= time();
    }
}
