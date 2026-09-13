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
 * 鍵を Web サーバーから読み取れるか.
 *
 * 署名は実行時 (リクエスト処理中) に行われるため, 鍵を Web サーバーが読めないと
 * discovery や署名が失敗する. 判定は所有者 uid / グループ gid / パーミッションビットからの
 * 推定で, 補助グループ・ACL・SELinux は考慮しない (PermissionDiagnostic と同じ根拠).
 */
enum WebReadability: string
{
    case Readable = 'readable';
    case Unreadable = 'unreadable';

    /**
     * 判定できない. 鍵が未生成, Web サーバーの実行ユーザーを特定できない,
     * キーストアがファイルではない (DB / Vault 等) のいずれか.
     */
    case Unknown = 'unknown';
}
