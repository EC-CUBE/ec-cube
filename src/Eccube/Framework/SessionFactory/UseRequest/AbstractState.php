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

namespace Eccube\Framework\SessionFactory\UseRequest;

/**
 * セッションデータ管理クラスの基底クラス
 *
 */
abstract class AbstractState
{

    /** 名前空間(pc/mobile) */
    public $namespace = '';

    /** 有効期間 */
    public $lifetime = 0;

    /** エラーチェック関数名の配列 */
    public $validate = array();

    /**
     * 名前空間を取得する
     *
     * @return string
     */
    public function getNameSpace()
    {
        return $this->namespace;
    }

    /**
     * 有効期間を取得する
     *
     * @return integer
     */
    public function getLifeTime()
    {
        return $this->lifetime;
    }

    /**
     * セッションデータが設定されているかを判定する.
     * $_SESSION[$namespace]の値が配列の場合に
     * trueを返す.
     *
     * @return boolean
     */
    public function validateNameSpace()
    {
        $namespace = $this->getNameSpace();
        if (isset($_SESSION[$namespace]) && is_array($_SESSION[$namespace])) {
            return true;
        }
        GcUtils::gfPrintLog("NameSpace $namespace not found in session data : sid=" . session_id());

        return false;
    }

    /**
     * セッションのデータを取得する
     * 取得するデータは$_SESSION[$namespace][$key]となる.
     *
     * @param  string     $key
     * @return mixed|null
     */
    public function getValue($key)
    {
        $namespace = $this->getNameSpace();

        return isset($_SESSION[$namespace][$key]) ? $_SESSION[$namespace][$key] : null;
    }

    /**
     * セッションにデータを登録する.
     * $_SESSION[$namespace][$key] = $valueの形で登録される.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function setValue($key, $value)
    {
        $namespace = $this->getNameSpace();
        $_SESSION[$namespace][$key] = $value;
    }

    /**
     * 有効期限を取得する.
     *
     * @return integer
     */
    public function getExpire()
    {
        return $this->getValue('expires');
    }

    /**
     * 有効期限を設定する.
     *
     */
    public function updateExpire()
    {
        $lifetime = $this->getLifeTime();
        $this->setValue('expires', time() + $lifetime);
    }

    /**
     * 有効期限内かどうかを判定する.
     *
     * @return boolean
     */
    public function validateExpire()
    {
        $expire = $this->getExpire();
        if (intval($expire) > time()) {
            return true;
        }
        $date = date('Y/m/d H:i:s', $expire);
        GcUtils::gfPrintLog("Session expired at $date : sid=" . session_id());

        return false;
    }

    /**
     * IPアドレスを取得する.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->getValue('ip');
    }

    /**
     * IPアドレスを設定する.
     *
     */
    public function updateIp()
    {
        $this->setValue('ip', $_SERVER['REMOTE_ADDR']);
    }

    /**
     * REMOTE_ADDRとセッション中のIPが同じかどうかを判定する.
     * 同じ場合にtrueが返る
     *
     * @return boolean
     */
    public function validateIp()
    {
        $ip = $this->getIp();
        if (!empty($_SERVER['REMOTE_ADDR']) && $ip === $_SERVER['REMOTE_ADDR']) {
            return true;
        }

        $msg = sprintf('Ip Addr mismatch : %s != %s(expected) : sid=%s', $_SERVER['REMOTE_ADDR'], $ip, session_id());
        GcUtils::gfPrintLog($msg);

        return false;
    }

    /**
     * UserAgentもしくは携帯の機種名を取得する.
     *
     * @return string
     */
    public function getModel()
    {
        return $this->getValue('model');
    }

    /**
     * セッション中のデータ検証する
     *
     * @return boolean
     */
    public function validateSessionData()
    {
        foreach ($this->validate as $method) {
            $method = 'validate' . $method;
            if (!$this->$method()) {
                return false;
            }
        }

        return true;
    }

    /**
     * セッションデータを初期化する.
     *
     */
    public function inisializeSessionData()
    {
        
    }

}
