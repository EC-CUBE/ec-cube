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
use Symfony\Component\Routing\Attribute\Route;

class InstallPluginController extends InstallController
{
    public function __construct(protected CacheUtil $cacheUtil, protected PluginRepository $pluginReposigoty, protected EventDispatcherInterface $eventDispatcher, private readonly SystemService $systemService, private readonly PluginService $pluginService)
    {
    }

    /**
     * 有効化可能なプラグイン一覧を返します.
     */
    #[Route(path: '/install/plugins', name: 'install_plugins', methods: ['GET'])]
    public function plugins(Request $request): JsonResponse
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
     * @param string $code
     *
     * @throws BadRequestHttpException
     * @throws NotFoundHttpException
     * @throws PluginException
     */
    #[Route(path: '/install/plugin/{code}/enable', name: 'install_plugin_enable', requirements: ['code' => '\w+'], methods: ['PUT'])]
    public function pluginEnable(Request $request, $code): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        // トランザクションチェックファイルの有効期限を確認する
        $token = $request->headers->get('ECCUBE-CSRF-TOKEN');
        if (!$this->isValidTransaction($token)) {
            throw new NotFoundHttpException();
        }

        /** @var Plugin|null $Plugin */
        $Plugin = $this->entityManager->getRepository(Plugin::class)->findOneBy(['code' => $code]);
        $log = null;
        // プラグインが存在しない場合は無視する
        if ($Plugin !== null) {
            $this->systemService->switchMaintenance(true); // auto_maintenanceと設定されたファイルを生成
            $this->systemService->disableMaintenance(SystemService::AUTO_MAINTENANCE);

            try {
                ob_start();

                if ($Plugin->isEnabled()) {
                    $this->pluginService->disable($Plugin);
                } else {
                    if (!$Plugin->isInitialized()) {
                        $this->pluginService->installWithCode($Plugin->getCode());
                    }
                    $this->pluginService->enable($Plugin);
                }
            } finally {
                $log = ob_get_clean();
                while (ob_get_level() > 0) {
                    ob_end_flush();
                }
            }

            $this->clearCacheOnTerminate();

            return $this->json(['success' => true, 'log' => $log]);
        }

        return $this->json(['success' => false, 'log' => $log]);
    }

    /**
     * トランザクションファイルを削除し, 管理画面に遷移します.
     */
    #[Route(path: '/install/plugin/redirect', name: 'install_plugin_redirect', methods: ['GET'])]
    public function redirectAdmin(Request $request): RedirectResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        $this->cacheUtil->clearCache();
        // トランザクションチェックファイルの有効期限を確認する
        $token = $request->headers->get('ECCUBE-CSRF-TOKEN');
        if (!$this->isValidTransaction($token)) {
            throw new NotFoundHttpException();
        }

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
     */
    public function isValidTransaction(string $token): bool
    {
        $projectDir = $this->getParameter('kernel.project_dir');
        if (!file_exists($projectDir.parent::TRANSACTION_CHECK_FILE)) {
            return false;
        }

        $transaction_checker = file_get_contents($projectDir.parent::TRANSACTION_CHECK_FILE);
        [$expire, $validToken] = explode(':', $transaction_checker);
        if ($token !== $validToken) {
            return false;
        }

        return $expire >= time();
    }

    /**
     * WebApiプラグインのシステム要件をチェックする
     *
     * かつては sodium 拡張が無い環境で WebApi プラグイン (ec-cube/api42) を自動アンインストールしていたが、
     * 同プラグインの実行時 (OAuth2 トークンの署名・検証) は RSA + openssl で処理し sodium 関数を呼ばないため、
     * sodium 拡張が無くても動作する。composer の install 時の platform チェックは composer.json の
     * config.platform.ext-sodium で満たすため、sodium 非対応の共有レンタルサーバーでも導入・維持できる (#6827)。
     * 本エンドポイントはインストーラ画面 (install/complete.twig) からの呼び出し互換のため残しているが、
     * 要件不適合によるアンインストールは行わない。
     *
     * @throws BadRequestHttpException|NotFoundHttpException
     */
    #[Route(path: '/install/plugin/check_api', name: 'install_plugin_check_api', methods: ['PUT'])]
    public function checkWebApiRequirements(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new BadRequestHttpException();
        }

        // トランザクションチェックファイルの有効期限を確認する
        $token = $request->headers->get('ECCUBE-CSRF-TOKEN');
        if (!$this->isValidTransaction($token)) {
            throw new NotFoundHttpException();
        }

        return $this->json(['success' => true]);
    }

    private function clearCacheOnTerminate(): void
    {
        // KernelEvents::TERMINATE で強制的にキャッシュを削除する
        // see https://github.com/EC-CUBE/ec-cube/issues/5498#issuecomment-1205904083
        $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function (): void {
            $projectDir = $this->getParameter('kernel.project_dir');
            $env = env('APP_ENV', 'prod');
            $fs = new Filesystem();
            // ビルド生成物 (var/build) と実行時キャッシュ (var/runtime) は別ディレクトリのため,
            // インストール直後のプラグインを反映するには双方を削除する必要がある.
            $fs->remove([
                $projectDir.'/var/cache/'.$env,
                $projectDir.'/var/build/'.$env,
                $projectDir.'/var/runtime/'.$env,
            ]);
        });
    }
}
