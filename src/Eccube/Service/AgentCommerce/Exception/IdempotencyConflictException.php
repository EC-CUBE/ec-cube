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
 * Idempotency-Key の競合 (HTTP 409 相当).
 *
 * - 同一キーが**異なるリクエスト内容**で再利用された場合
 * - 同一キーの**処理がまだ進行中** (並行リクエスト) の場合
 *
 * いずれもプロトコル層で HTTP 409 Conflict へ変換する。
 */
class IdempotencyConflictException extends \RuntimeException
{
}
