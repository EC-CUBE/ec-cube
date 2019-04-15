<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Util\Str;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

class TemplateController extends AbstractController
{

    /**
     * テンプレート一覧画面
     *
     * @param Application $app
     * @param Request $request
     */
    public function index(Application $app, Request $request)
    {

        $DeviceType = $app['eccube.repository.master.device_type']
            ->find(DeviceType::DEVICE_TYPE_PC);

        $Templates = $app['eccube.repository.template']
            ->findBy(array('DeviceType' => $DeviceType));

        $form = $app->form()
            ->add('selected', 'hidden')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $Template = $app['eccube.repository.template']
                    ->find($form['selected']->getData());

                // path.(yml|php)の再構築
                $file = $app['config']['root_dir'].'/app/config/eccube/path';
                if (file_exists($file.'.php')) {
                    $config = require $file.'.php';
                } elseif (file_exists($file.'.yml')) {
                    $config = Yaml::parse(file_get_contents($file.'.yml'));
                }

                $templateCode = $Template->getCode();
                $config['template_code'] = $templateCode;
                $config['template_realdir'] = $config['root_dir'].'/app/template/'.$templateCode;
                $config['template_html_realdir'] = $config['public_path_realdir'].'/template/'.$templateCode;
                $config['front_urlpath'] = $config['root_urlpath'].RELATIVE_PUBLIC_DIR_PATH.'/template/'.$templateCode;
                $config['block_realdir'] =$config['template_realdir'].'/Block';

                if (file_exists($file.'.php')) {
                    file_put_contents($file.'.php', sprintf('<?php return %s', var_export($config, true)).';');
                }
                if (file_exists($file.'.yml')) {
                    file_put_contents($file.'.yml', Yaml::dump($config));
                }

                $app->addSuccess('admin.content.template.save.complete', 'admin');

                return $app->redirect($app->url('admin_store_template'));
            }
        }

        return $app->render('Store/template.twig', array(
            'form' => $form->createView(),
            'Templates' => $Templates,
        ));
    }

    /**
     * テンプレート一覧からのダウンロード
     *
     * @param Application $app
     * @param Request $request
     * @param $id
     */
    public function download(Application $app, Request $request, $id)
    {
        /** @var $Template \Eccube\Entity\Template */
        $Template = $app['eccube.repository.template']->find($id);

        if (!$Template) {
            throw new NotFoundHttpException();
        }

        // 該当テンプレートのディレクトリ
        $config = $app['config'];
        $templateCode = $Template->getCode();
        $targetRealDir = $config['root_dir'] . '/app/template/' . $templateCode;
        $targetHtmlRealDir = $config['root_dir'] . '/html/template/' . $templateCode;

        // 一時ディレクトリ
        $uniqId = sha1(Str::random(32));
        $tmpDir = $config['template_temp_realdir'] . '/' . $uniqId;
        $appDir = $tmpDir . '/app';
        $htmlDir = $tmpDir . '/html';

        // ファイル名
        $tarFile = $config['template_temp_realdir'] . '/' . $uniqId . '.tar';
        $tarGzFile = $tarFile . '.gz';
        $downloadFileName = $Template->getCode() . '.tar.gz';

        // 該当テンプレートを一時ディレクトリへコピーする.
        $fs = new Filesystem();
        $fs->mkdir(array($appDir, $htmlDir));
        $fs->mirror($targetRealDir, $appDir);
        $fs->mirror($targetHtmlRealDir, $htmlDir);

        // tar.gzファイルに圧縮する.
        $phar = new \PharData($tarFile);
        $phar->buildFromDirectory($tmpDir);
        // appディレクトリがない場合は, 空ディレクトリを追加
        // @see https://github.com/EC-CUBE/ec-cube/issues/742
        if (empty($phar['app'])) {
            $phar->addEmptyDir('app');
        }
        $phar->compress(\Phar::GZ);

        // ダウンロード完了後にファイルを削除する.
        // http://stackoverflow.com/questions/15238897/removing-file-after-delivering-response-with-silex-symfony
        $app->finish(function (Request $request, Response $response, \Silex\Application $app) use (
            $tmpDir,
            $tarFile,
            $tarGzFile
        ) {
            $app['monolog']->addDebug('remove temp file: ' . $tmpDir);
            $app['monolog']->addDebug('remove temp file: ' . $tarFile);
            $app['monolog']->addDebug('remove temp file: ' . $tarGzFile);
            $fs = new Filesystem();
            $fs->remove($tmpDir);
            $fs->remove($tarFile);
            $fs->remove($tarGzFile);
        });

        return $app
            ->sendFile($tarGzFile)
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadFileName);
    }

    public function delete(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);

        /** @var $Template \Eccube\Entity\Template */
        $Template = $app['eccube.repository.template']->find($id);

        if (!$Template) {
            $app->deleteMessage();
            return $app->redirect($app->url('admin_store_template'));
        }

        // デフォルトテンプレート
        if ($Template->isDefaultTemplate()) {
            $app->addError('admin.content.template.delete.default.error', 'admin');

            return $app->redirect($app->url('admin_store_template'));
        }

        // 設定中のテンプレート
        if ($app['config']['template_code'] === $Template->getCode()) {
            $app->addError('admin.content.template.delete.current.error', 'admin');

            return $app->redirect($app->url('admin_store_template'));
        }

        // テンプレートディレクトリの削除
        $config = $app['config'];
        $templateCode = $Template->getCode();
        $targetRealDir = $config['root_dir'] . '/app/template/' . $templateCode;
        $targetHtmlRealDir = $config['root_dir'] . '/html/template/' . $templateCode;

        $fs = new Filesystem();
        $fs->remove($targetRealDir);
        $fs->remove($targetHtmlRealDir);

        // テーブルからも削除
        $app['orm.em']->remove($Template);
        $app['orm.em']->flush();

        $app->addSuccess('admin.content.template.delete.complete', 'admin');

        return $app->redirect($app->url('admin_store_template'));
    }

    public function add(Application $app, Request $request)
    {
        /** @var $Template \Eccube\Entity\Template */
        $Template = new \Eccube\Entity\Template();

        $form = $app['form.factory']
            ->createBuilder('admin_template', $Template)
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {

                /** @var $Template \Eccube\Entity\Template */
                $tem = $app['eccube.repository.template']
                    ->findByCode($form['code']->getData());

                // テンプレートコードの重複チェック.
                if ($tem) {
                    $form['code']->addError(new FormError('すでに登録されているテンプレートコードです。'));

                    return false;
                }

                // 該当テンプレートのディレクトリ
                $config = $app['config'];
                $templateCode = $Template->getCode();
                $targetRealDir = $config['root_dir'] . '/app/template/' . $templateCode;
                $targetHtmlRealDir = $config['root_dir'] . '/html/template/' . $templateCode;

                // 一時ディレクトリ
                $uniqId = sha1(Str::random(32));
                $tmpDir = $config['template_temp_realdir'] . '/' . $uniqId;
                $appDir = $tmpDir . '/app';
                $htmlDir = $tmpDir . '/html';

                $formFile = $form['file']->getData();
                // ファイル名
                $archive = $templateCode . '.' . $formFile->getClientOriginalExtension();

                // ファイルを一時ディレクトリへ移動.
                $formFile->move($tmpDir, $archive);

                // 一時ディレクトリへ解凍する.
                try {
                    if ($formFile->getClientOriginalExtension() == 'zip') {
                        $zip = new \ZipArchive();
                        $zip->open($tmpDir . '/' . $archive);
                        $zip->extractTo($tmpDir);
                        $zip->close();
                    } else {
                        $phar = new \PharData($tmpDir . '/' . $archive);
                        $phar->extractTo($tmpDir, null, true);
                    }
                } catch (\Exception $e) {
                    $form['file']->addError(new FormError('アップロードに失敗しました。圧縮ファイルを確認してください。'));

                    return $app->render('Store/template_add.twig', array(
                        'form' => $form->createView(),
                    ));
                }

                $fs = new Filesystem();

                // appディレクトリの存在チェック.
                if (!file_exists($appDir)) {
                    $fs->mkdir($appDir);
                }

                // htmlディレクトリの存在チェック.
                if (!file_exists($htmlDir)) {
                    $fs->mkdir($htmlDir);
                }

                // 一時ディレクトリから該当テンプレートのディレクトリへコピーする.
                $fs->mirror($appDir, $targetRealDir);
                $fs->mirror($htmlDir, $targetHtmlRealDir);

                // 一時ディレクトリを削除.
                $fs->remove($tmpDir);

                $DeviceType = $app['eccube.repository.master.device_type']
                    ->find(DeviceType::DEVICE_TYPE_PC);

                $Template->setDeviceType($DeviceType);

                $app['orm.em']->persist($Template);
                $app['orm.em']->flush();

                $app->addSuccess('admin.content.template.add.complete', 'admin');

                return $app->redirect($app->url('admin_store_template'));
            }
        }

        return $app->render('Store/template_add.twig', array(
            'form' => $form->createView(),
        ));
    }

}
