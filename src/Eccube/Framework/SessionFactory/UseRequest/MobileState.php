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

use Eccube\Application;
use Eccube\Framework\MobileUserAgent;

/**
 * モバイルサイト用のセッションデータ管理クラス
 *
 */
class MobileState extends AbstractState
{

    /**
     * コンストラクタ
     * セッションのデータ構造は下のようになる.
     * $_SESSION['mobile']=> array(
     *     ['model']   => 901sh
     *     ['ip']      => 127.0.0.1
     *     ['expires'] => 1204699031
     *     ['phone_id']=> ****
     * )
     */
    public function __construct()
    {
        $this->namespace = 'mobile';
        $this->lifetime = MOBILE_SESSION_LIFETIME;
        $this->validate = array('NameSpace', 'Model', 'Expire');
    }

    /**
     * 携帯の機種名を設定する
     *
     */
    public function updateModel()
    {
        $this->setValue('model', MobileUserAgent::getModel());
    }

    /**
     * セッション中の携帯機種名と、アクセスしてきたブラウザの機種名が同じかどうかを判定する
     *
     * @return boolean
     */
    public function validateModel()
    {
        $modelInSession = $this->getModel();
        $model = MobileUserAgent::getModel();
        if (!empty($model) && $model === $modelInSession) {
            return true;
        }

        return false;
    }

    /**
     * 携帯のIDを取得する
     *
     * @return string
     */
    public function getPhoneId()
    {
        return $this->getValue('phone_id');
    }

    /**
     * 携帯のIDを登録する.
     *
     */
    public function updatePhoneId()
    {
        $this->setValue('phone_id', MobileUserAgent::getId());
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
        $this->updatePhoneId();
    }

}
