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

namespace Eccube\Service\Security;

/**
 * 暗号鍵・シークレット素材 (PEM 等) を永続化するキーストアの抽象。
 *
 * EC-CUBE の複数の core 機能 (エージェントコマース・MCP サーバ等) が共通で利用する
 * 横断基盤。鍵は機能ごとに app/keystore/<feature>/ 配下へ分離して保管する。
 * 実装は app/Customize で差し替え可能 (例: DB 保管・Vault 連携)。
 * 標準実装はファイルシステム (FilesystemKeyStore)。
 */
interface KeyStoreInterface
{
    /**
     * 指定 purpose の鍵 (PEM 文字列) を読み出す。存在しなければ null。
     *
     * @param string $purpose 鍵の用途識別子 (例: 'ucp_signing')
     *
     * @return string|null PEM 文字列、または未保存の場合 null
     */
    public function read(string $purpose): ?string;

    /**
     * 指定 purpose の鍵 (PEM 文字列) を書き込む。
     *
     * @param string $purpose 鍵の用途識別子 (例: 'ucp_signing')
     * @param string $pem     保存する PEM 文字列
     */
    public function write(string $purpose, string $pem): void;
}
