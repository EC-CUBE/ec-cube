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

namespace Eccube\Exception;

/**
 * DB レコードと対になるテンプレートファイルの読み書きに失敗した.
 *
 * 権限を分離した構成では app/template を CLI ユーザーが所有し Web サーバーからは書き込めないため,
 * 実行ユーザーを誤るとファイル操作だけが失敗する. 生の IOException を伝播させると呼び出し側で
 * スタックトレースがそのまま表示され, 対処方法 (実行ユーザーの変更) が分からないため, 専用の
 * 例外へ変換して案内できるようにする.
 */
class ContentWriteException extends \RuntimeException
{
    public function __construct(private readonly string $path, string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    /**
     * 操作に失敗したファイルのパス.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public static function forWrite(string $path, \Throwable $previous): self
    {
        return new self(
            $path,
            sprintf('%s へ書き込めません. 書き込み権限のあるユーザーで実行してください.', $path),
            $previous
        );
    }

    public static function forRemove(string $path, \Throwable $previous): self
    {
        return new self(
            $path,
            sprintf('%s を削除できません. 書き込み権限のあるユーザーで実行してください.', $path),
            $previous
        );
    }
}
