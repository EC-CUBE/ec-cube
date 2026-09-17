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

namespace Eccube\Service\AgentCommerce\Security;

/**
 * キーストアへ保管する鍵の用途 (purpose) の定義.
 *
 * 生成方法をここへ集約し, CLI (eccube:keystore:generate) と実行時の自動生成
 * (UcpMessageSigner / AcpMessageSigner) の双方が本インターフェイス経由で生成する.
 * 生成方法を 1 箇所に保つことで, どちらから作っても同じ鍵が得られる.
 *
 * app/Customize やプラグインで実装を追加すると, Kernel の registerForAutoconfiguration により
 * 自動でタグ付けされ, KeyPurposeRegistry と eccube:keystore:* の対象になる.
 */
interface KeyPurposeInterface
{
    /**
     * 用途識別子. KeyStoreInterface のキーとなる.
     *
     * FilesystemKeyStore がパスへ連結するため, 小文字英数字と "_" "-" のみ使用できる.
     */
    public function getPurpose(): string;

    /**
     * 一覧表示に使う説明. 何に使う鍵かが分かる 1 行.
     */
    public function getLabel(): string;

    /**
     * 新しい鍵を生成し, キーストアへ保管する文字列を返す.
     */
    public function generate(): string;

    /**
     * 保管されている鍵から, 表示してよい公開情報だけを取り出す.
     *
     * 秘密鍵パラメータ (EC の d) や共有シークレットの値は絶対に含めない.
     * 戻り値は eccube:keystore:show の出力源になる.
     *
     * @param string $material キーストアに保管されている文字列
     *
     * @return array<string, mixed> 公開してよい情報のみ
     */
    public function describe(string $material): array;
}
