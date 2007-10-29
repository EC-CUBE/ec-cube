<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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

// {{{ requires
require_once CLASS_PATH . 'pages/LC_Page.php';

/**
 * オーナーズストア認証キーを返すページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Upgrade_EchoKey extends LC_Page {

    /** Services_Jsonオブジェクト */
    var $objJson = null;
    /** SC_FromParamオブジェクト */
    var $objForm = null;

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        $this->objJson = new Services_JSON();

        $this->objForm = new SC_FormParam();
        $this->objForm->addParam('seed', 'seed', MLTEXT_LEN, '', array('EXIST_CHECK', 'ALNUM_CHECK', 'MAX_LENGTH_CHECK'));
        $this->objForm->setParam($_POST);
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $errFormat = '* error! code:%s / debug:%s';

        GC_Utils::gfPrintLog('###Echo Key Start###');

        // リクエストの検証
        if ($this->objForm->checkError()) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_EK_POST_PARAM,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_EK_POST_PARAM
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($_POST))
            );
            exit;
        }

        $public_key = $this->getPublicKey();

        // 認証キーが設定されていない場合
        if (empty($public_key)) {
            $arrErr = array(
                'status'  => OWNERSSTORE_STATUS_ERROR,
                'errcode' => OWNERSSTORE_ERR_EK_KEY_MISSING,
                'body' => '配信サーバとの通信中にエラーが発生しました。エラーコード:' . OWNERSSTORE_ERR_EK_KEY_MISSING
            );
            echo $this->objJson->encode($arrErr);
            GC_Utils::gfPrintLog(
                sprintf($errFormat, $arrErr['errcode'], serialize($_POST))
            );
            exit;
        }

        // 認証キー + 配信サーバから送られるランダムな値をsha1()にかけechoする
        $arrParams = array(
            'status' => OWNERSSTORE_STATUS_SUCCESS,
            'body'   => sha1($public_key . $this->objForm->getValue('seed'))
        );

        echo $this->objJson->encode($arrParams);
        GC_Utils::gfPrintLog('* echo key ok');
        exit;
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        GC_Utils::gfPrintLog('###Echo Key End###');
    }

     /**
     * DBから認証キーを取得する
     * 無い場合はnullを返す
     *
     * @param void
     * @return string|null 認証キー
     */
    function getPublicKey() {
        $table  = 'dtb_ownersstore_settings';
        $col    = 'public_key';

        $objQuery = new SC_Query();

        $arrRet = $objQuery->select($col, $table, $where);

        if (isset($arrRet[0]['public_key'])) {
            return $arrRet[0]['public_key'];
        }

        return null;
    }
}
?>
