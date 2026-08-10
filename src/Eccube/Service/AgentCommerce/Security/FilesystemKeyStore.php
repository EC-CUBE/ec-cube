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
 * 鍵ファイルはパーミッション 0600、格納ディレクトリは 0700 で作成する。
 */
class FilesystemKeyStore implements KeyStoreInterface
{
    /**
     * @param string                $projectDir       プロジェクトルート (%kernel.project_dir%)
     * @param array<string, string> $envPathOverrides purpose => 絶対パスの上書きマップ
     */
    public function __construct(
        private readonly string $projectDir,
        private readonly array $envPathOverrides = [],
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

        $contents = file_get_contents($path);

        return $contents === false ? null : $contents;
    }

    /**
     * {@inheritdoc}
     */
    public function write(string $purpose, string $pem): void
    {
        $path = $this->resolvePath($purpose);
        $dir = \dirname($path);

        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('鍵格納ディレクトリ "%s" を作成できません.', $dir));
        }

        // file_put_contents は umask 既定 (通常 0644) でファイルを作成してから書き込むため、
        // chmod(0600) までの間に秘密鍵が group/other から読める瞬間が生じる。
        // 作成時点から 0600 になるよう、書き込みの間だけ umask(0077) に切り替える。
        $oldUmask = umask(0077);
        try {
            if (file_put_contents($path, $pem, LOCK_EX) === false) {
                throw new \RuntimeException(sprintf('鍵ファイル "%s" への書き込みに失敗しました.', $path));
            }
            if (!chmod($path, 0600)) {
                throw new \RuntimeException(sprintf('鍵ファイル "%s" のパーミッション設定に失敗しました.', $path));
            }
        } finally {
            umask($oldUmask);
        }
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
