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
            throw new \RuntimeException(sprintf('Failed to create key directory: %s', $dir));
        }

        if (file_put_contents($path, $pem, LOCK_EX) === false) {
            throw new \RuntimeException(sprintf('Failed to write key file: %s', $path));
        }

        if (!chmod($path, 0600)) {
            throw new \RuntimeException(sprintf('Failed to set key file permission: %s', $path));
        }
    }

    /**
     * purpose から鍵ファイルの絶対パスを解決する。
     *
     * $envPathOverrides[$purpose] が非空文字ならそのパスを優先し、
     * それ以外は既定パスを使用する。
     */
    private function resolvePath(string $purpose): string
    {
        $override = $this->envPathOverrides[$purpose] ?? '';

        if ($override !== '') {
            return $override;
        }

        return $this->projectDir.'/app/keystore/agent-commerce/'.$purpose.'.key';
    }
}
