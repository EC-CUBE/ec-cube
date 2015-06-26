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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController
{
    private $error = null;

    public function index(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->getForm();

        $htmlDir = realpath(str_replace($app['config']['user_data_route'], '', $app['config']['user_data_realdir']));
        $topDir = realpath($app['config']['user_data_realdir']);
        $nowDir = $request->get('tree_select_file') ?: $topDir . '/';

        $nowDirList = json_encode(explode('/', trim(str_replace($htmlDir, '', $nowDir), '/')));

        $isTopDir = ($topDir === $nowDir);
        $parentDir = substr($nowDir, 0, strrpos($nowDir, '/'));

        switch ($request->get('mode')) {
            case 'create':
                $this->create($app, $request);
                break;
            case 'delete':
                $this->delete($app, $request);
                break;
            case 'upload':
                $this->upload($app, $request);
                break;
            case 'download':
                if ($res = $this->download($app, $request)) {
                    return $res;
                }
                break;
            default:
                break;
        }

        $tree = $this->getTree($topDir, $request);
        $arrFileList = $this->getFileList($app, $nowDir);

        $javascript = $this->getJsArrayList($tree);
        $onload = "eccube.fileManager.viewFileTree('tree', arrTree, '" . $nowDir . "', 'tree_select_file', 'tree_status', 'move');";

        return $app->render('Content/file.twig', array(
            'form' => $form->createView(),
            'tpl_onload' => $onload,
            'tpl_javascript' => $javascript,
            'top_dir' => $topDir,
            'tpl_is_top_dir' => $isTopDir,
            'tpl_now_dir' => $nowDir,
            'html_dir' => $htmlDir,
            'now_dir_list' => $nowDirList,
            'tpl_parent_dir' => $parentDir,
            'arrFileList' => $arrFileList,
            'error' => $this->error,
        ));
    }

    public function view(Application $app, Request $request)
    {
        return $app->sendFile($request->get('file'));
    }

    public function create(Application $app, Request $request)
    {
        $fs = new Filesystem();
        $filename = $request->get('create_file');

        $pattern = "/[^[:alnum:]_.\\-]/";
        if (empty($filename)) {
            $this->error = array('message' => 'フォルダ作成名が入力されていません。');
        } else if (strlen($filename) > 0 && preg_match($pattern, $filename)) {
            $this->error = array('message' => 'ファイル名には、英数字、記号（_ - .）のみを入力して下さい。');
        } else  {
            $fs->mkdir($request->get('now_dir') . '/' . $filename);
        }
    }

    public function delete(Application $app, Request $request)
    {
        $fs = new Filesystem();
        if ($fs->exists($request->get('select_file'))) {
            $fs->remove($request->get('select_file'));
        }
    }

    public function download(Application $app, Request $request)
    {
        if ($file = $request->get('select_file')) {
            if (!is_dir($file)) {
                $pathParts = pathinfo($file);

                $patterns = array(
                        '/[a-zA-Z0-9!"#$%&()=~^|@`:*;+{}]/',
                        '/[- ,.<>?_[\]\/\\\\]/',
                        "/['\r\n\t\v\f]/",
                    );

                $str = preg_replace($patterns, '', $pathParts['basename']);
                if (strlen($str) === 0) {
                    return $app->sendFile($file)->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
                } else {
                    return $app->sendFile($file)->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, ord($pathParts['basename']));
                }

            }
        }

        return;
    }

    public function upload(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            if (!empty($data['file'])) {
                $filename = $data['file']->getClientOriginalName();
                $data['file']->move($request->get('now_dir'), $filename);
            } else {
                $this->error = array('message' => 'ファイルが選択されていません。');
            }
        }
    }

    private function getJsArrayList($tree)
    {
        $str = "arrTree = new Array();\n";
        foreach ($tree as $key => $val) {
            $str .= 'arrTree[' . $key . "] = new Array(" . $key . ", '" . $val['type'] . "', '" . $val['path'] . "', " . $val['rank'] . ',';
            if ($val['open']) {
                $str .= "true);\n";
            } else {
                $str .= "false);\n";
            }
        }

        return $str;
    }

    private function getTree($topDir, $request)
    {
        $finder = Finder::create()->in($topDir)
            ->directories()
            ->sortByName();
        $dirs = iterator_to_array($finder);

        $tree = array();
        $tree[] = array(
            'path' => $topDir,
            'type' => '_parent',
            'rank' => 0,
            'open' => true,
        );

        $defaultRank = count(explode('/', $topDir));

        $openDirs = array();
        if ($request->get('tree_status')) {
            $openDirs = explode('|', $request->get('tree_status'));
        }

        foreach ($finder as $dirs) {
            $path = $dirs->getRealPath();
            $type = (iterator_count(Finder::create()->in($path)->directories())) ? '_parent' : '_child';
            $rank = count(explode('/', $path)) - $defaultRank;

            $tree[] = array(
                'path' => $path,
                'type' => $type,
                'rank' => $rank,
                'open' => (in_array($path, $openDirs)) ? true : false,
            );
        }

        return $tree;
    }

    private function getFileList($app, $nowDir)
    {
        $dirFinder = Finder::create()
            ->in($nowDir)
            ->directories()
            ->sortByName()
            ->depth(0);
        $fileFinder = Finder::create()
            ->in($nowDir)
            ->files()
            ->sortByName()
            ->depth(0);
        $dirs = iterator_to_array($dirFinder);
        $files = iterator_to_array($fileFinder);

        $arrFileList = array();
        foreach ($dirs as $dir) {
            $arrFileList[] = array(
                'file_name' => $dir->getFilename(),
                'file_path' => $dir->getRealPath(),
                'file_size' => $dir->getSize(),
                'file_time' => date("Y/m/d", $dir->getmTime()),
                'is_dir' => true,
            );
        }
        foreach ($files as $file) {
            $arrFileList[] = array(
                'file_name' => $file->getFilename(),
                'file_path' => $file->getRealPath(),
                'file_size' => $file->getSize(),
                'file_time' => date("Y/m/d", $file->getmTime()),
                'is_dir' => false,
            );
        }

        return $arrFileList;
    }
}