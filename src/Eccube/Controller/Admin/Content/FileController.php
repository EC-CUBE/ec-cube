<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\EventListener\RestrictFileUploadListener;
use Eccube\Exception\ContentValidationException;
use Eccube\Service\Content\UserDataFileService;
use Eccube\Util\FilesystemUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

class FileController extends AbstractController
{
    public const SJIS = 'sjis-win';
    public const UTF = 'UTF-8';

    /**
     * @var array<int, array<string, string>>
     */
    private array $errors = [];
    private string $encode;

    public function __construct(private readonly UserDataFileService $userDataFileService)
    {
        $this->encode = self::UTF;
        if ('\\' === DIRECTORY_SEPARATOR) {
            $this->encode = self::SJIS;
        }
    }

    /**
     * @return array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/content/file_manager', name: 'admin_content_file', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Content/file.twig')]
    public function index(Request $request): array
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class, [
                'multiple' => true,
                'attr' => [
                    'multiple' => 'multiple',
                ],
            ])
            ->add('create_file', TextType::class)
            ->getForm();

        // user_data_dir
        $userDataDir = $this->userDataFileService->getRootDir();
        $topDir = $this->normalizePath($userDataDir);
        //        $topDir = '/';
        // user_data_dirの親ディレクトリ
        $htmlDir = $this->normalizePath($userDataDir.'/../');

        // カレントディレクトリ. user_data の外を指す場合はルートへフォールバックする.
        // tryResolve() は配置予定のパス (未作成) も解決するため, ここでは実在も確かめる.
        // 実在しないディレクトリを Finder::in() へ渡すと DirectoryNotFoundException になる
        $selected = $this->userDataFileService->tryResolve($request->get('tree_select_file'));
        $nowDir = null === $selected || !is_dir($selected) ? $topDir : $this->normalizePath($selected);

        // パンくず表示用データ
        $nowDirList = json_encode(explode('/', trim(str_replace($htmlDir, '', $nowDir), '/')), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        $jailNowDir = $this->userDataFileService->toRelative($nowDir);
        // $jailNowDir は user_data からの相対表記のため, 比較相手も同じ表記へ揃える
        $isTopDir = ($this->userDataFileService->toRelative($topDir) === $jailNowDir);
        $parentDir = substr($nowDir, 0, strrpos($nowDir, '/'));

        if ('POST' === $request->getMethod()) {
            $mode = $request->get('mode');

            // ディレクトリの移動も POST のため, RestrictFileUploadListener は
            // このルートの書き込みをメソッドで判別できない. ここで書き込む操作だけを拒否する
            if (in_array($mode, ['create', 'upload'], true)
                && $request->attributes->getBoolean(RestrictFileUploadListener::READ_ONLY_ATTRIBUTE)) {
                throw new AccessDeniedHttpException(trans('exception.error_message_restrict_url'));
            }

            switch ($mode) {
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
        $tree = $this->getTree($userDataDir, $request);
        $arrFileList = $this->getFileList($nowDir);
        $paths = $this->getPathsToArray($tree);
        $tree = $this->getTreeToArray($tree);

        return [
            'form' => $form->createView(),
            'tpl_javascript' => json_encode($tree, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
            'top_dir' => $this->userDataFileService->toRelative($topDir),
            'tpl_is_top_dir' => $isTopDir,
            'tpl_now_dir' => $jailNowDir,
            'html_dir' => $this->userDataFileService->toRelative($htmlDir),
            'now_dir_list' => $nowDirList,
            'tpl_parent_dir' => $this->userDataFileService->toRelative($parentDir),
            'arrFileList' => $arrFileList,
            'errors' => $this->errors,
            'paths' => json_encode($paths, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT),
        ];
    }

    /**
     * @throws NotFoundHttpException
     */
    #[Route(path: '/%eccube_admin_route%/content/file_view', name: 'admin_content_file_view', methods: ['GET'])]
    public function view(Request $request): BinaryFileResponse
    {
        $file = $this->userDataFileService->tryResolve($this->convertStrToServer((string) $request->get('file')));
        // tryResolve() は配置予定のパス (未作成) も解決するため, 実在の確認は呼び出し側で行う
        if (null !== $file && is_file($file)) {
            setlocale(LC_ALL, 'ja_JP.UTF-8');

            return new BinaryFileResponse($file);
        }

        throw new NotFoundHttpException();
    }

