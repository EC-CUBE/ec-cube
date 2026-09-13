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

namespace Eccube\Service\Permission;

/**
 * 診断結果の重大度.
 */
enum FindingSeverity: string
{
    /** 期待どおり. */
    case OK = 'ok';

    /** 判定できない, または運用上の注意が必要. */
    case WARN = 'warn';

    /** 期待と異なるため対応が必要. */
    case NG = 'ng';

    public function label(): string
    {
        return match ($this) {
            self::OK => 'OK',
            self::WARN => 'WARN',
            self::NG => 'NG',
        };
    }
}
