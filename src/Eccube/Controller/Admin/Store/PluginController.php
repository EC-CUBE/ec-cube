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
use Eccube\Entity\PluginEventHandler;
use Eccube\Exception\PluginException;
use Eccube\Form\Type\Admin\CaptchaType;
use Eccube\Form\Type\Admin\AuthenticationType;
use Eccube\Form\Type\Admin\PluginLocalInstallType;
use Eccube\Form\Type\Admin\PluginManagementType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PluginEventHandlerRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginApiService;
use Eccube\Service\PluginService;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;

class PluginController extends AbstractController
{
    /**
     * @var PluginEventHandlerRepository
     */
    protected $pluginEventHandlerRepository;

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
     * PluginController constructor.
     *
     * @param PluginRepository $pluginRepository
     * @param PluginService $pluginService
     * @param PluginEventHandlerRepository $eventHandlerRepository
     * @param BaseInfoRepository $baseInfoRepository
     * @param PluginApiService $pluginApiService
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __construct(PluginRepository $pluginRepository, PluginService $pluginService, PluginEventHandlerRepository $eventHandlerRepository, BaseInfoRepository $baseInfoRepository, PluginApiService $pluginApiService)
    {
        $this->pluginRepository = $pluginRepository;
        $this->pluginService = $pluginService;
        $this->pluginEventHandlerRepository = $eventHandlerRepository;
        $this->BaseInfo = $baseInfoRepository->get();
        $this->pluginApiService = $pluginApiService;
    }

