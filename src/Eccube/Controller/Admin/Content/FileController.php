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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Validator\Constraints as Assert;
use Eccube\Util\FilesystemUtil;

class FileController extends AbstractController
{
    const SJIS = 'sjis-win';
    const UTF = 'UTF-8';
    private $errors = [];
    private $encode = '';

    /**
     * FileController constructor.
     */
    public function __construct()
    {
        $this->encode = self::UTF;
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->encode = self::SJIS;
        }
    }

    /**
     * @Route("/%eccube_admin_route%/content/file_manager", name="admin_content_file")
     * @Template("@admin/Content/file.twig")
     */
    public function index(Request $request)
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class)
            ->add('create_file', TextType::class)
            ->getForm();

        // user_data_dir
        // $userDataDir = $this->getUserDataDir();
        // $topDir = $this->normalizePath($userDataDir);
        $topDir = '/';
        // user_data_dirの親ディレクトリ
        $htmlDir = $this->normalizePath($this->getUserDataDir().'/../');

        // カレントディレクトリ
        $nowDir = $this->checkDir($this->getUserDataDir($request->get('tree_select_file')), $this->getUserDataDir())
            ? $this->normalizePath($this->getUserDataDir($request->get('tree_select_file')))
            : $topDir;

        // パンくず表示用データ
        $nowDirList = json_encode(explode('/', trim(str_replace($htmlDir, '', $nowDir), '/')));
        $isTopDir = ($topDir === $nowDir);
        $parentDir = substr($nowDir, 0, strrpos($nowDir, '/'));

        if ('POST' === $request->getMethod()) {
            switch ($request->get('mode')) {
                case 'create':
                    $this->create($request);
                    break;
                case 'upload':
                    $this->upload($request);
                    break;
                default:
                    break;
            }
        }

        $tree = $this->getTree($this->getUserDataDir(), $request);
        $arrFileList = $this->getFileList($nowDir);
        $paths = $this->getPathsToArray($tree);
        $tree = $this->getTreeToArray($tree);

        return [
            'form' => $form->createView(),
            'tpl_javascript' => json_encode($tree),
            'top_dir' => $this->getJailDir($topDir),
            'tpl_is_top_dir' => $isTopDir,
            'tpl_now_dir' => $this->getJailDir($nowDir),
            'html_dir' => $this->getJailDir($htmlDir),
            'now_dir_list' => $nowDirList,
            'tpl_parent_dir' => $parentDir,
            'arrFileList' => $arrFileList,
            'errors' => $this->errors,
            'paths' => json_encode($paths),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/content/file_view", name="admin_content_file_view")
     */
    public function view(Request $request)
    {
        if ($this->checkDir($this->convertStrToServer($request->get('file')), $this->getUserDataDir())) {
            $file = $this->convertStrToServer($request->get('file'));
            setlocale(LC_ALL, 'ja_JP.UTF-8');

            return new BinaryFileResponse($file);
        }

        throw new NotFoundHttpException();
    }

    public function create(Request $request)
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class)
            ->add('create_file', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $fs = new Filesystem();
            $filename = $form->get('create_file')->getData();

            $pattern = '/[^[:alnum:]_.\\-]/';
            $pattern2 = "/^\.(.*)$/";
            if (empty($filename)) {
                $this->errors[] = ['message' => trans('file.text.error.folder_name')];
            } elseif (strlen($filename) > 0 && preg_match($pattern, $filename)) {
                $this->errors[] = ['message' => trans('file.text.error.folder_symbol')];
            } elseif (strlen($filename) > 0 && preg_match($pattern2, $filename)) {
                $this->errors[] = ['message' => trans('file.text.error.folder_period')];
            } else {
                $topDir = $this->getUserDataDir();
                $nowDir = $this->checkDir($request->get('now_dir'), $this->getUserDataDir())
                    ? $this->normalizePath($request->get('now_dir'))
                    : $topDir;
                $fs->mkdir($nowDir.'/'.$filename);

                $this->addSuccess('admin.create.complete', 'admin');
            }
        }
    }

    /**
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/content/file_delete", name="admin_content_file_delete")
     */
    public function delete(Request $request)
    {
        $this->isTokenValid();

        $topDir = $this->getUserDataDir();
        if ($this->checkDir($this->convertStrToServer($request->get('select_file')), $topDir)) {
            $fs = new Filesystem();
            if ($fs->exists($this->convertStrToServer($request->get('select_file')))) {
                $fs->remove($this->convertStrToServer($request->get('select_file')));
                $this->addSuccess('admin.delete.complete', 'admin');
            }
        }

        return $this->redirectToRoute('admin_content_file');
    }

    /**
     * @Route("/%eccube_admin_route%/content/file_download", name="admin_content_file_download")
     */
    public function download(Request $request)
    {
        $topDir = $this->getUserDataDir();
        $file = $this->convertStrToServer($request->get('select_file'));
        if ($this->checkDir($file, $topDir)) {
            if (!is_dir($file)) {
                $filename = $this->convertStrFromServer($file);
                setlocale(LC_ALL, 'ja_JP.UTF-8');
                $pathParts = pathinfo($file);

                $patterns = [
                    '/[a-zA-Z0-9!"#$%&()=~^|@`:*;+{}]/',
                    '/[- ,.<>?_[\]\/\\\\]/',
                    "/['\r\n\t\v\f]/",
                ];

                $str = preg_replace($patterns, '', $pathParts['basename']);
                if (strlen($str) === 0) {
                    return (new BinaryFileResponse($file))->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
                } else {
                    return new BinaryFileResponse($file, 200, [
                        'Content-Type' => 'aplication/octet-stream;',
                        'Content-Disposition' => "attachment; filename*=UTF-8\'\'".rawurlencode($this->convertStrFromServer($pathParts['basename'])),
                    ]);
                }
            }
        }
        throw new NotFoundHttpException();
    }

    public function upload(Request $request)
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class, [
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'file.text.error.file_not_selected',
                    ]),
                ],
            ])
            ->add('create_file', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->errors[] = ['message' => $error->getMessage()];
            }

            return;
        }

        $data = $form->getData();
        $topDir = $this->getUserDataDir();
        $nowDir = $this->getUserDataDir($request->get('now_dir'));
        if (!$this->checkDir($nowDir, $topDir)) {
            $this->errors[] = ['message' => 'file.text.error.invalid_upload_folder'];

            return;
        }

        $filename = $this->convertStrToServer($data['file']->getClientOriginalName());
        try {
            $data['file']->move($nowDir, $filename);
            $this->addSuccess('admin.content.file.upload_success', 'admin');
        } catch (FileException $e) {
            $this->errors[] = ['message' => $e->getMessage()];
        }
    }

    private function getTreeToArray($tree)
    {
        $arrTree = [];
        foreach ($tree as $key => $val) {
            $path = $this->getJailDir($val['path']);
            $arrTree[$key] = [
                $key,
                $val['type'],
                $path,
                $val['depth'],
                $val['open'] ? 'true' : 'false',
            ];
        }

        return $arrTree;
    }

    private function getPathsToArray($tree)
    {
        $paths = [];
        foreach ($tree as $val) {
            $paths[] = $this->getJailDir($val['path']);
        }

        return $paths;
    }

    private function getTree($topDir, $request)
    {
        $finder = Finder::create()->in($topDir)
            ->directories()
            ->sortByName();

        $tree = [];
        $tree[] = [
            'path' => $topDir,
            'type' => '_parent',
            'depth' => 0,
            'open' => true,
        ];

        $defaultDepth = count(explode('/', $topDir));

        $openDirs = [];
        if ($request->get('tree_status')) {
            $openDirs = explode('|', $request->get('tree_status'));
        }

        foreach ($finder as $dirs) {
            $path = $this->normalizePath($dirs->getRealPath());
            $type = (iterator_count(Finder::create()->in($path)->directories())) ? '_parent' : '_child';
            $depth = count(explode('/', $path)) - $defaultDepth;
            $tree[] = [
                'path' => $path,
                'type' => $type,
                'depth' => $depth,
                'open' => (in_array($path, $openDirs)) ? true : false,
            ];
        }

        return $tree;
    }

    private function getFileList($nowDir)
    {
        $topDir = $this->getuserDataDir();
        $filter = function (\SplFileInfo $file) use ($topDir) {
            $acceptPath = realpath($topDir);
            $targetPath = $file->getRealPath();

            return strpos($targetPath, $acceptPath) === 0;
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

        $arrFileList = [];
        foreach ($dirs as $dir) {
            $arrFileList[] = [
                'file_name' => $this->convertStrFromServer($dir->getFilename()),
                'file_path' => $this->convertStrFromServer($this->getJailDir($this->normalizePath($dir->getRealPath()))),
                'file_size' => FilesystemUtil::sizeToHumanReadable($dir->getSize()),
                'file_time' => $dir->getmTime(),
                'is_dir' => true,
            ];
        }
        foreach ($files as $file) {
            $arrFileList[] = [
                'file_name' => $this->convertStrFromServer($file->getFilename()),
                'file_path' => $this->convertStrFromServer($this->getJailDir($this->normalizePath($file->getRealPath()))),
                'file_size' => FilesystemUtil::sizeToHumanReadable($file->getSize()),
                'file_time' => $file->getmTime(),
                'is_dir' => false,
            ];
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

        return strpos($targetDir, $topDir) === 0;
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

    private function getUserDataDir($nowDir = null)
    {
        return rtrim($this->getParameter('kernel.project_dir').'/html/user_data'.$nowDir, '/');
    }

    private function getJailDir($path)
    {
        $realpath = realpath($path);
        $jailPath = str_replace($this->getUserDataDir(), '', $realpath);

        return $jailPath ? $jailPath : '/';
    }
}
