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
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class FileController
{
    private $app;

    private $form;

    public function index(Application $app)
    {
        $this->app = $app;
        $mainTitle = 'コンテンツ管理';
        $subTitle = 'ファイル管理';

        $this->form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->getForm();

        $htmlDir = $app['request']->server->get('DOCUMENT_ROOT') . $app['config']['root'];
        $topDir = $htmlDir . 'user_data';
        $nowDir = $app['request']->get('tree_select_file') ?: $topDir;

        $nowDirList = json_encode(explode('/', trim(str_replace($htmlDir, '', $nowDir), '/')));

        $isTopDir = ($topDir === $nowDir);
        $parentDir = substr($nowDir, 0, strrpos($nowDir, '/'));

        // jsとの結合が強い＋RWD対応でどうせ変えるため、一旦mode残す
        switch ($app['request']->get('mode')) {
            case 'create':
                $this->create($app);
                break;
            case 'delete':
                $this->delete($app);
                break;
            case 'upload':
                $this->upload($app);
                break;
            case 'download':
                if ($res = $this->download($app)) {
                    return $res;
                }
                break;
            default:
                break;
        }

        $tree = $this->getTree($topDir);
        $arrFileList = $this->getFileList($nowDir);

        $javascript = $this->getJsArrayList($tree);
        $onload = "eccube.fileManager.viewFileTree('tree', arrTree, '" . $nowDir . "', 'tree_select_file', 'tree_status', 'move');";

        return $app['view']->render('Admin/Content/index.twig', array(
            'form' => $this->form->createView(),
            'tpl_maintitle' => $mainTitle,
            'tpl_subtitle' => $subTitle,
            'tpl_onload' => $onload,
            'tpl_javascript' => $javascript,
            'top_dir' => $topDir,
            'tpl_is_top_dir' => $isTopDir,
            'tpl_now_dir' => $nowDir,
            'html_dir' => $htmlDir,
            'now_dir_list' => $nowDirList,
            'tpl_parent_dir' => $parentDir,
            'arrFileList' => $arrFileList,
        ));
    }

    public function view(Application $app)
    {
        return $app->sendFile($app['request']->get('file'));
    }

    public function create(Application $app)
    {
        $app['filesystem']->mkdir($app['request']->get('now_dir') . '/' . $app['request']->get('create_file'));
    }

    public function delete(Application $app)
    {
        if ($app['filesystem']->exists($app['request']->get('select_file'))) {
            $app['filesystem']->remove($app['request']->get('select_file'));
        }
    }

    public function download(Application $app)
    {
        if ($file = $app['request']->get('select_file')) {
            if (!is_dir($file)) {
                return $app->sendFile($file)
                    ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
            }
        }

        return;
    }

    public function upload(Application $app)
    {
        $this->form->handleRequest($app['request']);
        if ($this->form->isValid()) {
            $data = $this->form->getData();
            $filename = $data['file']->getClientOriginalName();
            $data['file']->move($app['request']->get('now_dir'), $filename);
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

    private function getTree($topDir)
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
        if ($this->app['request']->get('tree_status')) {
            $openDirs = explode('|', $this->app['request']->get('tree_status'));
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

    private function getFileList($nowDir)
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
