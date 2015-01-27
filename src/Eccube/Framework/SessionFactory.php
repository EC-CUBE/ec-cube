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

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Display;

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
 * $sessionFactory = \Eccube\Framework\SessionFactory::getInstance()
 * $sessionFactory->initSession();
 *
 * @package Session
 * @author LOCKON CO.,LTD.
 */
class SessionFactory
{
    /**
     * パラメーター管理で設定したセッション維持設定に従って適切なオブジェクトを返す.
     *
     * @return \Eccube\SessionFactory
     */
    public static function getInstance()
    {
        $type = defined('SESSION_KEEP_METHOD')
            ? SESSION_KEEP_METHOD
            : '';

        switch ($type) {
            // セッションの維持にリクエストパラメーターを使用する
            case 'useRequest':
                $session = new \Eccube\SessionFactory\UseRequestSessionFactory();
                Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE
                    ? $session->setState('mobile')
                    : $session->setState('pc');
                break;

            // クッキーを使用する
            case 'useCookie':
            default:
                // モバイルの場合はEccube\SessionFactory\UseRequestを使用する
                if (Application::alias('eccube.display')->detectDevice() == DEVICE_TYPE_MOBILE) {
                    $session = new \Eccube\Framework\SessionFactory\UseRequestSessionFactory();
                    $session->setState('mobile');
                } else {
                    $session = new \Eccube\Framework\SessionFactory\UseCookieSessionFactory();
                }
                break;
        }

        return $session;
    }

    /**
     * セッションの初期化を行う.
     *
     */
    public function initSession()
    {
        session_set_save_handler(array(&$this, 'sfSessOpen'),
            array(&$this, 'sfSessClose'),
            array(&$this, 'sfSessRead'),
            array(&$this, 'sfSessWrite'),
            array(&$this, 'sfSessDestroy'),
            array(&$this, 'sfSessGc'));

        // 通常よりも早い段階(オブジェクトが破棄される前)でセッションデータを書き込んでセッションを終了する
        // XXX APC による MDB2 の破棄タイミングによる不具合を回避する目的
        register_shutdown_function('session_write_close');
    }

    /**
     * Cookieを使用するかどうかを返す.
     *
     * @return boolean|null
     */
    public function useCookie()
    {
    }

    /**
     * セッションを開始する.
     *
     * @param  string $save_path    セッションを保存するパス(使用しない)
     * @param  string $session_name セッション名(使用しない)
     * @return bool   セッションが正常に開始された場合 true
     */
    public function sfSessOpen($save_path, $session_name)
    {
        return true;
    }

    /**
     * セッションを閉じる.
     *
     * @return bool セッションが正常に終了した場合 true
     */
    public function sfSessClose()
    {
        return true;
    }

    /**
     * セッションのデータをDBから読み込む.
     *
     * @param  string $id セッションID
     * @return string セッションデータの値
     */
    public function sfSessRead($id)
    {
        $objQuery = Application::alias('eccube.query');
        $arrRet = $objQuery->select('sess_data', 'dtb_session', 'sess_id = ?', array($id));
        if (empty($arrRet)) {
            return '';
        } else {
            return $arrRet[0]['sess_data'];
        }
    }

    /**
     * セッションのデータをDBに書き込む.
     *
     * @param  string $id        セッションID
     * @param  string $sess_data セッションデータの値
     * @return bool   セッションの書き込みに成功した場合 true
     */
    public function sfSessWrite($id, $sess_data)
    {
        $objQuery = Application::alias('eccube.query');
        $exists = $objQuery->exists('dtb_session', 'sess_id = ?', array($id));
        $sqlval = array();
        if ($exists) {
            // レコード更新
            $sqlval['sess_data'] = $sess_data;
            $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
            $objQuery->update('dtb_session', $sqlval, 'sess_id = ?', array($id));
        } else {
            // セッションデータがある場合は、レコード作成
            if (strlen($sess_data) > 0) {
                $sqlval['sess_id'] = $id;
                $sqlval['sess_data'] = $sess_data;
                $sqlval['update_date'] = 'CURRENT_TIMESTAMP';
                $sqlval['create_date'] = 'CURRENT_TIMESTAMP';
                $objQuery->insert('dtb_session', $sqlval);
            }
        }

        return true;
    }

    // セッション破棄

    /**
     * セッションを破棄する.
     *
     * @param  string $id セッションID
     * @return bool   セッションを正常に破棄した場合 true
     */
    public function sfSessDestroy($id)
    {
        $objQuery = Application::alias('eccube.query');
        $objQuery->delete('dtb_session', 'sess_id = ?', array($id));

        return true;
    }

    /**
     * ガーベジコレクションを実行する.
     *
     * 引数 $maxlifetime の代りに 定数 MAX_LIFETIME を使用する.
     *
     * @param integer $maxlifetime セッションの有効期限(使用しない)
     * @return bool
     */
    public function sfSessGc($maxlifetime)
    {
        // MAX_LIFETIME以上更新されていないセッションを削除する。
        $objQuery = Application::alias('eccube.query');
        $limit = date("Y-m-d H:i:s", time() - MAX_LIFETIME);
        $where = "update_date < '". $limit . "' ";
        $objQuery->delete('dtb_session', $where);

        return true;
    }
}
