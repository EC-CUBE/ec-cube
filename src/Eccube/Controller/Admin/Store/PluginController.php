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
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Plugin;
use Eccube\Entity\PluginEventHandler;
use Eccube\Exception\PluginException;
use Eccube\Form\Type\Admin\PluginLocalInstallType;
use Eccube\Form\Type\Admin\PluginManagementType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\PluginEventHandlerRepository;
use Eccube\Repository\PluginRepository;
use Eccube\Service\PluginService;
use Eccube\Util\Str;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Component
 * @Route(service=PluginController::class)
 */
class PluginController extends AbstractController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("monolog")
     * @var Logger
     */
    protected $logger;

    /**
     * @Inject(PluginEventHandlerRepository::class)
     * @var PluginEventHandlerRepository
     */
    protected $pluginEventHandlerRepository;

    /**
     * @Inject(PluginService::class)
     * @var PluginService
     */
    protected $pluginService;

    /**
     * @Inject("config")
     * @var array
     */
    protected $appConfig;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(PluginRepository::class)
     * @var PluginRepository
     */
    protected $pluginRepository;


    /**
     * インストール済プラグイン画面
     *
     * @Route("/{_admin}/store/plugin", name="admin_store_plugin")
     * @Template("Store/plugin.twig")
     */
    public function index(Application $app, Request $request)
    {
        $pluginForms = array();
        $configPages = array();

        $Plugins = $this->pluginRepository->findBy(array(), array('code' => 'ASC'));

        // ファイル設置プラグインの取得.
        $unregisterdPlugins = $this->getUnregisteredPlugins($Plugins, $app);
        $unregisterdPluginsConfigPages = array();
        foreach ($unregisterdPlugins as $unregisterdPlugin) {
            try {
                $code = $unregisterdPlugin['code'];
                // プラグイン用設定画面があれば表示(プラグイン用のサービスプロバイダーに定義されているか)
                $unregisterdPluginsConfigPages[$code] = $app->url('plugin_'.$code.'_config');
            } catch (RouteNotFoundException $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
        }

        $officialPlugins = array();
        $unofficialPlugins = array();

        foreach ($Plugins as $Plugin) {

            $form = $this->formFactory
                ->createNamedBuilder(
                    'form'.$Plugin->getId(),
                    PluginManagementType::class,
                    null,
                    array(
                        'plugin_id' => $Plugin->getId(),
                    )
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

        // オーナーズストアからダウンロード可能プラグイン情報を取得
        $BaseInfo = $this->baseInfoRepository->get();

        $authKey = $BaseInfo->getAuthenticationKey();

        if (!is_null($authKey)) {

            // オーナーズストア通信
            $url = $this->appConfig['owners_store_url'].'?method=list';
            list($json, $info) = $this->getRequestApi($request, $authKey, $url, $app);

            if ($json) {

                // 接続成功時

                $data = json_decode($json, true);

                if (isset($data['success'])) {
                    $success = $data['success'];
                    if ($success == '1') {

                        // 既にインストールされているかどうか確認
                        foreach ($data['item'] as $item) {
                            foreach ($officialPlugins as $plugin) {
                                if ($plugin->getSource() == $item['product_id']) {
                                    // 商品IDが同一の情報を設定
                                    $plugin->setNewVersion($item['version']);
                                    $plugin->setLastUpdateDate($item['last_update_date']);
                                    $plugin->setProductUrl($item['product_url']);
                                    $plugin->setEccubeVersion($item['eccube_version']);

                                    if ($plugin->getVersion() != $item['version']) {
                                        // バージョンが異なる
                                        $plugin->setUpdateStatus(3);
                                        break;
                                    }
                                }
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
        ];
    }

    /**
     * インストール済プラグインからのアップデート
     *
     * @Method("POST")
     * @Route("/{_admin}/store/plugin/{id}/update", requirements={"id":"\d+"}, name="admin_store_plugin_update")
     */
    public function update(Application $app, Request $request, Plugin $Plugin)
    {
        $form = $this->formFactory
            ->createNamedBuilder(
                'form'.$Plugin->getId(),
                PluginManagementType::class,
                null,
                array(
                    'plugin_id' => null, // placeHolder
                )
            )
            ->getForm();

        $message = '';

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $tmpDir = null;
                try {

                    $formFile = $form['plugin_archive']->getData();

                    $tmpDir = $this->pluginService->createTempDir();
                    $tmpFile = sha1(Str::random(32)).'.'.$formFile->getClientOriginalExtension();

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
                }
            } else {
                $errors = $form->getErrors(true);
                foreach ($errors as $error) {
                    $message = $error->getMessage();
                }

            }

        }

        $app->addError($message, 'admin');

        return $app->redirect($app->url('admin_store_plugin'));
    }


    /**
     * 対象のプラグインを有効にします。
     *
     * @Method("PUT")
     * @Route("/{_admin}/store/plugin/{id}/enable", requirements={"id":"\d+"}, name="admin_store_plugin_enable")
     */
    public function enable(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->getEnable() == Constant::ENABLED) {
            $app->addError('admin.plugin.already.enable', 'admin');
        } else {
            $this->pluginService->enable($Plugin);
            $app->addSuccess('admin.plugin.enable.complete', 'admin');
        }

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * 対象のプラグインを無効にします。
     *
     * @Method("PUT")
     * @Route("/{_admin}/store/plugin/{id}/disable", requirements={"id":"\d+"}, name="admin_store_plugin_disable")
     */
    public function disable(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        if ($Plugin->getEnable() == Constant::ENABLED) {
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
     * @Route("/{_admin}/store/plugin/{id}/uninstall", requirements={"id":"\d+"}, name="admin_store_plugin_uninstall")
     */
    public function uninstall(Application $app, Plugin $Plugin)
    {
        $this->isTokenValid($app);

        $this->pluginService->uninstall($Plugin);

        $app->addSuccess('admin.plugin.uninstall.complete', 'admin');

        return $app->redirect($app->url('admin_store_plugin'));
    }

    /**
     * @Route("/{_admin}/store/plugin/handler", name="admin_store_plugin_handler")
     * @Template("Store/plugin_handler.twig")
     */
    public function handler(Application $app)
    {
        $handlers = $this->pluginEventHandlerRepository->getHandlers();

        // 一次元配列からイベント毎の二次元配列に変換する
        $HandlersPerEvent = array();
        foreach ($handlers as $handler) {
            $HandlersPerEvent[$handler->getEvent()][$handler->getHandlerType()][] = $handler;
        }

        return [
            'handlersPerEvent' => $HandlersPerEvent,
        ];
    }

    /**
     * @Route("/{_admin}/store/plugin/handler_up/{id}", requirements={"id":"\d+"}, name="admin_store_plugin_handler_up")
     */
    public function handler_up(Application $app, PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($repo->find($Handler->getId()));

        return $app->redirect($app->url('admin_store_plugin_handler'));
    }

    /**
     * @Route("/{_admin}/store/plugin/handler_down/{id}", requirements={"id":"\d+"}, name="admin_store_plugin_handler_down")
     */
    public function handler_down(Application $app, PluginEventHandler $Handler)
    {
        $repo = $this->pluginEventHandlerRepository;
        $repo->upPriority($Handler, false);

        return $app->redirect($app->url('admin_store_plugin_handler'));
    }

    /**
     * プラグインファイルアップロード画面
     *
     * @Route("/{_admin}/store/plugin/install", name="admin_store_plugin_install")
     * @Template("Store/plugin_install.twig")
     */
    public function install(Application $app, Request $request)
    {
        $form = $this->formFactory
            ->createBuilder(PluginLocalInstallType::class)
            ->getForm();

        $errors = array();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                $tmpDir = null;
                try {
                    $service = $this->pluginService;

                    $formFile = $form['plugin_archive']->getData();

                    $tmpDir = $service->createTempDir();
                    $tmpFile = sha1(Str::random(32))
                        .'.'
                        .$formFile->getClientOriginalExtension(); // 拡張子を付けないとpharが動かないので付ける

                    $formFile->move($tmpDir, $tmpFile);

                    $service->install($tmpDir.'/'.$tmpFile);

                    $fs = new Filesystem();
                    $fs->remove($tmpDir);

                    $app->addSuccess('admin.plugin.install.complete', 'admin');

                    return $app->redirect($app->url('admin_store_plugin'));

                } catch (PluginException $e) {
                    if (!empty($tmpDir) && file_exists($tmpDir)) {
                        $fs = new Filesystem();
                        $fs->remove($tmpDir);
                    }
                    $this->logger->error(
                        "plugin install failed.",
                        array(
                            'original-message' => $e->getMessage(),
                        )
                    );
                    $errors[] = $e;
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $errors[] = $error;
                }
            }
        }

        return [
            'form' => $form->createView(),
            'errors' => $errors,
        ];
    }

    /**
     * オーナーズストアプラグインインストール画面
     *
     * @Route("/{_admin}/store/plugin/owners_install", name="admin_store_plugin_owners_install")
     * @Template("Store/plugin_owners_install.twig")
     */
    public function ownersInstall(Application $app, Request $request)
    {
        // オーナーズストアからダウンロード可能プラグイン情報を取得
        $BaseInfo = $this->baseInfoRepository->get();

        $authKey = $BaseInfo->getAuthenticationKey();
        $authResult = true;
        $success = 0;
        $items = array();
        $promotionItems = array();
        $message = '';
        if (!is_null($authKey)) {

            // オーナーズストア通信
            $url = $this->appConfig['owners_store_url'].'?method=list';
            list($json, $info) = $this->getRequestApi($request, $authKey, $url, $app);

            if ($json === false) {
                // 接続失敗時
                $success = 0;

                $message = $this->getResponseErrorMessage($info);

            } else {
                // 接続成功時

                $data = json_decode($json, true);

                if (isset($data['success'])) {
                    $success = $data['success'];
                    if ($success == '1') {
                        $items = array();

                        // 既にインストールされているかどうか確認
                        $Plugins = $this->pluginRepository->findAll();
                        $status = false;
                        // update_status 1 : 未インストール、2 : インストール済、 3 : 更新あり、4 : 有料購入
                        foreach ($data['item'] as $item) {
                            foreach ($Plugins as $plugin) {
                                if ($plugin->getSource() == $item['product_id']) {
                                    if ($plugin->getVersion() == $item['version']) {
                                        // バージョンが同じ
                                        $item['update_status'] = 2;
                                    } else {
                                        // バージョンが異なる
                                        $item['update_status'] = 3;
                                    }
                                    $items[] = $item;
                                    $status = true;
                                    break;
                                }
                            }
                            if (!$status) {
                                // 未インストール
                                $item['update_status'] = 1;
                                $items[] = $item;
                            }
                            $status = false;
                        }

                        // EC-CUBEのバージョンチェック
                        // 参照渡しをして値を追加
                        foreach ($items as &$item) {
                            if (in_array(Constant::VERSION, $item['eccube_version'])) {
                                // 対象バージョン
                                $item['version_check'] = 1;
                            } else {
                                // 未対象バージョン
                                $item['version_check'] = 0;
                            }
                            if ($item['price'] != '0' && $item['purchased'] == '0') {
                                // 有料商品で未購入
                                $item['update_status'] = 4;
                            }
                        }
                        unset($item);

                        // promotionアイテム
                        $i = 0;
                        foreach ($items as $item) {
                            if ($item['promotion'] == 1) {
                                $promotionItems[] = $item;
                                unset($items[$i]);
                            }
                            $i++;
                        }

                    } else {
                        $message = $data['error_code'].' : '.$data['error_message'];
                    }
                } else {
                    $success = 0;
                    $message = "EC-CUBEオーナーズストアにエラーが発生しています。";
                }
            }

        } else {
            $authResult = false;
        }

        return [
            'authResult' => $authResult,
            'success' => $success,
            'items' => $items,
            'promotionItems' => $promotionItems,
            'message' => $message,
        ];
    }

    /**
     * オーナーズブラグインインストール、アップデート
     *
     * @Route("/{_admin}/store/plugin/upgrade/{action}/{id}/{version}", requirements={"id":"\d+"}, name="admin_store_plugin_upgrade")
     */
    public function upgrade(Application $app, Request $request, $action, $id, $version)
    {

        $BaseInfo = $this->baseInfoRepository->get();

        $authKey = $BaseInfo->getAuthenticationKey();
        $message = '';

        if (!is_null($authKey)) {

            // オーナーズストア通信
            $url = $this->appConfig['owners_store_url'].'?method=download&product_id='.$id;
            list($json, $info) = $this->getRequestApi($request, $authKey, $url, $app);

            if ($json === false) {
                // 接続失敗時

                $message = $this->getResponseErrorMessage($info);

            } else {
                // 接続成功時

                $data = json_decode($json, true);

                if (isset($data['success'])) {
                    $success = $data['success'];
                    if ($success == '1') {
                        $tmpDir = null;
                        try {
                            $service = $this->pluginService;

                            $item = $data['item'];
                            $file = base64_decode($item['data']);
                            $extension = pathinfo($item['file_name'], PATHINFO_EXTENSION);

                            $tmpDir = $service->createTempDir();
                            $tmpFile = sha1(Str::random(32)).'.'.$extension;

                            // ファイル作成
                            $fs = new Filesystem();
                            $fs->dumpFile($tmpDir.'/'.$tmpFile, $file);

                            if ($action == 'install') {

                                $service->install($tmpDir.'/'.$tmpFile, $id);
                                $app->addSuccess('admin.plugin.install.complete', 'admin');

                            } else {
                                if ($action == 'update') {

                                    $Plugin = $this->pluginRepository->findOneBy(array('source' => $id));

                                    $service->update($Plugin, $tmpDir.'/'.$tmpFile);
                                    $app->addSuccess('admin.plugin.update.complete', 'admin');
                                }
                            }

                            $fs = new Filesystem();
                            $fs->remove($tmpDir);

                            // ダウンロード完了通知処理(正常終了時)
                            $url = $this->appConfig['owners_store_url'].'?method=commit&product_id='.$id.'&status=1&version='.$version;
                            $this->getRequestApi($request, $authKey, $url, $app);

                            return $app->redirect($app->url('admin_store_plugin'));

                        } catch (PluginException $e) {
                            if (!empty($tmpDir) && file_exists($tmpDir)) {
                                $fs = new Filesystem();
                                $fs->remove($tmpDir);
                            }
                            $message = $e->getMessage();
                        }

                    } else {
                        $message = $data['error_code'].' : '.$data['error_message'];
                    }
                } else {
                    $message = "EC-CUBEオーナーズストアにエラーが発生しています。";
                }
            }
        }

        // ダウンロード完了通知処理(エラー発生時)
        $url = $this->appConfig['owners_store_url']
            .'?method=commit&product_id='.$id
            .'&status=0&version='.$version
            .'&message='.urlencode($message);

        $this->getRequestApi($request, $authKey, $url, $app);

        $app->addError($message, 'admin');

        return $app->redirect($app->url('admin_store_plugin_owners_install'));
    }

    /**
     * 認証キー設定画面
     *
     * @Route("/{_admin}/store/plugin/authentication_setting", name="admin_store_authentication_setting")
     * @Template("Store/authentication_setting.twig")
     */
    public function authenticationSetting(Application $app, Request $request)
    {
        $BaseInfo = $this->baseInfoRepository->get();

        $builder = $this->formFactory
            ->createBuilder(FormType::class, $BaseInfo);
        $builder->add(
            'authentication_key',
            TextType::class,
            [
                'label' => '認証キー',
                'constraints' => [
                    new Assert\Regex(['pattern' => "/^[0-9a-zA-Z]+$/",]),
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
     * @return array
     */
    private function getRequestApi(Request $request, $authKey, $url, $app)
    {
        $curl = curl_init($url);

        $options = array(           // オプション配列
            //HEADER
            CURLOPT_HTTPHEADER => array(
                'Authorization: '.base64_encode($authKey),
                'x-eccube-store-url: '.base64_encode($request->getSchemeAndHttpHost().$request->getBasePath()),
                'x-eccube-store-version: '.base64_encode(Constant::VERSION),
            ),
            CURLOPT_HTTPGET => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FAILONERROR => true,
            CURLOPT_CAINFO => \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath(),
        );

        curl_setopt_array($curl, $options); /// オプション値を設定
        $result = curl_exec($curl);
        $info = curl_getinfo($curl);

        $message = curl_error($curl);
        $info['message'] = $message;
        curl_close($curl);

        $app->log('http get_info', $info);

        return array($result, $info);
    }

    /**
     * レスポンスのチェック
     *
     * @param $info
     * @return string
     */
    private function getResponseErrorMessage($info)
    {
        if (!empty($info)) {
            $statusCode = $info['http_code'];
            $message = $info['message'];

            $message = $statusCode.' : '.$message;

        } else {
            $message = "タイムアウトエラーまたはURLの指定に誤りがあります。";
        }

        return $message;
    }


    /**
     * フォルダ設置のみのプラグインを取得する.
     *
     * @param array $plugins
     * @param Application $app
     * @return array
     */
    protected function getUnregisteredPlugins(array $plugins, \Eccube\Application $app)
    {
        $finder = new Finder();
        $pluginCodes = array();

        // DB登録済みプラグインコードのみ取得
        foreach ($plugins as $key => $plugin) {
            $pluginCodes[] = $plugin->getCode();
        }
        // DB登録済みプラグインコードPluginディレクトリから排他
        $dirs = $finder->in($this->appConfig['plugin_realdir'])->depth(0)->directories();

        // プラグイン基本チェック
        $unregisteredPlugins = array();
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
            $unregisteredPlugins[$pluginCode]['enable'] = Constant::DISABLED;
            $unregisteredPlugins[$pluginCode]['code'] = isset($config['code']) ? $config['code'] : null;
        }

        return $unregisteredPlugins;
    }
}
