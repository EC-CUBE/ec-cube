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

namespace Eccube\Service\AgentCommerce\Exception;

/**
 * UCP の RFC 9421 署名検証に失敗したことを表す例外.
 *
 * プロトコル系エラー (HTTP 401 等) に変換される。署名欠落・不正・鍵不一致・
 * Content-Digest 不一致・プロファイル取得失敗・許可外ドメインを含む。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md
 */
class UcpSignatureException extends \RuntimeException
{
}
