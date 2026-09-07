<?php

declare(strict_types=1);

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

namespace Eccube\Service\Content;

use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * html/user_data 配下のファイルを扱う.
 *
 * 管理画面のファイル管理 (FileController) と CLI (eccube:user-data:* / eccube:asset:*) の
 * 双方から使用する. html/user_data はドキュメントルート (html/) の配下にあるため,
 * パスの境界検査とファイル名・拡張子の検証は UX ではなく任意のファイルを公開させないための
 * セキュリティ境界である. CLI からの操作にも管理画面と同じ検証を適用する.
 */
class UserDataFileService
{
    /**
     * 配置を許可するファイル名 (英数字, 半角スペース, _-.() のみ).
     *
     * 管理画面のフォーム (FileController) からも同じ定義を参照する.
     */
    public const FILE_NAME_PATTERN = '/\A[a-zA-Z0-9_\-\.\(\) ]+\Z/';

    /**
     * ディレクトリ名に使用できない文字 (match したら不正).
     */
    public const DIRECTORY_NAME_DENY_PATTERN = '/[^[:alnum:]_.\-]/';

    /**
     * . で始まる名前 (match したら不正).
     */
    public const DOT_PREFIX_PATTERN = '/^\.(.*)$/';

    /**
     * @param list<string> $uploadableExtensions 配置を許可する拡張子 (eccube_file_uploadable_extensions)
     */
    public function __construct(
        private readonly string $userDataDir,
        private readonly array $uploadableExtensions,
        private readonly Filesystem $filesystem,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * user_data のルートディレクトリ (末尾に区切り文字を付けない).
     */
    public function getRootDir(): string
    {
        return rtrim($this->normalize($this->userDataDir), '/');
    }

    /**
     * 相対パスを user_data 配下の絶対パスへ解決する.
     *
     * 存在しないパスも解決できる (新規ファイルの配置に使うため).
     *
     * @throws ContentValidationException user_data の外を指す場合
     */
    public function resolve(?string $relative): string
    {
        $resolved = $this->tryResolve($relative);
        if (null === $resolved) {
            throw new ContentValidationException([sprintf('%s は html/user_data の配下ではありません.', (string) $relative)]);
        }

        return $resolved;
    }

    /**
     * resolve() と同じだが, user_data の外を指す場合は例外ではなく null を返す.
     *
     * 管理画面のファイル管理は不正な位置を指定されたときルートへフォールバックするため,
     * 例外ではなく null で受ける.
     */
    public function tryResolve(?string $relative): ?string
    {
        $relative = (string) $relative;

        // 相対パスに .. やヌルバイトを含むものは解決前に拒否する.
        // 現行の FileController::checkDir() と同じく, セグメント単位ではなく文字列として判定する.
        if (str_contains($relative, '..') || str_contains($relative, "\0")) {
            return null;
        }

        $root = $this->getRootDir();
        $relative = trim($this->normalize($relative), '/');
        $target = '' === $relative ? $root : $root.'/'.$relative;

        return $this->contains($target) ? $target : null;
    }

    /**
     * 絶対パスが user_data の配下かどうか.
     *
     * 実体 (realpath) で判定するため, 配下のシンボリックリンクが外部を指す場合も false になる.
     */
    public function contains(string $path): bool
    {
        $root = $this->realpathAllowingMissing($this->userDataDir);
        $real = $this->realpathAllowingMissing($path);

        if (null === $root || null === $real) {
            return false;
        }

        // 区切り文字を含めて比較する. 含めないと user_data_evil のような
        // 兄弟ディレクトリが配下と判定される.
        return $real === $root || str_starts_with($real, $root.'/');
    }

    /**
     * 絶対パスを user_data からの相対パス (先頭に / を付けた表記) へ変換する.
     *
     * 管理画面のファイル管理が画面へ渡すパスの表記に合わせる. ルート自身は '/' になる.
     */
    public function toRelative(string $path): string
    {
        $real = $this->realpathAllowingMissing($path);
        if (null === $real) {
            return '/';
        }

        $root = $this->realpathAllowingMissing($this->userDataDir);
        $jailPath = null === $root ? $real : str_replace($root, '', $real);

        return $jailPath ?: '/';
    }

    /**
     * 区切り文字を / に統一する.
     *
     * Windows の realpath() は \ を返すため, 比較の前に揃える.
     */
    public function normalize(string $path): string
    {
        return str_replace('\\', '/', $path);
    }

    /**
     * user_data 配下のファイル・ディレクトリを一覧する.
     *
     * @return list<array{name: string, path: string, is_dir: bool, size: int, mtime: int}>
     *
     * @throws ContentValidationException 指定した位置が user_data の外, またはディレクトリでない場合
     */
    public function list(?string $relativeDir = null, bool $recursive = false): array
    {
        $dir = $this->resolve($relativeDir);
        if (!is_dir($dir)) {
            throw new ContentValidationException([sprintf('ディレクトリが見つかりません: %s', $this->toRelative($dir))]);
        }

        $finder = Finder::create()
            ->in($dir)
            ->ignoreDotFiles(false)
            ->sortByName();
        if (!$recursive) {
            $finder->depth(0);
        }

        $entries = [];
        foreach ($finder as $file) {
            $entries[] = [
                'name' => $file->getFilename(),
                'path' => $this->toRelative($file->getPathname()),
                'is_dir' => $file->isDir(),
                'size' => $file->isDir() ? 0 : (int) $file->getSize(),
                'mtime' => (int) $file->getMTime(),
            ];
        }

        return $entries;
    }

    /**
     * ファイルの内容を取得する.
     *
     * @throws ContentValidationException ファイルが存在しない場合
     */
    public function read(?string $relative): string
    {
        $path = $this->resolve($relative);
        if (!is_file($path)) {
            throw new ContentValidationException([sprintf('ファイルが見つかりません: %s', (string) $relative)]);
        }

        $contents = file_get_contents($path);
        if (false === $contents) {
            throw new ContentValidationException([sprintf('ファイルを読み込めません: %s', (string) $relative)]);
        }

        return $contents;
    }

    /**
     * ファイルを配置する (upsert).
     *
     * 中間ディレクトリは作成する. ファイル名・拡張子は管理画面のアップロードと同じ検証を通す.
     *
     * @throws ContentValidationException パス・ファイル名・拡張子が不正な場合
     * @throws ContentWriteException      書き込めない場合
     */
    public function write(?string $relative, string $contents, bool $dryRun = false): ContentResult
    {
        $path = $this->resolve($relative);
        if ($path === $this->getRootDir()) {
            throw new ContentValidationException(['配置先のファイル名を指定してください.']);
        }
        if (is_dir($path)) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.same_name_folder_exists')]);
        }

        $jailPath = $this->toRelative($path);
        $segments = explode('/', trim($this->normalize((string) $relative), '/'));
        $fileName = array_pop($segments);
        foreach ($segments as $segment) {
            $this->assertDirectoryName($segment);
        }
        $this->assertUploadableFileName($fileName);

        $exists = is_file($path);
        $before = $exists ? (string) file_get_contents($path) : '';
        if ($exists && $before === $contents) {
            return new ContentResult(ContentStatus::Unchanged, null, $jailPath);
        }

        // 差分は表示用のため, 画像等 UTF-8 として解釈できない内容では作らない
        // (端末へバイナリをそのまま書き出さないようにする).
        $fileChanges = mb_check_encoding($before, 'UTF-8') && mb_check_encoding($contents, 'UTF-8')
            ? [$path => [$before, $contents]]
            : [];
        $status = $exists ? ContentStatus::Updated : ContentStatus::Created;

        if ($dryRun) {
            return new ContentResult($status, null, $jailPath, [], [], [], $fileChanges);
        }

        try {
            $this->filesystem->dumpFile($path, $contents);
        } catch (IOException $e) {
            throw ContentWriteException::forWrite($path, $e);
        }

        return new ContentResult($status, null, $jailPath, [$path], [], [], $fileChanges);
    }