    /**
     * インストール済プラグイン画面
     *
     * @Route("/%eccube_admin_route%/store/plugin", name="admin_store_plugin")
     * @Template("@admin/Store/plugin.twig")
     */
    public function index(Request $request)
    {
        $pluginForms = [];
        $configPages = [];
        $Plugins = $this->pluginRepository->findBy([], ['code' => 'ASC']);

        // ファイル設置プラグインの取得.
        $unregisterdPlugins = $this->getUnregisteredPlugins($Plugins);
        $unregisterdPluginsConfigPages = [];
        foreach ($unregisterdPlugins as $unregisterdPlugin) {
            try {
                $code = $unregisterdPlugin['code'];
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $unregisterdPluginsConfigPages[$code] = $this->generateUrl('plugin_'.$code.'_config');
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

        // Todo: Need new authentication mechanism
        // オーナーズストアからダウンロード可能プラグイン情報を取得
        $authKey = $this->BaseInfo->getAuthenticationKey();
        // オーナーズストア通信
        // TODO: get url from api service instead of direct from controller
        $url = $this->eccubeConfig['eccube_package_repo_url'].'/plugins/purchased';
        list($json, $info) = $this->getRequestApi($request, $authKey, $url);
        $officialPluginsDetail = [];
        if ($json) {
            // 接続成功時
            $data = json_decode($json, true);
            foreach ($data as $item) {
                if (isset($officialPlugins[$item['id']])) {
                    $Plugin = $officialPlugins[$item['id']];
                    $officialPluginsDetail[$item['id']] = $item;
                    $officialPluginsDetail[$item['id']]['update_status'] = 0;
                    if ($this->pluginService->isUpdate($Plugin->getVersion(), $item['version'])) {
                        $officialPluginsDetail[$item['id']]['update_status'] = 1;
                    }
                } else {
                    $Plugin = new Plugin();
                    $Plugin->setName($item['name']);
                    $Plugin->setCode($item['code']);
                    $Plugin->setVersion($item['version']);
                    $Plugin->setSource($item['id']);
                    $Plugin->setEnabled(false);
                    $officialPlugins[$item['id']] = $Plugin;
                    $officialPluginsDetail[$item['id']] = $item;
                    $officialPluginsDetail[$item['id']]['update_status'] = 0;
                    if ($this->pluginService->isUpdate($Plugin->getVersion(), $item['version'])) {
                        $officialPluginsDetail[$item['id']]['update_status'] = 1;
                    }
                }
            }
        }

        return [
            'plugin_forms' => $pluginForms,
            'officialPlugins' => $officialPlugins,
            'unofficialPlugins' => $unofficialPlugins,
            'configPages' => $configPages,
            'unregisterdPlugins' => $unregisterdPlugins,
            'unregisterdPluginsConfigPages' => $unregisterdPluginsConfigPages,
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
     * @Route("/%eccube_admin_route%/store/plugin/{id}/enable", requirements={"id" = "\d+"}, name="admin_store_plugin_enable", methods={"PUT"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
     */
    public function enable(Plugin $Plugin, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        if ($Plugin->isEnabled()) {
            $this->addError('admin.plugin.already.enable', 'admin');
        } else {
            $requires = $this->pluginService->findRequirePluginNeedEnable($Plugin->getCode());
            if (!empty($requires)) {
                $DependPlugin = $this->pluginRepository->findOneBy(['code' => $requires[0]]);
                $dependName = $requires[0];
                if ($DependPlugin) {
                    $dependName = $DependPlugin->getName();
                }
                $message = trans('admin.plugin.enable.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
                $this->addError($message, 'admin');

                return $this->redirectToRoute('admin_store_plugin');
            }
            $this->pluginService->enable($Plugin);
            $this->addSuccess('admin.plugin.enable.complete', 'admin');
        }

        // キャッシュを削除してリダイレクト
        // リダイレクトにredirectToRoute関数を使用していないのは、削除したキャッシュが再生成されてしまうため。
        $url = $this->generateUrl('admin_store_plugin');
        $cacheUtil->clearCache();

        return $this->redirect($url);
    }

    /**
     * 対象のプラグインを無効にします。
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/disable", requirements={"id" = "\d+"}, name="admin_store_plugin_disable", methods={"PUT"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
     */
    public function disable(Plugin $Plugin, CacheUtil $cacheUtil)
    {
        $this->isTokenValid();

        if ($Plugin->isEnabled()) {
            $dependents = $this->pluginService->findDependentPluginNeedDisable($Plugin->getCode());
            if (!empty($dependents)) {
                $dependName = $dependents[0];
                $DependPlugin = $this->pluginRepository->findOneBy(['code' => $dependents[0]]);
                if ($DependPlugin) {
                    $dependName = $DependPlugin->getName();
                }
                $message = trans('admin.plugin.disable.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
                $this->addError($message, 'admin');

                return $this->redirectToRoute('admin_store_plugin');
            }

            $this->pluginService->disable($Plugin);
            $this->addSuccess('admin.plugin.disable.complete', 'admin');
        } else {
            $this->addError('admin.plugin.already.disable', 'admin');

            return $this->redirectToRoute('admin_store_plugin');
        }

        // キャッシュを削除してリダイレクト
        // リダイレクトにredirectToRoute関数を使用していないのは、削除したキャッシュが再生成されてしまうため。
        $url = $this->generateUrl('admin_store_plugin');
        $cacheUtil->clearCache();

        return $this->redirect($url);
    }

    /**
     * 対象のプラグインを削除します。
     *
     * @Route("/%eccube_admin_route%/store/plugin/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_uninstall", methods={"DELETE"})
     *
     * @param Plugin $Plugin
     *
     * @return RedirectResponse
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
     * @Route("/%eccube_admin_route%/store/plugin/handler", name="admin_store_plugin_handler")
     * @Template("@admin/Store/plugin_handler.twig")
     */
    public function handler()
    {
        $handlers = $this->pluginEventHandlerRepository->getHandlers();

        // 一次元配列からイベント毎の二次元配列に変換する
        $HandlersPerEvent = [];
        foreach ($handlers as $handler) {
            $HandlersPerEvent[$handler->getEvent()][$handler->getHandlerType()][] = $handler;
        }

        return [
            'handlersPerEvent' => $HandlersPerEvent,
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/store/plugin/handler_up/{id}", requirements={"id" = "\d+"}, name="admin_store_plugin_handler_up")
     */
    public function handler_up(PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($repo->find($Handler->getId()));

        return $this->redirectToRoute('admin_store_plugin_handler');
    }

    /**
     * @Route("/%eccube_admin_route%/store/plugin/handler_down/{id}", requirements={"id" = "\d+"}, name="admin_store_plugin_handler_down")
     */
    public function handler_down(PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($Handler, false);

        return $this->redirectToRoute('admin_store_plugin_handler');
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

            $this->addSuccess('admin.flash.register_completed', 'admin');
        }

        $builderCaptcha = $this->formFactory->createBuilder(CaptchaType::class);

        // get captcha image, save it to temp folder
        list($captcha, $info) = $this->pluginApiService->getCaptcha();
        $tmpFolder = $this->eccubeConfig->get('eccube_temp_image_dir');
        file_put_contents($tmpFolder.'/captcha.png', $captcha);

        return [
            'form' => $form->createView(),
            'captchaForm' => $builderCaptcha->getForm()->createView(),
        ];
    }

    /**
     * Captcha
     * Todo: check fail (implement after the api defined)
     *
     * @param Request $request
     * @return RedirectResponse
     *
     * @Route("/%eccube_admin_route%/store/plugin/auth/captcha", name="admin_store_auth_captcha")
     */
    public function authenticationCaptcha(Request $request)
    {
        $builder = $this->formFactory->createBuilder(CaptchaType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $param['captcha'] = $form['captcha']->getData();
            list($ret, $info) = $this->pluginApiService->postApiKey($param);
            if ($ret && $data = json_decode($ret, true)) {
                if (isset($data['api_key']) && !empty($data['api_key'])) {
                    $this->BaseInfo->setAuthenticationKey($data['api_key']);
                    $this->entityManager->persist($this->BaseInfo);
                    $this->entityManager->flush($this->BaseInfo);
                    $this->addSuccess('admin.flash.register_completed', 'admin');

                    return $this->redirectToRoute('admin_store_authentication_setting');
                }
            }
        }
        $this->addError('admin.flash.register_failed', 'admin');

        return $this->redirectToRoute('admin_store_authentication_setting');
    }

    /**
     * APIリクエスト処理
     *
     * @param Request $request
     * @param string|null $authKey
     * @param string $url
     * @deprecated since release, please refer PluginApiService
     * @return array
     */
    private function getRequestApi(Request $request, $authKey, $url)
    {
        $curl = curl_init($url);

        $options = [// オプション配列
            //HEADER
            CURLOPT_HTTPHEADER => [
                'Authorization: '.base64_encode($authKey),
                'x-eccube-store-url: '.base64_encode($request->getSchemeAndHttpHost().$request->getBasePath()),
                'x-eccube-store-version: '.base64_encode(Constant::VERSION),
            ],
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
        ];

        curl_setopt_array($curl, $options); /// オプション値を設定
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        log_info('http get_info', $info);

        return [$result, $info];
    }

    /**
     * レスポンスのチェック
     *
     * @param $info
     *
     * @return string
     * @deprecated since release, please refer PluginApiService
     */
    private function getResponseErrorMessage($info)
    {
        if (!empty($info)) {
            $statusCode = $info['http_code'];
            $message = $info['message'];

            $message = $statusCode.' : '.$message;
        } else {
            $message = trans('plugin.text.error.timeout_or_invalid_url');
        }

        return $message;
    }

    /**
     * フォルダ設置のみのプラグインを取得する.
     *
     * @param array $plugins
     *
     * @return array
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
            } catch (\Eccube\Exception\PluginException $e) {
                //config.yamlに不備があった際は全てスキップ
                log_warning($e->getMessage());
                continue;
            }
            $config = $this->pluginService->readYml($dir->getRealPath().'/config.yml');
            $unregisteredPlugins[$pluginCode]['name'] = isset($config['name']) ? $config['name'] : null;
            $unregisteredPlugins[$pluginCode]['event'] = isset($config['event']) ? $config['event'] : null;
            $unregisteredPlugins[$pluginCode]['version'] = isset($config['version']) ? $config['version'] : null;
            $unregisteredPlugins[$pluginCode]['enabled'] = Constant::DISABLED;
            $unregisteredPlugins[$pluginCode]['code'] = isset($config['code']) ? $config['code'] : null;
        }

        return $unregisteredPlugins;
    }
}
