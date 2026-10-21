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

namespace Eccube\Form\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * 管理画面の URL (ECCUBE_ADMIN_ROUTE) に使えない名前を拒否する制約.
 *
 * - 認証を掛けないファイアウォール (security.yaml の dev: _profiler / _wdt / css / images / js) に一致する名前.
 *   dev ファイアウォールが admin より前にあるため, 一致すると管理画面に認証が掛からない
 * - フロントのルートに含まれる名前. admin ファイアウォールがフロントの URL を奪う
 */
#[\Attribute]
class AdminRouteDir extends Constraint
{
    public string $unsecuredMessage = 'form_error.admin_route_dir_unsecured';

    public string $frontRouteMessage = 'form_error.admin_route_dir_front_route';
}