    /**
     * ファイル・ディレクトリを削除する.
     *
     * @throws ContentValidationException パスが不正, または空でないディレクトリを $recursive なしで指定した場合
     * @throws ContentWriteException      削除できない場合
     */
    public function remove(?string $relative, bool $recursive = false, bool $dryRun = false): ContentResult
    {
        $path = $this->resolve($relative);
        if ($path === $this->getRootDir()) {
            // ルートを消すとファイル管理そのものが動かなくなる
            throw new ContentValidationException(['html/user_data 自体は削除できません.']);
        }
        if (!file_exists($path)) {
            throw new ContentValidationException([sprintf('ファイルが見つかりません: %s', (string) $relative)]);
        }
        if (is_dir($path) && !$recursive && !$this->isEmptyDir($path)) {
            throw new ContentValidationException([sprintf('%s は空ではありません. 配下ごと削除する場合は --recursive を指定してください.', $this->toRelative($path))]);
        }

        $jailPath = $this->toRelative($path);

        if ($dryRun) {
            return new ContentResult(ContentStatus::Removed, null, $jailPath, [], [$path]);
        }

        try {
            $this->filesystem->remove($path);
        } catch (IOException $e) {
            throw ContentWriteException::forRemove($path, $e);
        }

        return new ContentResult(ContentStatus::Removed, null, $jailPath, [], [$path]);
    }

