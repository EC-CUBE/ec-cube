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

namespace Eccube\Service\AgentCommerce\Security;

/**
 * 署名鍵をファイルシステムに保管する標準キーストア実装。
 *
 * 既定パスは "{projectDir}/app/keystore/agent-commerce/{purpose}.key"。
 * purpose ごとに環境変数等によるパス上書き ($envPathOverrides) が可能。
 *
 * パーミッションは既定でディレクトリ 0755 / ファイル 0644。Web サーバーと CLI を別ユーザーに
 * 分けた構成では、CLI が配置した鍵を Web サーバーが読める必要があるためで、所有者専用 (0700 / 0600)
 * にすると chgrp できない環境 (共有レンタルサーバー等) で鍵を読めなくなる。
 * 同一サーバーの他ユーザーからも読ませたくない場合は $strictPermissions を有効にする
 * (ECCUBE_KEYSTORE_STRICT_PERMISSIONS=1)。この場合 Web サーバーが読めるかは運用側で担保する
 * (eccube:keystore:generate が読めないことを検出してエラーにする)。
 */
class FilesystemKeyStore implements KeyStoreInterface, KeyStorePathAwareInterface
{
    private const DIR_MODE = 0755;

    private const FILE_MODE = 0644;

    private const STRICT_DIR_MODE = 0700;

    private const STRICT_FILE_MODE = 0600;

    /**
     * @param string                $projectDir        プロジェクトルート (%kernel.project_dir%)
     * @param array<string, string> $envPathOverrides  purpose => 絶対パスの上書きマップ
     * @param bool                  $strictPermissions 鍵を所有者専用 (0700 / 0600) で作成する
     */
    public function __construct(
        private readonly string $projectDir,
        private readonly array $envPathOverrides = [],
        private readonly bool $strictPermissions = false,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function read(string $purpose): ?string
    {
        $path = $this->resolvePath($purpose);

        if (!is_file($path)) {
            return null;
        }

        // 別ユーザーが所有する鍵 (Web サーバーが実行時に生成した 0600 のファイル等) は
        // 読めなくて当然のため、警告は出さずに null を返す。呼び出し側が状態として扱う。
        $contents = @file_get_contents($path);

        return $contents === false ? null : $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $purpose, string $pem): void
    {
        $path = $this->resolvePath($purpose);
        $dir = \dirname($path);
        $dirMode = $this->strictPermissions ? self::STRICT_DIR_MODE : self::DIR_MODE;
        $fileMode = $this->strictPermissions ? self::STRICT_FILE_MODE : self::FILE_MODE;

        if (!is_dir($dir)) {
            // mkdir のモードは umask で削られる。Web サーバーがここを通り抜けられるかは
            // 機能要件のため、ECCUBE_UMASK の設定に関わらず明示的に設定する。
            // 対象は今回作成した階層だけとし、既にあるディレクトリのモードは変更しない
            // (運用側で設定した権限を上書きしないため)。
            $created = [];
            for ($current = $dir; !is_dir($current) && $current !== \dirname($current); $current = \dirname($current)) {
                $created[] = $current;
            }

            // 失敗は例外で同じ内容を伝えるため、警告は抑止する (CLI の出力を汚さない)。
            if (!@mkdir($dir, $dirMode, true) && !is_dir($dir)) {
                throw new \RuntimeException(sprintf('鍵格納ディレクトリ "%s" を作成できません.', $dir));
            }

            foreach ($created as $createdDir) {
                if (!chmod($createdDir, $dirMode)) {
                    throw new \RuntimeException(sprintf('鍵格納ディレクトリ "%s" のパーミッション設定に失敗しました.', $createdDir));
                }
            }
        }

        // 厳格モードでは file_put_contents が umask 既定 (通常 0644) でファイルを作成してから
        // 書き込むため、chmod(0600) までの間に秘密鍵が group/other から読める瞬間が生じる。
        // 作成時点から 0600 になるよう、書き込みの間だけ umask(0077) に切り替える。
        // 既定モードは作成時より広げる方向のため、この対策は不要。
        $previousUmask = $this->strictPermissions ? umask(0077) : null;
        try {
            if (@file_put_contents($path, $pem, LOCK_EX) === false) {
                throw new \RuntimeException(sprintf('鍵ファイル "%s" への書き込みに失敗しました.', $path));
            }
            if (!chmod($path, $fileMode)) {
                throw new \RuntimeException(sprintf('鍵ファイル "%s" のパーミッション設定に失敗しました.', $path));
            }
        } finally {
            if ($previousUmask !== null) {
                umask($previousUmask);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(string $purpose): string
    {
        return $this->resolvePath($purpose);
    }

    /**
     * purpose から鍵ファイルの絶対パスを解決する。
     *
     * $envPathOverrides[$purpose] が非空文字ならそのパスを優先し、
     * それ以外は既定パスを使用する。
     *
     * $purpose は既定パスへ直接連結されるため、パストラバーサル ("../" 等) を防ぐべく
     * 許可文字 ([a-z0-9_-]) のみに制限する。
     *
     * @throws \InvalidArgumentException $purpose に許可外の文字が含まれる場合
     */
    private function resolvePath(string $purpose): string
    {
        if (!preg_match('/\A[a-z0-9_-]+\z/', $purpose)) {
            throw new \InvalidArgumentException(sprintf('Invalid key purpose "%s". Only lowercase alphanumerics, "_" and "-" are allowed.', $purpose));
        }

        $override = $this->envPathOverrides[$purpose] ?? '';

        if ($override !== '') {
            return $override;
        }

        return $this->projectDir.'/app/keystore/agent-commerce/'.$purpose.'.key';
    }
}
