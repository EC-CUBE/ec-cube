<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Page\Upgrade\Helper;

use Eccube\Application;
use Eccube\Framework\Util\GcUtils;

/**
 * オーナーズストア管理用ログクラス
 *
 */
class LogHelper
{
    /**
     * 開始メッセージを出力
     *
     * @param string $mode
     */
    public function start($mode)
    {
        $message = "##### $mode start #####";
        $this->log($message);
    }
    /**
     * 終了メッセージを出力
     *
     */
    public function end()
    {
        $message = '##### end #####';
        $this->log($message);
    }
    /**
     * メッセージを出力
     *
     * @param string $message
     */
    public function log($message)
    {
        GcUtils::gfPrintLog($message, OSTORE_LOG_REALFILE);
    }
    /**
     * エラーメッセージを出力
     *
     * @param string $code
     * @param mixed  $val
     */
    public function error($code, $val = null)
    {
        $format = '* error! code:%s / debug:%s';
        $message = sprintf($format, $code, serialize($val));
        $this->log($message);
        $this->end();
    }
}
