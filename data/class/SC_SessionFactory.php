<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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

/**
 * セッションの初期化処理を抽象化するファクトリークラス.
 *
 * このクラスはセッションの維持方法を管理するクラスです.
 * 他のセッション管理クラスとは若干異なります.
 *
 * EC-CUBE2.1.1ベータ版から、
 * 管理画面＞基本情報＞パラメーター管理で、セッションの維持方法を
 * ・Cookieを使用する場合
 * ・リクエストパラメーターを使用する場合
 * の2種類が選択できますが、どちらの設定であっても下記のように呼び出すことで
 * 適切にセッションを開始することができます.
 *
 * $sessionFactory = SC_SessionFactory::getInstance()
 * $sessionFactory->initSession();
 *
 * @package SC_Session
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_SessionFactory {

    // }}}
    // {{{ functions

    /**
     * パラメーター管理で設定したセッション維持設定に従って適切なオブジェクトを返す.
     *
     * @return SC_SessionFactory
     */
    function getInstance() {

        $type = defined('SESSION_KEEP_METHOD')
            ? SESSION_KEEP_METHOD
            : '';

        switch ($type) {
            // セッションの維持にリクエストパラメーターを使用する
            case 'useRequest':
                $session = new SC_SessionFactory_UseRequest_Ex;
                SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE
                    ? $session->setState('mobile')
                    : $session->setState('pc');
                break;

            // クッキーを使用する
            case 'useCookie':
            default:
                // モバイルの場合はSC_SessionFactory_UseRequestを使用する
                if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
                    $session = new SC_SessionFactory_UseRequest_Ex;
                    $session->setState('mobile');
                } else {
                    $session = new SC_SessionFactory_UseCookie_Ex;
                }
                break;
        }

        return $session;
    }

    /**
     * セッションの初期化を行う.
     *
     */
    function initSession() {}

    /**
     * Cookieを使用するかどうかを返す.
     *
     * @return boolean
     */
    function useCookie() {}

}
/*
 * Local variables:
 * coding: utf-8
 * End:
 */
