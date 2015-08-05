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


namespace Eccube\Controller\Admin\Setting\Store;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Controller\AbstractController;
use Eccube\Exception\PluginException;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PluginController extends AbstractController
{
    public function index(Application $app)
    {
        $repo = $app['eccube.repository.plugin'];

        $pluginForms = array();
        $Plugins = $repo->findBy(array(), array('id' => 'ASC'));
        $configPages = array();

        foreach ($repo->findAll() as $Plugin) {
            $builder = $app['form.factory']->createNamedBuilder('form' . $Plugin->getId(), 'plugin_management', null, array(
                'plugin_id' => $Plugin->getId(),
                'enable' => $Plugin->getEnable()
            ));
            $pluginForms[$Plugin->getId()] = $builder->getForm()->createView();


            try {
                $configPages[$Plugin->getCode()] = $app->url('plugin_' . $Plugin->getCode() . '_config');
            } catch (\Exception $e) {
                // プラグインで設定画面のルートが定義されていない場合は無視
            }
        }

        return $app->render('Setting/Store/plugin.twig', array(
            'plugin_forms' => $pluginForms,
            'Plugins' => $Plugins,
            'configPages' => $configPages
        ));

    }

    public function install(Application $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('plugin_local_install')
            ->getForm();

        $errors = array();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                try {
                    $service = $app['eccube.service.plugin'];

                    $formFile = $form['plugin_archive']->getData();

                    $tmpDir = $service->createTempDir();
                    $tmpFile = sha1(Str::random(32)) . '.' . $formFile->getClientOriginalExtension(); // 拡張子を付けないとpharが動かないので付ける

                    $form['plugin_archive']->getData()->move($tmpDir, $tmpFile);

                    $service->install($tmpDir . '/' . $tmpFile);

                    $fs = new Filesystem();
                    $fs->remove($tmpDir);

                    return $app->redirect($app->url('admin_setting_store_plugin'));

                } catch (PluginException $e) {
                    if (file_exists($tmpDir)) {
                        $fs = new Filesystem();
                        $fs->remove($tmpDir);
                    }
                    $errors[] = $e;
                }
            }
        }

        // オーナーズストアからダウンロード可能プラグイン情報を取得
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $authKey = $BaseInfo->getAuthenticationKey();
        $authResult = true;
        $items = array();
        $message = '';
        if (!is_null($authKey)) {

            // 共通リクエストヘッダー取得
            $opts = $this->getRequestOption($app, $request, $authKey);
            $context = stream_context_create($opts);
            $url = $app['config']['owners_store_url'] . '?method=list';
            $json = @file_get_contents($url, false, $context);

            if ($json === false) {
                // 接続失敗時
                $success = 0;

                if (!empty($http_response_header)) {
                    list($version, $statusCode, $message) = explode(' ', $http_response_header[0], 3);

                    switch ($statusCode) {
                        case '404':
                            $message = $statusCode . ' : ' . $message;
                            break;
                        case '500':
                            $message = $statusCode . ' : ' . $message;
                            break;
                        default:
                            $message = "EC-CUBEオーナーズストアにエラーが発生しています。";
                            break;
                    }
                } else {
                    $message = "タイムアウトエラーまたはURLの指定に誤りがあります。";
                }


            } else {
                // 接続成功時

                $data = json_decode($json, true);

                if (isset($data['success'])) {
                    $success = $data['success'];
                    if ($success == '1') {
                        $items = array();

                        // 既にインストールされているかどうか確認
                        $Plugins = $app['eccube.repository.plugin']->findAll();
                        if ($Plugins) {
                            $status = false;
                            foreach ($data['item'] as $item) {
                                foreach($Plugins as $plugin) {
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
                        } else {
                            $items = $data['item'];
                        }

                        // EC-CUBEのバージョンチェック
                        $arr = $items;
                        $items = array();
                        foreach($arr as $item) {
                            if (array_search(Constant::VERSION, $item['eccube_version'])) {
                                $item['version_check'] = 1;
                            } else {
                                $item['version_check'] = 0;
                            }
                            $items[] = $item;
                        }

                    } else {
                        $message = $data['error_message'];
                    }
                } else {
                    $success = 0;
                    $message = "EC-CUBEオーナーズストアにエラーが発生しています。";
                }
            }


        } else {
            $authResult = false;
        }


        return $app->render('Setting/Store/plugin_install.twig', array(
            'form' => $form->createView(),
            'errors' => $errors,
            'authResult' => $authResult,
            'success' => $success,
            'items' => $items,
            'message' => $message,
        ));

    }

    public function update(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);

        $form = $app['form.factory']
            ->createNamedBuilder('form' . $id, 'plugin_management', null, array(
                'plugin_id' => null, // placeHolder
                'enable' => null,
            ))
            ->getForm();

        $form->handleRequest($app['request']);

        $tmpDir = $app['eccube.service.plugin']->createTempDir();
        $tmpFile = sha1(Str::random(32)) . ".tar";

        $form['plugin_archive']->getData()->move($tmpDir, $tmpFile);
        $app['eccube.service.plugin']->update($Plugin, $tmpDir . '/' . $tmpFile);
        $app->addSuccess('admin.plugin.update.complete', 'admin');

        $fs = new Filesystem();
        $fs->remove($tmpDir . '/' . $tmpFile);

        return $app->redirect($app->url('admin_setting_store_plugin'));
    }

    public function enable(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        if ($Plugin->getEnable() == 1) {
            $app->addError('admin.plugin.already.enable', 'admin');
        } else {
            $app['eccube.service.plugin']->enable($Plugin);
            $app->addSuccess('admin.plugin.enable.complete');
        }

        return $app->redirect($app->url('admin_setting_store_plugin'));
    }

    public function disable(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        if ($Plugin->getEnable() == 1) {
            $app['eccube.service.plugin']->disable($Plugin);
            $app->addSuccess('admin.plugin.disable.complete');
        } else {
            $app->addError('admin.plugin.already.disable', 'admin');
        }

        return $app->redirect($app->url('admin_setting_store_plugin'));
    }


    public function uninstall(Application $app, $id)
    {
        $Plugin = $app['eccube.repository.plugin']
            ->find($id);
        $app['eccube.service.plugin']->uninstall($Plugin);

        return $app->redirect($app->url('admin_setting_store_plugin'));
    }

    function handler(Application $app)
    {
        $handlers = $app['eccube.repository.plugin_event_handler']->getHandlers();

        // 一次元配列からイベント毎の二次元配列に変換する
        $HandlersPerEvent = array();
        foreach ($handlers as $handler) {
            $HandlersPerEvent[$handler->getEvent()][$handler->getHandlerType()][] = $handler;
        }

        return $app->render('Setting/Store/plugin_handler.twig', array(
            'handlersPerEvent' => $HandlersPerEvent
        ));

    }

    function handler_up(Application $app, $handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority($repo->find($handlerId));

        return $app->redirect($app->url('admin_setting_store_plugin_handler'));
    }

    function handler_down(Application $app, $handlerId)
    {
        $repo = $app['eccube.repository.plugin_event_handler'];
        $repo->upPriority($repo->find($handlerId), false);

        return $app->redirect($app->url('admin_setting_store_plugin_handler'));
    }

    public function authenticationSetting(Application $app, Request $request)
    {

        $form = $app->form()->getForm();

        $BaseInfo = $app['eccube.repository.base_info']->get();

        // 認証キーの取得
        $form->add(
            'authentication_key', 'text', array(
            'label' => '認証キー',
            'constraints' => array(
                new Assert\Regex(array(
                    'pattern' => "/^[0-9a-zA-Z]+$/",
                )),
            ),
            'data' => $BaseInfo->getAuthenticationKey(),
        ));

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();

                // 認証キーの登録
                $BaseInfo->setAuthenticationKey($data['authentication_key']);
                $app['orm.em']->flush($BaseInfo);

                $app->addSuccess('admin.plugin.authentication.setting.complete', 'admin');

            }
        }


        return $app->render('Setting/Store/authentication_setting.twig', array(
            'form' => $form->createView(),
        ));

    }


    /**
     * リクエスト時の共通ヘッダ
     * @return array
     */
    private function getRequestOption($app, $request, $authKey)
    {
        return array(
            'http' => array(
                'method' => 'GET',
                'ignore_errors' => false,
                'timeout' => 60,
                'header' => array(
                    'Authorization: ' . base64_encode($authKey),
                    'x-eccube-store-url: ' . base64_encode($request->getBaseUrl() . '/' . $app['config']['admin_route']),
                    'x-eccube-store-version: ' . base64_encode(Constant::VERSION)
                )
            )
        );
    }
}
