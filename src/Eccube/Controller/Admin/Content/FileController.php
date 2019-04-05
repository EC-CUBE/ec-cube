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


namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileController extends AbstractController
{
    const SJIS = 'sjis-win';
    const UTF = 'UTF-8';
    private $error = null;
    private $encode = '';

    public function __construct(){
        $this->encode = self::UTF;
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->encode = self::SJIS;
        }
    }

    public function index(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->add('create_file', 'text')
            ->getForm();

        // user_data_dir
        $topDir = $this->normalizePath($app['config']['user_data_realdir']);
        // user_data_dirの親ディレクトリ
        $htmlDir = $this->normalizePath($topDir.'/../');
        // カレントディレクトリ
        $nowDir = $this->checkDir($request->get('tree_select_file'), $topDir)
            ? $this->normalizePath($request->get('tree_select_file'))
            : $topDir;
        // パンくず表示用データ
        $nowDirList = json_encode(explode('/', trim(str_replace($htmlDir, '', $nowDir), '/')));

        $isTopDir = ($topDir === $nowDir);
        $parentDir = substr($nowDir, 0, strrpos($nowDir, '/'));

        if ('POST' === $request->getMethod()) {
            switch ($request->get('mode')) {
                case 'create':
                    $this->create($app, $request);
                    break;
                case 'upload':
                    $this->upload($app, $request);
                    break;
                default:
                    break;
            }
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
        $topDir = $app['config']['user_data_realdir'];
        if ($this->checkDir($this->convertStrToServer($request->get('file')), $topDir)) {
            $file = $this->convertStrToServer($request->get('file'));
            setlocale(LC_ALL, "ja_JP.UTF-8");
            return $app->sendFile($file);
        }

        throw new NotFoundHttpException();
    }

    public function create(Application $app, Request $request)
    {

        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->add('create_file', 'text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $fs = new Filesystem();
            $filename = $form->get('create_file')->getData();

            $pattern = "/[^[:alnum:]_.\\-]/";
            $pattern2 = "/^\.(.*)$/";
            if (empty($filename)) {
                $this->error = array('message' => 'フォルダ作成名が入力されていません。');
            } elseif (strlen($filename) > 0 && preg_match($pattern, $filename)) {
                $this->error = array('message' => 'フォルダ名には、英数字、記号（_ - .）のみを入力して下さい。');
            } elseif (strlen($filename) > 0 && preg_match($pattern2, $filename)) {
                $this->error = array('message' => '.から始まるフォルダ名は作成できません。');
            } else {
                $topDir = $app['config']['user_data_realdir'];
                $nowDir = $this->checkDir($request->get('now_dir'), $topDir)
                    ? $this->normalizePath($request->get('now_dir'))
                    : $topDir;
                $fs->mkdir($nowDir . '/' . $filename);
            }
        }
    }

    public function delete(Application $app, Request $request)
    {

        $this->isTokenValid($app);

        $topDir = $app['config']['user_data_realdir'];
        if ($this->checkDir($this->convertStrToServer($request->get('select_file')), $topDir)) {
            $fs = new Filesystem();
            if ($fs->exists($this->convertStrToServer($request->get('select_file')))) {
                $fs->remove($this->convertStrToServer($request->get('select_file')));
            }
        }

        return $app->redirect($app->url('admin_content_file'));
    }

    public function download(Application $app, Request $request)
    {
        $topDir = $app['config']['user_data_realdir'];
        $file = $this->convertStrToServer($request->get('select_file'));
        if ($this->checkDir($file, $topDir)) {
            if (!is_dir($file)) {
                $filename = $this->convertStrFromServer($file);
                setlocale(LC_ALL, 'ja_JP.UTF-8');
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
                    return $app->sendFile($file, 200, array(
                        "Content-Type" => "aplication/octet-stream;",
                        "Content-Disposition" => "attachment; filename*=UTF-8\'\'".rawurlencode($this->convertStrFromServer($pathParts['basename']))
                    ));
                }
            }
        }
        throw new NotFoundHttpException();
    }

    public function upload(Application $app, Request $request)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('file', 'file')
            ->add('create_file', 'text')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            if (empty($data['file'])) {
                $this->error = array('message' => 'ファイルが選択されていません。');
            } else {
                $topDir = $app['config']['user_data_realdir'];
                if ($this->checkDir($request->get('now_dir'), $topDir)) {
                    $filename = $this->convertStrToServer($data['file']->getClientOriginalName());
                    $data['file']->move($request->get('now_dir'), $filename);
                }
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
            $path = $this->normalizePath($dirs->getRealPath());
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
        $topDir = $app['config']['user_data_realdir'];
        $filter = function (\SplFileInfo $file) use ($topDir) {
            $acceptPath = realpath($topDir);
            $targetPath = $file->getRealPath();
            return (strpos($targetPath, $acceptPath) === 0);
        };

        $dirFinder = Finder::create()
            ->filter($filter)
            ->in($nowDir)
            ->directories()
            ->sortByName()
            ->depth(0);
        $fileFinder = Finder::create()
            ->filter($filter)
            ->in($nowDir)
            ->files()
            ->sortByName()
            ->depth(0);
        $dirs = iterator_to_array($dirFinder);
        $files = iterator_to_array($fileFinder);

        $arrFileList = array();
        foreach ($dirs as $dir) {
            $arrFileList[] = array(
                'file_name' => $this->convertStrFromServer($dir->getFilename()),
                'file_path' => $this->convertStrFromServer($this->normalizePath($dir->getRealPath())),
                'file_size' => $dir->getSize(),
                'file_time' => date("Y/m/d", $dir->getmTime()),
                'is_dir' => true,
            );
        }
        foreach ($files as $file) {
            $arrFileList[] = array(
                'file_name' => $this->convertStrFromServer($file->getFilename()),
                'file_path' => $this->convertStrFromServer($this->normalizePath($file->getRealPath())),
                'file_size' => $file->getSize(),
                'file_time' => date("Y/m/d", $file->getmTime()),
                'is_dir' => false,
            );
        }

        return $arrFileList;
    }

    protected function normalizePath($path)
    {
        return str_replace('\\', '/', realpath($path));
    }

    protected function checkDir($targetDir, $topDir)
    {
        $targetDir = realpath($targetDir);
        $topDir = realpath($topDir);
        return (strpos($targetDir, $topDir) === 0);
    }

    private function convertStrFromServer($target)
    {
        if ($this->encode == self::SJIS) {
            return mb_convert_encoding($target, self::UTF, self::SJIS);
        }
        return $target;
    }

    private function convertStrToServer($target)
    {
        if ($this->encode == self::SJIS) {
            return mb_convert_encoding($target, self::SJIS, self::UTF);
        }
        return $target;
    }
}
