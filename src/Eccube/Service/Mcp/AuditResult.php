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

namespace Eccube\Service\Mcp;

/**
 * MCP 監査ログの `result_status` フィールドに記録する判別子。
 *
 * 設計 §4.2 / §3.3 の警告分類と一致させる。 値はクライアントへの応答ではなく
 * 監査ログ内部の値で、エラーレスポンス (`unauthorized` / `insufficient_scope` /
 * `rate_limited`) とは目的が異なる。
 */
enum AuditResult: string
{
    case Success = 'success';
    case ValidationError = 'validation_error';
    case OriginInvalid = 'origin_invalid';
    case TokenInvalid = 'token_invalid';
    case ScopeDenied = 'scope_denied';
    case RateLimited = 'rate_limited';
    case InternalError = 'internal_error';
}