    /**
     * Create directory
     *
     * @throws IOException
     */
    public function create(Request $request): void
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class, [
                'multiple' => true,
                'attr' => [
                    'multiple' => 'multiple',
                ],
            ])
            ->add('create_file', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    // 検証の定義は CLI (eccube:user-data:*) と共有する
                    new Assert\Regex(pattern: UserDataFileService::DIRECTORY_NAME_DENY_PATTERN, match: false, message: 'admin.content.file.folder_name_symbol_error'),
                    new Assert\Regex(pattern: UserDataFileService::DOT_PREFIX_PATTERN, match: false, message: 'admin.content.file.folder_name_period_error'),
                ],
            ])
            ->getForm();

        $form->handleRequest($request);
        if (!$form->isValid()) {
            foreach ($form->getErrors(true) as $error) {
                $this->errors[] = ['message' => $error->getMessage()];
            }

            return;
        }

        $fs = new Filesystem();
        $filename = $form->get('create_file')->getData();

        try {
            $topDir = $this->userDataFileService->getRootDir();
            $resolved = $this->userDataFileService->tryResolve($request->get('now_dir'));
            $nowDir = null === $resolved ? $topDir : $this->normalizePath($resolved);
            $newFilePath = $nowDir.'/'.$filename;
            if (file_exists($newFilePath)) {
                throw new IOException(trans('admin.content.file.dir_exists', ['%file_name%' => $filename]));
            }
        } catch (IOException $e) {
            $this->errors[] = ['message' => $e->getMessage()];

            return;
        }
        try {
            $fs->mkdir($newFilePath);
            $this->addSuccess('admin.common.create_complete', 'admin');
        } catch (IOException $e) {
            log_error($e->getMessage());
            $this->errors[] = ['message' => trans('admin.content.file.upload_error', [
                '%file_name%' => $filename,
            ])];
        }
    }

    #[Route(path: '/%eccube_admin_route%/content/file_delete', name: 'admin_content_file_delete', methods: ['DELETE'])]
    public function delete(Request $request): RedirectResponse
    {
        $this->isTokenValid();

        $selectFile = $request->get('select_file');
        if ($selectFile === '' || $selectFile === null || $selectFile == '/') {
            return $this->redirectToRoute('admin_content_file');
        }

        $file = $this->userDataFileService->tryResolve($this->convertStrToServer((string) $selectFile));
        if (null !== $file) {
            $fs = new Filesystem();
            if ($fs->exists($file)) {
                $fs->remove($file);
                $this->addSuccess('admin.common.delete_complete', 'admin');
            }
        }

        // 削除実行時のカレントディレクトリを表示させる
        return $this->redirectToRoute('admin_content_file', ['tree_select_file' => dirname((string) $selectFile)]);
    }

    /**
     * @throws NotFoundHttpException
     */
    #[Route(path: '/%eccube_admin_route%/content/file_download', name: 'admin_content_file_download', methods: ['GET'])]
    public function download(Request $request): BinaryFileResponse
    {
        $file = $this->userDataFileService->tryResolve($this->convertStrToServer((string) $request->get('select_file')));
        if (null !== $file && file_exists($file)) {
            if (!is_dir($file)) {
                setlocale(LC_ALL, 'ja_JP.UTF-8');
                $pathParts = pathinfo($file);

                $patterns = [
                    '/[a-zA-Z0-9!"#$%&()=~^|@`:*;+{}]/',
                    '/[- ,.<>?_[\]\/\\\\]/',
                    "/['\r\n\t\v\f]/",
                ];

                $str = preg_replace($patterns, '', $pathParts['basename']);
                if (strlen((string) $str) === 0) {
                    return (new BinaryFileResponse($file))->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);
                }

                return new BinaryFileResponse($file, Response::HTTP_OK, [
                    'Content-Type' => 'aplication/octet-stream;',
                    'Content-Disposition' => "attachment; filename*=UTF-8\'\'".rawurlencode($this->convertStrFromServer($pathParts['basename'])),
                ]);
            }
        }
        throw new NotFoundHttpException();
    }

    public function upload(Request $request): void
    {
        $form = $this->formFactory->createBuilder(FormType::class)
            ->add('file', FileType::class, [
                'multiple' => true,
                'constraints' => [
                    new Assert\NotBlank(message: 'admin.common.file_select_empty'),
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
        $nowDir = $this->userDataFileService->tryResolve($request->get('now_dir'));

        if (null === $nowDir) {
            $this->errors[] = ['message' => 'file.text.error.invalid_upload_folder'];

            return;
        }

        $uploadCount = count($data['file']);
        $successCount = 0;

        /** @var UploadedFile $file */
        foreach ($data['file'] as $file) {
            $filename = $this->convertStrToServer($file->getClientOriginalName());
            try {
                // フォルダの存在チェック
                if (is_dir(rtrim($nowDir, '/\\').\DIRECTORY_SEPARATOR.$filename)) {
                    throw new UnsupportedMediaTypeHttpException(trans('admin.content.file.same_name_folder_exists'));
                }
                // ファイル名・拡張子の検証は CLI (eccube:user-data:put) と同じものを通す
                try {
                    $this->userDataFileService->assertUploadableFileName($filename);
                } catch (ContentValidationException $e) {
                    throw new UnsupportedMediaTypeHttpException(implode(' ', $e->getErrors()));
                }
            } catch (UnsupportedMediaTypeHttpException $e) {
                if (!in_array($e->getMessage(), array_column($this->errors, 'message'))) {
                    $this->errors[] = ['message' => $e->getMessage()];
                }
                continue;
            }
            try {
                $file->move($nowDir, $filename);
                $successCount++;
            } catch (FileException $e) {
                log_error($e->getMessage());
                $this->errors[] = ['message' => trans('admin.content.file.upload_error', [
                    '%file_name%' => $filename,
                ])];
            }
        }
        if ($successCount > 0) {
            $this->addSuccess(trans('admin.content.file.upload_complete', [
                '%success%' => $successCount,
                '%count%' => $uploadCount,
            ]), 'admin');
        }
    }

    /**
     * @param array<int, array<string, mixed>> $tree
     *
     * @return array<int, array<int, mixed>>
     */
    private function getTreeToArray(array $tree): array
    {
        $arrTree = [];
        foreach ($tree as $key => $val) {
            $path = $this->userDataFileService->toRelative($val['path']);
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

    /**
     * @param array<int, array<string, mixed>> $tree
     *
     * @return array<int<0, max>,mixed>
     */
    private function getPathsToArray(array $tree): array
    {
        $paths = [];
        foreach ($tree as $val) {
            $paths[] = $this->userDataFileService->toRelative($val['path']);
        }

        return $paths;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getTree(string $topDir, Request $request): array
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
            $openDirs = explode('|', (string) $request->get('tree_status'));
        }

        foreach ($finder as $dirs) {
            $path = $this->normalizePath($dirs->getRealPath());
            $type = (iterator_count(Finder::create()->in($path)->directories())) ? '_parent' : '_child';
            $depth = count(explode('/', $path)) - $defaultDepth;
            $tree[] = [
                'path' => $path,
                'type' => $type,
                'depth' => $depth,
                'open' => in_array($path, $openDirs),
            ];
        }

        return $tree;
    }

    /**
     * @return array<mixed>
     */
    private function getFileList(string $nowDir): array
    {
        // user_data の外 (シンボリックリンクの先など) は一覧に含めない
        $filter = fn (\SplFileInfo $file): bool => $this->userDataFileService->contains((string) $file->getRealPath());

        $finder = Finder::create()
            ->filter($filter)
            ->in($nowDir)
            ->ignoreDotFiles(false)
            ->sortByName()
            ->depth(0);
        $dirFinder = $finder->directories();
        try {
            $dirs = $dirFinder->getIterator();
        } catch (\Exception) {
            $dirs = [];
        }

        $fileFinder = $finder->files();
        try {
            $files = $fileFinder->getIterator();
        } catch (\Exception) {
            $files = [];
        }

        $arrFileList = [];
        foreach ($dirs as $dir) {
            $dirPath = $this->normalizePath($dir->getRealPath());
            $childDir = Finder::create()
                ->in($dirPath)
                ->ignoreDotFiles(false)
                ->directories()
                ->depth(0);
            $childFile = Finder::create()
                ->in($dirPath)
                ->ignoreDotFiles(false)
                ->files()
                ->depth(0);
            $countNumber = $childDir->count() + $childFile->count();
            $arrFileList[] = [
                'file_name' => $this->convertStrFromServer($dir->getFilename()),
                'file_path' => $this->convertStrFromServer($this->userDataFileService->toRelative($dirPath)),
                'file_size' => FilesystemUtil::sizeToHumanReadable($dir->getSize()),
                'file_time' => $dir->getmTime(),
                'is_dir' => true,
                'is_empty' => $countNumber == 0,
            ];
        }
        foreach ($files as $file) {
            $arrFileList[] = [
                'file_name' => $this->convertStrFromServer($file->getFilename()),
                'file_path' => $this->convertStrFromServer($this->userDataFileService->toRelative($this->normalizePath($file->getRealPath()))),
                'file_size' => FilesystemUtil::sizeToHumanReadable($file->getSize()),
                'file_time' => $file->getmTime(),
                'is_dir' => false,
                'is_empty' => false,
                'extension' => $file->getExtension(),
            ];
        }

        return $arrFileList;
    }

    /**
     * 表示用にパスの区切り文字を / へ揃える.
     *
     * user_data の内外を判定するのは UserDataFileService::contains() で, ここでは行わない.
     */
    protected function normalizePath(string $path): string
    {
        return $this->userDataFileService->normalize((string) realpath($path));
    }

    private function convertStrFromServer(string $target): string
    {
        if ($this->encode == self::SJIS) {
            return mb_convert_encoding($target, self::UTF, self::SJIS);
        }

        return $target;
    }

    private function convertStrToServer(string $target): string
    {
        if ($this->encode == self::SJIS) {
            return mb_convert_encoding($target, self::SJIS, self::UTF);
        }

        return $target;
    }
}
