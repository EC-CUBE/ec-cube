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
 * 鍵をファイルとして保管するキーストアが実装する追加インターフェイス.
 *
 * 保管先のパスが分かる場合にのみ, eccube:keystore:* がパスの表示と
 * Web サーバーからの読み取り可否の判定を行う. DB や Vault へ保管する実装は
 * これを実装しないため, 判定は行われない (KeyStoreInterface は read/write に保つ).
 */
interface KeyStorePathAwareInterface
{
    /**
     * purpose に対応する鍵ファイルの絶対パスを返す.
     *
     * 鍵が未生成でもパスは返す (これから書き込む先を表示するため).
     *
     * @throws \InvalidArgumentException $purpose に許可外の文字が含まれる場合
     */
    public function getPath(string $purpose): string;
}
