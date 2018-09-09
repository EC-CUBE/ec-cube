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

namespace Eccube\Controller\Admin\Store;

use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Plugin;
use Eccube\Exception\PluginApiException;
use Eccube\Exception\PluginException;
use Eccube\Form\Type\Admin\AuthenticationType;
use Eccube\Form\Type\Admin\PluginLocalInstallType;
use Eccube\Form\Type\Admin\PluginManagementType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\Composer\ComposerApiService;
use Eccube\Service\PluginApiService;
use Eccube\Service\PluginService;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PluginController extends AbstractController
{
    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * @var PluginApiService
     */
    protected $pluginApiService;
    /**
     * @var ComposerApiService
     */
    private $composerApiService;

    /**
     * PluginController constructor.
     *
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * @param BaseInfoRepository $baseInfoRepository
     * @param PluginApiService $pluginApiService
     *
     * @param ComposerApiService $composerApiService
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(PluginRepository $pluginRepository, PluginService $pluginService, BaseInfoRepository $baseInfoRepository, PluginApiService $pluginApiService, ComposerApiService $composerApiService)
    {
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->pluginApiService = $pluginApiService;
        $this->composerApiService = $composerApiService;
    }

    /**
     * インストール済プラグイン画面
     *
     * @Route("/%eccube_admin_route%/store/plugin", name="admin_store_plugin")
     * @Template("@admin/Store/plugin.twig")
     *
     * @return array
     *
     * @throws PluginException
     */
    public function index()
    {
        $pluginForms = [];
        $configPages = [];
        $Plugins = $this->pluginRepository->findBy([], ['code' => 'ASC']);

        // ファイル設置プラグインの取得.
        $unregisteredPlugins = $this->getUnregisteredPlugins($Plugins);
        $unregisteredPluginsConfigPages = [];
        foreach ($unregisteredPlugins as $unregisteredPlugin) {
            try {
                $code = $unregisteredPlugin['code'];
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $unregisteredPluginsConfigPages[$code] = $this->generateUrl('plugin_'.$code.'_config');
            } catch (RouteNotFoundException $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
        }

        $officialPlugins = [];
        $unofficialPlugins = [];

        foreach ($Plugins as $Plugin) {
            $form = $this->formFactory
                ->createNamedBuilder(
                    'form'.$Plugin->getId(),
                    PluginManagementType::class,
                    null,
                    [
                        'plugin_id' => $Plugin->getId(),
                    ]
                )
                ->getForm();
            $pluginForms[$Plugin->getId()] = $form->createView();

            try {
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $configPages[$Plugin->getCode()] = $this->generateUrl(Container::underscore($Plugin->getCode()).'_admin_config');
            } catch (\Exception $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
            if ($Plugin->getSource() == 0) {
                // 商品IDが設定されていない場合、非公式プラグイン
                $unofficialPlugins[] = $Plugin;
            } else {
                $officialPlugins[$Plugin->getSource()] = $Plugin;
            }
        }

        // オーナーズストア通信
        $officialPluginsDetail = [];
        try {
            $data = $this->pluginApiService->getPurchased();
            foreach ($data as $item) {
                if (isset($officialPlugins[$item['id']]) === false) {
                    $Plugin = new Plugin();
                    $Plugin->setName($item['name']);
                    $Plugin->setCode($item['code']);
                    $Plugin->setVersion($item['version']);
                    $Plugin->setSource($item['id']);
                    $Plugin->setEnabled(false);
                    $officialPlugins[$item['id']] = $Plugin;
                }
                $officialPluginsDetail[$item['id']] = $item;
            }
        } catch (PluginApiException $e) {
            $this->addWarning($e->getMessage(), 'admin');
        }

        return [
            'plugin_forms' => $pluginForms,
            'officialPlugins' => $officialPlugins,
            'unofficialPlugins' => $unofficialPlugins,
            'configPages' => $configPages,
            'unregisteredPlugins' => $unregisteredPlugins,
            'unregisteredPluginsConfigPages' => $unregisteredPluginsConfigPages,
            'officialPluginsDetail' => $officialPluginsDetail,
        ];
    }

    /**
     * インストール済プラグインからのアップデート
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/update", requirements={"id" = "\d+"}, name="admin_store_plugin_update", methods={"POST"})
     *
     * @param Request $request
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
     */
    public function update(Request $request, Plugin $Plugin)
    {
        $form = $this->formFactory
            ->createNamedBuilder(
                'form'.$Plugin->getId(),
                PluginManagementType::class,
                null,
                [
                    'plugin_id' => null, // placeHolder
                ]
            )
            ->getForm();

        $message = '';
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tmpDir = null;
            try {
                $formFile = $form['plugin_archive']->getData();
                $tmpDir = $this->pluginService->createTempDir();
                $tmpFile = sha1(StringUtil::random(32)).'.'.$formFile->getClientOriginalExtension();
                $formFile->move($tmpDir, $tmpFile);
                $this->pluginService->update($Plugin, $tmpDir.'/'.$tmpFile);
                $fs = new Filesystem();
                $fs->remove($tmpDir);
                $this->addSuccess('admin.plugin.update.complete', 'admin');

                return $this->redirectToRoute('admin_store_plugin');
            } catch (PluginException $e) {
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                $message = $e->getMessage();
            } catch (\Exception $er) {
                // Catch composer install error | Other error
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                log_error('plugin install failed.', ['original-message' => $er->getMessage()]);
                $message = 'admin.plugin.install.fail';
            }
        } else {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $message = $error->getMessage();
            }
        }

        $this->addError($message, 'admin');

        return $this->redirectToRoute('admin_store_plugin');
    }

    /**
     * 対象のプラグインを有効にします。
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/enable", requirements={"id" = "\d+"}, name="admin_store_plugin_enable", methods={"POST"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse|JsonResponse
     *
     * @throws PluginException
     */
    public function enable(Plugin $Plugin, CacheUtil $cacheUtil, Request $request)
    {
        $this->isTokenValid();

        $log = null;

        if ($Plugin->isEnabled()) {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => true]);
            } else {
                $this->addError('admin.plugin.already.enable', 'admin');
            }
        } else {
            // ストアからインストールしたプラグインは依存プラグインが有効化されているかを確認
            if ($Plugin->getSource()) {
                $requires = $this->pluginService->getPluginRequired($Plugin);
                $requires = array_filter($requires, function ($req) {
                    $code = preg_replace('/^ec-cube\//', '', $req['name']);
                    /** @var Plugin $DependPlugin */
                    $DependPlugin = $this->pluginRepository->findOneBy(['code' => $code]);

                    return $DependPlugin->isEnabled() == false;
                });
                if (!empty($requires)) {
                    $names = array_map(function ($req) {
                        return "「${req['description']}」";
                    }, $requires);
                    $message = trans('%depend_name%を先に有効化してください。', ['%name%' => $Plugin->getName(), '%depend_name%' => implode(', ', $names)]);

                    if ($request->isXmlHttpRequest()) {
                        return $this->json(['success' => false, 'message' => $message], 400);
                    } else {
                        $this->addError($message, 'admin');

                        return $this->redirectToRoute('admin_store_plugin');
                    }
                }
            }

            ob_start();

            if (!$Plugin->isInitialized()) {
                $this->pluginService->installWithCode($Plugin->getCode());
            }

            $this->pluginService->enable($Plugin);
            $log = ob_get_clean();
            ob_end_flush();
        }

        $cacheUtil->clearCache();

        if ($request->isXmlHttpRequest()) {
            return $this->json(['success' => true, 'log' => $log]);
        } else {
            $this->addSuccess(trans('「%plugin_name%」を有効にしました。', ['%plugin_name%' => $Plugin->getName()]), 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }
    }

    /**
     * 対象のプラグインを無効にします。
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/disable", requirements={"id" = "\d+"}, name="admin_store_plugin_disable", methods={"POST"})
     *
     * @param Request $request
     * @param Plugin $Plugin
     * @param CacheUtil $cacheUtil
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|RedirectResponse
     */
    public function disable(Request $request, Plugin $Plugin, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        $log = null;
        if ($Plugin->isEnabled()) {
            $dependents = $this->pluginService->findDependentPluginNeedDisable($Plugin->getCode());
            if (!empty($dependents)) {
                $dependName = $dependents[0];
                $DependPlugin = $this->pluginRepository->findOneBy(['code' => $dependents[0]]);
                if ($DependPlugin) {
                    $dependName = $DependPlugin->getName();
                }
                $message = trans('admin.plugin.disable.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);

                if ($request->isXmlHttpRequest()) {
                    return $this->json(['message' => $message], 400);
                } else {
                    $this->addError($message, 'admin');

                    return $this->redirectToRoute('admin_store_plugin');
                }
            }

            ob_start();
            $this->pluginService->disable($Plugin);
            $log = ob_get_clean();
            ob_end_flush();
        } else {
            if ($request->isXmlHttpRequest()) {
                return $this->json(['success' => true, 'log' => $log]);
            } else {
                $this->addError('admin.plugin.already.disable', 'admin');

                return $this->redirectToRoute('admin_store_plugin');
            }
        }

        $cacheUtil->clearCache();

        if ($request->isXmlHttpRequest()) {
            return $this->json(['success' => true, 'log' => $log]);
        } else {
            $this->addSuccess('admin.plugin.disable.complete', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }
    }

    /**
     * 対象のプラグインを削除します。
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_uninstall", methods={"DELETE"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
     *
     * @throws \Exception
     */
    public function uninstall(Plugin $Plugin)
    {
        $this->isTokenValid();

        if ($Plugin->isEnabled()) {
            $this->addError('admin.plugin.uninstall.error.not_disable', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }

        // Check other plugin depend on it
        $pluginCode = $Plugin->getCode();
        $otherDepend = $this->pluginService->findDependentPlugin($pluginCode);
        if (!empty($otherDepend)) {
            $DependPlugin = $this->pluginRepository->findOneBy(['code' => $otherDepend[0]]);
            $dependName = $otherDepend[0];
            if ($DependPlugin) {
                $dependName = $DependPlugin->getName();
            }
            $message = trans('admin.plugin.uninstall.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
            $this->addError($message, 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }

        $this->pluginService->uninstall($Plugin);
        $this->addSuccess('admin.plugin.uninstall.complete', 'admin');

        return $this->redirectToRoute('admin_store_plugin');
    }

    /**
     * プラグインファイルアップロード画面
     *
     * @Route("/%eccube_admin_route%/store/plugin/install", name="admin_store_plugin_install")
     * @Template("@admin/Store/plugin_install.twig")
     *
     * @param Request $request
     *
     * @return array|RedirectResponse
     */
    public function install(Request $request)
    {
        $form = $this->formFactory
            ->createBuilder(PluginLocalInstallType::class)
            ->getForm();
        $errors = [];
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tmpDir = null;
            try {
                $service = $this->pluginService;
                /** @var UploadedFile $formFile */
                $formFile = $form['plugin_archive']->getData();
                $tmpDir = $service->createTempDir();
                // 拡張子を付けないとpharが動かないので付ける
                $tmpFile = sha1(StringUtil::random(32)).'.'.$formFile->getClientOriginalExtension();
                $formFile->move($tmpDir, $tmpFile);
                $tmpPath = $tmpDir.'/'.$tmpFile;
                $service->install($tmpPath);
                // Remove tmp file
                $fs = new Filesystem();
                $fs->remove($tmpDir);
                $this->addSuccess('admin.plugin.install.complete', 'admin');

                return $this->redirectToRoute('admin_store_plugin');
            } catch (PluginException $e) {
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                log_error('plugin install failed.', ['original-message' => $e->getMessage()]);
                $errors[] = $e;
            } catch (\Exception $er) {
                // Catch composer install error | Other error
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                log_error('plugin install failed.', ['original-message' => $er->getMessage()]);
                $this->addError('admin.plugin.install.fail', 'admin');
            }
        } else {
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error;
            }
        }

        return [
            'form' => $form->createView(),
            'errors' => $errors,
        ];
    }

    /**
     * 認証キー設定画面
     *
     * @Route("/%eccube_admin_route%/store/plugin/authentication_setting", name="admin_store_authentication_setting")
     * @Template("@admin/Store/authentication_setting.twig")
     * @param Request $request
     *
     * @return array
     */
    public function authenticationSetting(Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(AuthenticationType::class, $this->BaseInfo);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 認証キーの登録 and PHP path
            $this->BaseInfo = $form->getData();
            $this->entityManager->persist($this->BaseInfo);
            $this->entityManager->flush();

            // composerの認証を更新
            $this->composerApiService->configureRepository($this->BaseInfo);

            $this->addSuccess('admin.common.save_complete', 'admin');
        }

        return [
            'form' => $form->createView(),
            'eccubeUrl' => $this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'eccubeShopName' => $this->BaseInfo->getShopName(),
        ];
    }

    /**
     * フォルダ設置のみのプラグインを取得する.
     *
     * @param array $plugins
     *
     * @return array
     *
     * @throws PluginException
     */
    protected function getUnregisteredPlugins(array $plugins)
    {
        $finder = new Finder();
        $pluginCodes = [];

        // DB登録済みプラグインコードのみ取得
        foreach ($plugins as $key => $plugin) {
            $pluginCodes[] = $plugin->getCode();
        }
        // DB登録済みプラグインコードPluginディレクトリから排他
        $dirs = $finder->in($this->eccubeConfig['plugin_realdir'])->depth(0)->directories();

        // プラグイン基本チェック
        $unregisteredPlugins = [];
        foreach ($dirs as $dir) {
            $pluginCode = $dir->getBasename();
            if (in_array($pluginCode, $pluginCodes, true)) {
                continue;
            }
            try {
                $this->pluginService->checkPluginArchiveContent($dir->getRealPath());
            } catch (PluginException $e) {
                //config.yamlに不備があった際は全てスキップ
                log_warning($e->getMessage());
                continue;
            }
            $config = $this->pluginService->readConfig($dir->getRealPath());
            $unregisteredPlugins[$pluginCode]['name'] = isset($config['name']) ? $config['name'] : null;
            $unregisteredPlugins[$pluginCode]['event'] = isset($config['event']) ? $config['event'] : null;
            $unregisteredPlugins[$pluginCode]['version'] = isset($config['version']) ? $config['version'] : null;
            $unregisteredPlugins[$pluginCode]['enabled'] = Constant::DISABLED;
            $unregisteredPlugins[$pluginCode]['code'] = isset($config['code']) ? $config['code'] : null;
        }

        return $unregisteredPlugins;
    }
}
