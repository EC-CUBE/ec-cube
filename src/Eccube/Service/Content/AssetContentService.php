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

/**
 * 管理画面の CSS 管理 / JS 管理が編集するファイルを扱う.
 *
 * 管理画面 (CssController / JsController) と CLI (eccube:asset:*) の双方から使用する.
 * パスの解決と書き込みは UserDataFileService に委譲する.
 */
class AssetContentService
{
    /**
     * 種別 => html/user_data からの相対パス.
     *
     * @var array<string, string>
     */
    public const TYPES = [
        'css' => 'assets/css/customize.css',
        'js' => 'assets/js/customize.js',
    ];

    public function __construct(private readonly UserDataFileService $userDataFileService)
    {
    }

    /**
     * @return list<string>
     */
    public function getTypes(): array
    {
        return array_keys(self::TYPES);
    }

    public function isValidType(string $type): bool
    {
        return isset(self::TYPES[$type]);
    }

    public function getFilePath(string $type): string
    {
        $this->assertType($type);

        return $this->userDataFileService->resolve(self::TYPES[$type]);
    }

    /**
     * 現在の内容を取得する.
     *
     * 判定は存在の有無だけで行う. 書き込み権限の有無を条件に加えると, 権限を分離した構成
     * (html/user_data がレーン S) で管理画面から現在の内容を確認できなくなる.
     *
     * @throws ContentValidationException ファイルはあるが読み込めない場合
     */
    public function read(string $type): string
    {
        $path = $this->getFilePath($type);

        return file_exists($path) ? $this->userDataFileService->read(self::TYPES[$type]) : '';
    }

    /**
     * 内容を保存する (upsert).
     *
     * 同じ内容を複数回適用しても結果は変わらない.
     *
     * @throws ContentValidationException 種別が不正な場合
     * @throws ContentWriteException      書き込めない場合
     */
    public function apply(string $type, string $body, bool $dryRun = false): ContentResult
    {
        $this->assertType($type);

        $result = $this->userDataFileService->write(self::TYPES[$type], $body, $dryRun);

        // 識別子は利用者が指定した種別 (css / js) にする
        return new ContentResult(
            $result->status,
            null,
            $type,
            $result->writtenPaths,
            $result->removedPaths,
            [],
            $result->fileChanges
        );
    }

    /**
     * @throws ContentValidationException
     */
    private function assertType(string $type): void
    {
        if (!$this->isValidType($type)) {
            throw new ContentValidationException([sprintf('種別は %s のいずれかで指定してください: %s', implode(' / ', $this->getTypes()), $type)]);
        }
    }
}
