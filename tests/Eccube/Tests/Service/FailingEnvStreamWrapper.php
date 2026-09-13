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

namespace Eccube\Tests\Service;

/**
 * .env の書き込みが途中で失敗する状況を再現するストリームラッパー.
 *
 * ディスクフルやクォータ超過は通常のファイルでは再現できないため, 失敗を注入できる
 * ストリームで代替する. EnvFileService はパスを $projectDir から組み立てるだけなので,
 * projectDir にこのラッパーのスキームを渡せば set() をそのまま通せる.
 *
 * PHP は stream_write が要求より少ないバイト数を返すと残りを書こうとして再度呼び出す
 * (実測: 10 バイトの fwrite で 10, 7, 4, 1 と 4 回呼ばれた). 途中までしか書けない状況は
 * 「予算を使い切ったら 0 を返す」ことで再現する. 0 を返すと PHP は再試行を打ち切り,
 * fwrite は false ではなく書き込めたバイト数を返す.
 *
 * 書き込みの予算は rewind ごとに次の要素へ進む ($writeBudgets)。EnvFileService は
 * 書き込みの直前に必ず rewind するため, 「本体の書き込み」と「復元の書き込み」へ
 * 別々の予算を与えられる。
 */
final class FailingEnvStreamWrapper
{
    public const SCHEME = 'eccube-failing-env';

    /**
     * stream_wrapper_register がコンテキストを代入するため public にする.
     *
     * @var resource|null
     */
    public $context;

    private static string $contents = '';

    /**
     * @var list<int> rewind ごとの書き込み可能バイト数. 尽きたら最後の要素を使い続ける
     */
    private static array $writeBudgets = [];

    private static bool $truncateFails = false;

    private static bool $flushFails = false;

    private int $position = 0;

    private int $budgetIndex = 0;

    private int $remaining = 0;

    private bool $wroteSinceSeek = false;

    /**
     * ラッパーを登録し, EnvFileService へ渡す projectDir を返す.
     *
     * @param list<int> $writeBudgets 書き込みごとの上限バイト数. 空なら無制限
     */
    public static function register(string $contents, array $writeBudgets = [], bool $truncateFails = false, bool $flushFails = false): string
    {
        self::unregister();

        self::$contents = $contents;
        self::$writeBudgets = $writeBudgets;
        self::$truncateFails = $truncateFails;
        self::$flushFails = $flushFails;

        stream_wrapper_register(self::SCHEME, self::class);

        return self::SCHEME.'://project';
    }

    public static function unregister(): void
    {
        if (in_array(self::SCHEME, stream_get_wrappers(), true)) {
            stream_wrapper_unregister(self::SCHEME);
        }

        self::$contents = '';
        self::$writeBudgets = [];
        self::$truncateFails = false;
        self::$flushFails = false;
    }

    /**
     * 現在のファイル内容.
     */
    public static function contents(): string
    {
        return self::$contents;
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $this->position = 0;
        $this->budgetIndex = 0;
        $this->remaining = self::budgetAt(0);
        $this->wroteSinceSeek = false;

        return true;
    }

    public function stream_read(int $count): string
    {
        $read = substr(self::$contents, $this->position, $count);
        $this->position += strlen($read);

        return $read;
    }

    public function stream_write(string $data): int
    {
        $this->wroteSinceSeek = true;

        $length = min(strlen($data), $this->remaining);
        if ($length <= 0) {
            // 0 を返すと PHP は再試行を打ち切る. これで部分書き込みになる
            return 0;
        }

        $this->remaining -= $length;

        // 実ファイルと同じく, 位置を越える書き込みはファイルを伸ばす
        self::$contents = str_pad(self::$contents, $this->position, "\0");
        self::$contents = substr_replace(self::$contents, substr($data, 0, $length), $this->position, $length);
        $this->position += $length;

        return $length;
    }

    public function stream_seek(int $offset, int $whence = SEEK_SET): bool
    {
        // 書き込みの後に先頭へ戻ったら, 次の書き込みは別の試行として扱う
        if ($this->wroteSinceSeek) {
            $this->remaining = self::budgetAt(++$this->budgetIndex);
            $this->wroteSinceSeek = false;
        }

        $this->position = match ($whence) {
            SEEK_CUR => $this->position + $offset,
            SEEK_END => strlen(self::$contents) + $offset,
            default => $offset,
        };

        return $this->position >= 0;
    }

    public function stream_tell(): int
    {
        return $this->position;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(self::$contents);
    }

    public function stream_truncate(int $newSize): bool
    {
        if (self::$truncateFails) {
            return false;
        }

        self::$contents = substr(str_pad(self::$contents, $newSize, "\0"), 0, $newSize);

        return true;
    }

    public function stream_flush(): bool
    {
        return !self::$flushFails;
    }

    public function stream_lock(int $operation): bool
    {
        return true;
    }

    /**
     * @return array<string, int>
     */
    public function stream_stat(): array
    {
        return self::stat();
    }

    /**
     * is_file() のために必要.
     *
     * stream_close() は定義しない. 解放する資源が無く, 未定義でも fclose() は
     * 警告を出さずに true を返す (実測).
     *
     * @return array<string, int>|false 登録した .env 以外は存在しないものとして false
     */
    public function url_stat(string $path, int $flags): array|false
    {
        if (!str_ends_with($path, '/.env')) {
            return false;
        }

        return self::stat();
    }

    /**
     * @return array<string, int>
     */
    private static function stat(): array
    {
        // 0100000 (S_IFREG) を立てないと is_file() が false になる
        return ['dev' => 0, 'ino' => 0, 'mode' => 0100644, 'nlink' => 1, 'uid' => 0, 'gid' => 0,
            'rdev' => 0, 'size' => strlen(self::$contents), 'atime' => 0, 'mtime' => 0, 'ctime' => 0,
            'blksize' => -1, 'blocks' => -1];
    }

    private static function budgetAt(int $index): int
    {
        if (self::$writeBudgets === []) {
            return PHP_INT_MAX;
        }

        return self::$writeBudgets[min($index, count(self::$writeBudgets) - 1)];
    }
}
