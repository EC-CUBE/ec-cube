<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Page;

use Eccube\Application;
use Eccube\Framework\FormParam;
use Eccube\Framework\Util\Utils;

/**
 * 郵便番号入力 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class InputZip extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        $this->skip_load_page_layout = true;
        parent::init();
        $this->tpl_message = '住所を検索しています。';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        // 入力エラーチェック
        $arrErr = $this->fnErrorCheck($_GET);
        // 入力エラーの場合は終了
        if (count($arrErr) > 0) {
            $tpl_message = '';
            foreach ($arrErr as $val) {
                $tpl_message .= preg_replace("/<br \/>/", "\n", $val);
            }
            echo $tpl_message;

        // エラー無し
        } else {
            // 郵便番号検索文作成
            $zipcode = $_GET['zip1'] . $_GET['zip2'];

            // 郵便番号検索
            $arrAdsList = Utils::sfGetAddress($zipcode);

            // 郵便番号が発見された場合
            if (!empty($arrAdsList)) {
                $data = $arrAdsList[0]['state']. '|'. $arrAdsList[0]['city']. '|'. $arrAdsList[0]['town'];
                echo $data;

            // 該当無し
            } else {
                echo '該当する住所が見つかりませんでした。';
            }
        }
    }

    /**
     * 入力エラーのチェック.
     *
     * @param  array $arrRequest リクエスト値($_GET)
     * @return array $arrErr エラーメッセージ配列
     */
    public function fnErrorCheck($arrRequest)
    {
        // パラメーター管理クラス
        $objFormParam = Application::alias('eccube.form_param');
        // パラメーター情報の初期化
        $objFormParam->addParam('郵便番号1', 'zip1', ZIP01_LEN, 'n', array('NUM_COUNT_CHECK', 'NUM_CHECK'));
        $objFormParam->addParam('郵便番号2', 'zip2', ZIP02_LEN, 'n', array('NUM_COUNT_CHECK', 'NUM_CHECK'));
        // // リクエスト値をセット
        $objFormParam->setParam($arrRequest);
        // エラーチェック
        $arrErr = $objFormParam->checkError();
        // 親ウィンドウの戻り値を格納するinputタグのnameのエラーチェック
        if (!$this->lfInputNameCheck($arrRequest['input1'])) {
            $arrErr['input1'] = '※ 入力形式が不正です。<br />';
        }
        if (!$this->lfInputNameCheck($arrRequest['input2'])) {
            $arrErr['input2'] = '※ 入力形式が不正です。<br />';
        }

        return $arrErr;
    }

    /**
     * エラーチェック.
     *
     * @param  string                 $value
     * @return boolean エラー：false
     */
    public function lfInputNameCheck($value)
    {
        // 半角英数字と_（アンダーバー）, []以外の文字を使用していたらエラー
        if (strlen($value) > 0 && !preg_match("/^[a-zA-Z0-9_\[\]]+$/", $value)) {
            return false;
        }

        return true;
    }
}
