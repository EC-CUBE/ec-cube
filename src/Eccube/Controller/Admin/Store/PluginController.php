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

namespace Eccube\Controller\Admin\Store;

use Doctrine\ORM\EntityManager;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Eccube\Exception\PluginException;
use Eccube\Form\Type\Admin\PluginLocalInstallType;
use Eccube\Form\Type\Admin\PluginManagementType;
use Eccube\Repository\PluginEventHandlerRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route(service=PluginController::class)
 */
class PluginController extends AbstractController
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var PluginEventHandlerRepository
     */
    protected $pluginEventHandlerRepository;

    /**
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @var array
     */
    protected $eccubeConfig;

    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var PluginRepository
     */
    protected $pluginRepository;

    /**
     * インストール済プラグイン画面
     *
     * @Route("/%eccube_admin_route%/store/plugin", name="admin_store_plugin")
     * @Template("Store/plugin.twig")
     */
    public function index(Application $app, Request $request)
    {
        $pluginForms = [];
        $configPages = [];
        $Plugins = $this->pluginRepository->findBy([], ['code' => 'ASC']);

        // ファイル設置プラグインの取得.
        $unregisterdPlugins = $this->getUnregisteredPlugins($Plugins, $app);
        $unregisterdPluginsConfigPages = [];
        foreach ($unregisterdPlugins as $unregisterdPlugin) {
            try {
                $code = $unregisterdPlugin['code'];
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $unregisterdPluginsConfigPages[$code] = $app->url('plugin_'.$code.'_config');
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
                $configPages[$Plugin->getCode()] = $app->url('plugin_'.$Plugin->getCode().'_config');
            } catch (\Exception $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
            if ($Plugin->getSource() == 0) {
                // 商品IDが設定されていない場合、非公式プラグイン
                $unofficialPlugins[] = $Plugin;
            } else {
                $officialPlugins[] = $Plugin;
            }
        }

        // Todo: Need new authentication mechanism
        // オーナーズストアからダウンロード可能プラグイン情報を取得
        $authKey = $this->BaseInfo->getAuthenticationKey();
        // オーナーズストア通信
        $url = $this->eccubeConfig['package_repo_url'].'/search/packages.json';
        list($json, $info) = $this->getRequestApi($request, $authKey, $url, $app);

        $officialPluginsDetail = [];
        if ($json) {
            // 接続成功時
            $data = json_decode($json, true);
            if (isset($data['success']) && $data['success']) {
                foreach ($data['item'] as $item) {
                    foreach ($officialPlugins as $key => $plugin) {
                        if ($plugin->getSource() == $item['product_id']) {
                            $officialPluginsDetail[$key] = $item;
                            $officialPluginsDetail[$key]['update_status'] = 0;
                            if ($this->pluginService->isUpdate($plugin->getVersion(), $item['version'])) {
                                $officialPluginsDetail[$key]['update_status'] = 1;
                            }
                        }
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
     * @Method("POST")
     * @Route("/%eccube_admin_route%/store/plugin/{id}/update", requirements={"id" = "\d+"}, name="admin_store_plugin_update")
     *
     * @param Application $app
     * @param Request     $request
     * @param Plugin      $Plugin
     *
     * @return RedirectResponse
     */
    public function update(Application $app, Request $request, Plugin $Plugin)
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
                $app->addSuccess('admin.plugin.update.complete', 'admin');

                return $app->redirect($app->url('admin_store_plugin'));
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
                $this->logger->error('plugin install failed.', ['original-message' => $er->getMessage()]);
                $message = 'admin.plugin.install.fail';
            }
        } else {
            $errors = $form->getErrors(true);
            foreach ($errors as $error) {
                $message = $error->getMessage();
            }
        }

        $app->addError($message, 'admin');

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * 対象のプラグインを有効にします。
     *
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/store/plugin/{id}/enable", requirements={"id" = "\d+"}, name="admin_store_plugin_enable")
     *
     * @param Application $app
     * @param Plugin      $Plugin
     *
     * @return RedirectResponse
     */
    public function enable(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->isEnabled()) {
            $app->addError('admin.plugin.already.enable', 'admin');
        } else {
            $requires = $this->pluginService->findRequirePluginNeedEnable($Plugin->getCode());
            if (!empty($requires)) {
                $DependPlugin = $this->pluginRepository->findOneBy(['code' => $requires[0]]);
                $dependName = $requires[0];
                if ($DependPlugin) {
                    $dependName = $DependPlugin->getName();
                }
                $message = trans('admin.plugin.enable.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
                $app->addError($message, 'admin');

                return $app->redirect($app->url('admin_store_plugin'));
            }
            $this->pluginService->enable($Plugin);
            $app->addSuccess('admin.plugin.enable.complete', 'admin');
        }

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * 対象のプラグインを無効にします。
     *
     * @Method("PUT")
     * @Route("/%eccube_admin_route%/store/plugin/{id}/disable", requirements={"id" = "\d+"}, name="admin_store_plugin_disable")
     *
     * @param Application $app
     * @param Plugin      $Plugin
     *
     * @return RedirectResponse
     */
    public function disable(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->isEnabled()) {
            $dependents = $this->pluginService->findDependentPluginNeedDisable($Plugin->getCode());
            if (!empty($dependents)) {
                $dependName = $dependents[0];
                $DependPlugin = $this->pluginRepository->findOneBy(['code' => $dependents[0]]);
                if ($DependPlugin) {
                    $dependName = $DependPlugin->getName();
                }
                $message = trans('admin.plugin.disable.depend', ['%name%' => $Plugin->getName(), '%depend_name%' => $dependName]);
                $app->addError($message, 'admin');

                return $app->redirect($app->url('admin_store_plugin'));
            }

            $this->pluginService->disable($Plugin);
            $app->addSuccess('admin.plugin.disable.complete', 'admin');
        } else {
            $app->addError('admin.plugin.already.disable', 'admin');
        }

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * 対象のプラグインを削除します。
     *
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/store/plugin/{id}/uninstall", requirements={"id" = "\d+"}, name="admin_store_plugin_uninstall")
     *
     * @param Application $app
     * @param Plugin      $Plugin
     *
     * @return RedirectResponse
     */
    public function uninstall(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->isEnabled()) {
            $app->addError('admin.plugin.uninstall.error.not_disable', 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
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
            $app->addError($message, 'admin');

            return $app->redirect($app->url('admin_store_plugin'));
        }

        $this->pluginService->uninstall($Plugin);
        $app->addSuccess('admin.plugin.uninstall.complete', 'admin');

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * @Route("/%eccube_admin_route%/store/plugin/handler", name="admin_store_plugin_handler")
     * @Template("Store/plugin_handler.twig")
     */
    public function handler(Application $app)
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
    public function handler_up(Application $app, PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($repo->find($Handler->getId()));

        return $app->redirectToRoute('admin_store_plugin_handler');
    }

    /**
     * @Route("/%eccube_admin_route%/store/plugin/handler_down/{id}", requirements={"id" = "\d+"}, name="admin_store_plugin_handler_down")
     */
    public function handler_down(Application $app, PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($Handler, false);

        return $app->redirectToRoute('admin_store_plugin_handler');
    }

    /**
     * プラグインファイルアップロード画面
     *
     * @Route("/%eccube_admin_route%/store/plugin/install", name="admin_store_plugin_install")
     * @Template("Store/plugin_install.twig")
     *
     * @param Application $app
     * @param Request     $request
     *
     * @return array|RedirectResponse
     */
    public function install(Application $app, Request $request)
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
                $app->addSuccess('admin.plugin.install.complete', 'admin');

                return $app->redirect($app->url('admin_store_plugin'));
            } catch (PluginException $e) {
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                $this->logger->error('plugin install failed.', ['original-message' => $e->getMessage()]);
                $errors[] = $e;
            } catch (\Exception $er) {
                // Catch composer install error | Other error
                if (!empty($tmpDir) && file_exists($tmpDir)) {
                    $fs = new Filesystem();
                    $fs->remove($tmpDir);
                }
                $this->logger->error('plugin install failed.', ['original-message' => $er->getMessage()]);
                $app->addError('admin.plugin.install.fail', 'admin');
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
     * @Template("Store/authentication_setting.twig")
     */
    public function authenticationSetting(Application $app, Request $request)
    {
        $builder = $this->formFactory
            ->createBuilder(FormType::class, $this->BaseInfo);
        $builder->add(
            'authentication_key',
            TextType::class,
            [
                'label' => trans('plugin.label.auth_key'),
                'constraints' => [
                    new Assert\Regex(['pattern' => '/^[0-9a-zA-Z]+$/']),
                ],
            ]
        );

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 認証キーの登録
            $BaseInfo = $form->getData();
            $this->entityManager->flush($BaseInfo);

            $app->addSuccess('admin.plugin.authentication.setting.complete', 'admin');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * APIリクエスト処理
     *
     * @param Request $request
     * @param $authKey
     * @param string $url
     * @param Application $app
     *
     * @return array
     */
    private function getRequestApi(Request $request, $authKey, $url, $app)
    {
        $curl = curl_init($url);

        $options = [           // オプション配列
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

        $app->log('http get_info', $info);

        return [$result, $info];
    }

    /**
     * レスポンスのチェック
     *
     * @param $info
     *
     * @return string
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
     * @param Application $app
     *
     * @return array
     */
    protected function getUnregisteredPlugins(array $plugins, \Eccube\Application $app)
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
                $this->logger->warning($e->getMessage());
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
