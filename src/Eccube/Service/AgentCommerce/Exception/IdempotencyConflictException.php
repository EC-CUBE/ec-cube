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
 * 同一 Idempotency-Key が異なるリクエスト内容で再利用されたことを表す例外.
 *
 * UCP/ACP では同一キーでの再送はキャッシュ結果のリプレイ (副作用の再実行なし) とし、
 * **パラメータが異なる**再利用は HTTP 409 Conflict に変換する。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP checkout-rest.md (Idempotency-Key)
 */
class IdempotencyConflictException extends \RuntimeException
{
}
