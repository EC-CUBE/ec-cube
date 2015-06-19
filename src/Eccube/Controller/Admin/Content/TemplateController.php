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

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Yaml\Yaml;

class TemplateController
{
    public function index(Application $app, Request $request)
    {
        $DeviceType = $app['orm.em']
            ->getRepository('Eccube\Entity\Master\DeviceType')
            ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);

        $Templates = $app['orm.em']
            ->getRepository('Eccube\Entity\Template')
            ->findBy(array('DeviceType' => $DeviceType));

        $form = $app->form()
            ->add('selected', 'hidden')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $Template = $app['orm.em']
                    ->getRepository('Eccube\Entity\Template')
                    ->find($form['selected']->getData());

                // path.ymlの再構築
                $file = $app['config']['root_dir'] . '/app/config/eccube/path.yml';
                $config = Yaml::parse($file);

                $templateCode = $Template->getCode();
                $config['template_code'] = $templateCode;
                $config['template_realdir'] = $config['root_dir'] . '/app/template/' . $templateCode;
                $config['template_html_realdir'] = $config['root_dir'] . '/html/template/' . $templateCode;
                $config['front_urlpath'] = $config['root_urlpath'] . '/template/' . $templateCode;

                file_put_contents($file, Yaml::dump($config));

                $app->addSuccess('admin.content.template.save.complete', 'admin');

                return $app->redirect($app->url('admin_content_template'));
            }
        }

        return $app->render('Content/template.twig', array(
            'form' => $form->createView(),
            'Templates' => $Templates,
        ));
    }

    public function download(Application $app, Request $request, $id)
    {
        /** @var $Template \Eccube\Entity\Template */
        $Template = $app['orm.em']
            ->getRepository('Eccube\Entity\Template')
            ->find($id);

        if (is_null($Template)) {
            throw new NotFoundHttpException();
        }

        // 該当テンプレートのディレクトリ
        $config = $app['config'];
        $templateCode = $Template->getCode();
        $targetRealDir = $config['root_dir'] . '/app/template/' . $templateCode;
        $targetHtmlRealDir = $config['root_dir'] . '/html/template/' . $templateCode;

        // 一時ディレクトリ
        $uniqId = sha1(uniqid(mt_rand(), true));
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
        /** @var $Template \Eccube\Entity\Template */
        $Template = $app['orm.em']
            ->getRepository('Eccube\Entity\Template')
            ->find($id);

        if (is_null($Template)) {
            throw new NotFoundHttpException();
        }

        // デフォルトテンプレート
        if ($Template->isDefaultTemplate()) {
            $app->addError('admin.content.template.delete.default.error', 'admin');

            return $app->redirect($app->url('admin_content_template'));
        }

        // 設定中のテンプレート
        if ($app['config']['template_code'] === $Template->getCode()) {
            $app->addError('admin.content.template.delete.current.error', 'admin');

            return $app->redirect($app->url('admin_content_template'));
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

        return $app->redirect($app->url('admin_content_template'));
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

            if ($this->isValid($form, $app)) {
                // 該当テンプレートのディレクトリ
                $config = $app['config'];
                $templateCode = $Template->getCode();
                $targetRealDir = $config['root_dir'] . '/app/template/' . $templateCode;
                $targetHtmlRealDir = $config['root_dir'] . '/html/template/' . $templateCode;

                // 一時ディレクトリ
                $uniqId = sha1(uniqid(mt_rand(), true));
                $tmpDir = $config['template_temp_realdir'] . '/' . $uniqId;
                $appDir = $tmpDir . '/app';
                $htmlDir = $tmpDir . '/html';

                // ファイル名
                $tarFile = $tmpDir . '/' . $templateCode . '.tar.gz';

                // ファイルを一時ディレクトリへ移動.
                $file = $form['file']->getData();
                $file->move($tmpDir, $templateCode . '.tar.gz');

                // 一時ディレクトリへ解凍する.
                $phar = new \PharData($tarFile);
                $phar->extractTo($tmpDir);

                // appディレクトリの存在チェック.
                if (!file_exists($appDir)) {
                    $form['file']->addError(new FormError('appディレクトリが見つかりません。ファイルの形式を確認してください。'));

                    return $app->render('Content/template_add.twig', array(
                        'form' => $form->createView(),
                    ));
                }

                // htmlディレクトリの存在チェック.
                if (!file_exists($htmlDir)) {
                    $form['file']->addError(new FormError('htmlディレクトリが見つかりません。ファイルの形式を確認してください。'));

                    return $app->render('Content/template_add.twig', array(
                        'form' => $form->createView(),
                    ));
                }

                // 一時ディレクトリから該当テンプレートのディレクトリへコピーする.
                $fs = new Filesystem();
                $fs->mirror($appDir, $targetRealDir);
                $fs->mirror($htmlDir, $targetHtmlRealDir);

                // 一時ディレクトリを削除.
                $fs->remove($tmpDir);

                $DeviceType = $app['orm.em']
                    ->getRepository('Eccube\Entity\Master\DeviceType')
                    ->find(\Eccube\Entity\Master\DeviceType::DEVICE_TYPE_PC);

                $Template->setDeviceType($DeviceType);

                $app['orm.em']->persist($Template);
                $app['orm.em']->flush();

                $app->addSuccess('admin.content.template.add.complete', 'admin');

                return $app->redirect($app->url('admin_content_template'));
            }
        }

        return $app->render('Content/template_add.twig', array(
            'form' => $form->createView(),
        ));
    }

    protected function isValid($form, $app)
    {
        // FormTypeのバリデーション.
        if (!$form->isValid()) {
            return false;
        }

        /** @var $Template \Eccube\Entity\Template */
        $Template = $app['orm.em']
            ->getRepository('Eccube\Entity\Template')
            ->findByCode($form['code']->getData());

        // テンプレートコードの重複チェック.
        if ($Template) {
            $form['code']->addError(new FormError('すでに登録されているテンプレートコードです。'));

            return false;
        }

        // ファイルアップロードのチェック
        $file = $form['file']->getData();
        if (is_null($file)) {
            $form['file']->addError(new FormError('ファイルが選択されていません。'));

            return false;
        }

        // ファイルのアップロードのチェック
        if (!$file->isValid()) {
            $form['file']->addError(new FormError('ファイルのアップロードに失敗しました。'));

            return false;
        }

        return true;
    }
}

