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
 * 書き込み先の権限レーン.
 *
 * Web サーバーへ与える書き込み権限を最小化するため, EC-CUBE が書き込む先を
 * 「リクエスト処理中に書き込みが発生するもの」と「CLI (SSH ログインユーザー) へ移せるもの」に分類する.
 */
enum WriteLane: string
{
    /** Web サーバー所有. リクエスト処理中に書き込みが発生するため CLI へ移せない. */
    case WEB = 'web';

    /** SSH ユーザー所有. Web サーバーは読み取りのみ. */
    case SSH = 'ssh';

    public function label(): string
    {
        return match ($this) {
            self::WEB => 'W (Web サーバー所有)',
            self::SSH => 'S (SSH ユーザー所有)',
        };
    }
}
