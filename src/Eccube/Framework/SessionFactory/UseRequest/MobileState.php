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