    /**
     * 配置を許可するファイル名か検証する.
     *
     * 管理画面のアップロード (FileController::upload) と同じ規則.
     *
     * @throws ContentValidationException
     */
    public function assertUploadableFileName(string $fileName): void
    {
        // 英数字, 半角スペース, _-.() のみ許可
        if (!preg_match(self::FILE_NAME_PATTERN, $fileName)) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.folder_name_symbol_error')]);
        }
        // dotファイルは配置不可
        if (str_starts_with($fileName, '.')) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.dotfile_error')]);
        }
        // 許可した拡張子以外は配置不可
        if (!$this->isUploadableExtension($fileName)) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.extension_error')]);
        }
    }

    /**
     * 拡張子が許可リストに含まれるか.
     */
    public function isUploadableExtension(string $fileName): bool
    {
        return in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), $this->uploadableExtensions, true);
    }

    /**
     * 作成を許可するディレクトリ名か検証する.
     *
     * 管理画面のフォルダ作成 (FileController::create) と同じ規則.
     *
     * @throws ContentValidationException
     */
    public function assertDirectoryName(string $name): void
    {
        if ('' === $name) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.folder_name_symbol_error')]);
        }
        if (preg_match(self::DIRECTORY_NAME_DENY_PATTERN, $name)) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.folder_name_symbol_error')]);
        }
        if (preg_match(self::DOT_PREFIX_PATTERN, $name)) {
            throw new ContentValidationException([$this->translator->trans('admin.content.file.folder_name_period_error')]);
        }
    }

    private function isEmptyDir(string $dir): bool
    {
        return !Finder::create()->in($dir)->ignoreDotFiles(false)->depth(0)->hasResults();
    }

    /**
     * 存在しないパスも解決できる realpath().
     *
     * 存在する最深の祖先だけを realpath() し, 残りのセグメントを連結する.
     * 壊れたシンボリックリンクは解決できないため null を返して拒否する
     * (解決せずに連結すると, 存在しない外部を指すリンク越しに書き込めてしまう).
     */
    private function realpathAllowingMissing(string $path): ?string
    {
        $path = rtrim($this->normalize($path), '/');
        if ('' === $path) {
            return null;
        }

        $suffix = [];
        $current = $path;
        while (!file_exists($current)) {
            if (is_link($current)) {
                // リンク先が存在しない (壊れたリンク). 実体を確認できないため拒否する
                return null;
            }

            $parent = \dirname($current);
            if ($parent === $current) {
                return null;
            }

            array_unshift($suffix, basename($current));
            $current = $parent;
        }

        $real = realpath($current);
        if (false === $real) {
            return null;
        }

        $real = rtrim($this->normalize($real), '/');

        return [] === $suffix ? $real : $real.'/'.implode('/', $suffix);
    }
}
