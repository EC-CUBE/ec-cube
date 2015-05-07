<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Framework\SessionFactory\UseRequest;

use Eccube\Application;
use Eccube\Framework\Util\GcUtils;

/**
 * PCサイト用のセッションデータ管理クラス
 *
 */
class PcState extends AbstractState
{

    /**
     * コンストラクタ
     * セッションのデータ構造は下のようになる.
     * $_SESSION['pc']=> array(
     *     ['model']   => 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)'
     *     ['ip']      => '127.0.0.1'
     *     ['expires'] => 1204699031
     * )
     */
    public function __construct()
    {
        $this->namespace = 'pc';
        $this->lifetime = SESSION_LIFETIME;
        $this->validate = array('NameSpace', 'Model', 'Ip', 'Expire');
    }

    /**
     * セッションにUserAgentを設定する.
     *
     */
    public function updateModel()
    {
        $this->setValue('model', $_SERVER['HTTP_USER_AGENT']);
    }

    /**
     * UserAgentを検証する.
     *
     * @return boolean
     */
    public function validateModel()
    {
        $ua = $this->getModel();
        if (!empty($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] === $ua) {
            return true;
        }
        $msg = sprintf('User agent model mismatch : %s != %s(expected), sid=%s', $_SERVER['HTTP_USER_AGENT'], $ua, session_id());
        GcUtils::gfPrintLog($msg);

        return false;
    }

    /**
     * セッションデータを初期化する.
     *
     */
    public function inisializeSessionData()
    {
        $_SESSION = array();
        $this->updateModel();
        $this->updateIp();
        $this->updateExpire();
    }

}
