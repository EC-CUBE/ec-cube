<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
require_once CLASS_EX_REALDIR . 'page_extends/frontparts/bloc/LC_Page_FrontParts_Bloc_Ex.php';

/**
 * 新着情報 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_FrontParts_Bloc_News extends LC_Page_FrontParts_Bloc_Ex 
{

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process()
    {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action()
    {

        $objNews = new SC_Helper_News_Ex();
        $objFormParam = new SC_FormParam_Ex();
        switch ($this->getMode()) {
            case 'getList':
                $this->lfInitNewsParam($objFormParam);
                $objFormParam->setParam($_POST);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError(false);
                if (empty($this->arrErr)) {
                    $arrData = $objFormParam->getHashArray();
                    $json = $this->lfGetNewsForJson($arrData, $objNews);
                    echo $json;
                    SC_Response_Ex::actionExit();
                } else {
                    echo $this->lfGetErrors($this->arrErr);
                    SC_Response_Ex::actionExit();
                }
                break;
            case 'getDetail':
                $this->lfInitNewsParam($objFormParam);
                $objFormParam->setParam($_GET);
                $objFormParam->convParam();
                $this->arrErr = $objFormParam->checkError(false);
                if (empty($this->arrErr)) {
                    $arrData = $objFormParam->getHashArray();
                    $json = $this->lfGetNewsDetailForJson($arrData, $objNews);
                    echo $json;
                    SC_Response_Ex::actionExit();
                } else {
                    echo $this->lfGetErrors($this->arrErr);
                    SC_Response_Ex::actionExit();
                }
                break;
            default:
                $this->arrNews = $objNews->getList();
                $this->newsCount = $objNews->getCount();
                break;
        }

    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy()
    {
        parent::destroy();
    }

    /**
     * 新着情報パラメーター初期化
     *
     * @param array $objFormParam フォームパラメータークラス
     * @return void
     */
    function lfInitNewsParam(&$objFormParam)
    {
        $objFormParam->addParam('現在ページ', 'pageno', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('表示件数', 'disp_number', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
        $objFormParam->addParam('新着ID', 'news_id', INT_LEN, 'n', array('NUM_CHECK', 'MAX_LENGTH_CHECK'), '', false);
    }

    /**
     * 新着情報を取得する.
     *
     * @return array $arrNewsList 新着情報の配列を返す
     */
    function lfGetNews($dispNumber, $pageNo, SC_Helper_News_Ex $objNews)
    {
        $arrNewsList = $objNews->getList($dispNumber, $pageNo);

        // モバイルサイトのセッション保持 (#797)
        if (SC_Display_Ex::detectDevice() == DEVICE_TYPE_MOBILE) {
            foreach ($arrNewsList as $key => $value) {
                $arrRow =& $arrNewsList[$key];
                if (SC_Utils_Ex::isAppInnerUrl($arrRow['news_url'])) {
                    $netUrl = new Net_URL($arrRow['news_url']);
                    $netUrl->addQueryString(session_name(), session_id());
                    $arrRow['news_url'] = $netUrl->getURL();
                }
            }
        }

        return $arrNewsList;
    }

    /**
     * 新着情報をJSON形式で取得する
     * (ページと表示件数を指定)
     *
     * @param array $arrData フォーム入力値
     * @param object $objNews
     * @return String $json 新着情報のJSONを返す
     */
    function lfGetNewsForJson($arrData, SC_Helper_News_Ex $objNews)
    {

        $dispNumber = $arrData['disp_number'];
        $pageNo = $arrData['pageno'];
        $arrNewsList = $this->lfGetNews($dispNumber, $pageNo, $objNews);

        //新着情報の最大ページ数をセット
        $newsCount = $objNews->getCount();
        $arrNewsList['news_page_count'] = ceil($newsCount / 3);

        $json =  SC_Utils_Ex::jsonEncode($arrNewsList);    //JSON形式

        return $json;
    }

    /**
     * 新着情報1件分をJSON形式で取得する
     * (news_idを指定)
     *
     * @param array $arrData フォーム入力値
     * @param object $objNews
     * @return String $json 新着情報1件分のJSONを返す
     */
    function lfGetNewsDetailForJson($arrData, SC_Helper_News_Ex $objNews)
    {

        $arrNewsList = $objNews->get($arrData['news_id']);
        $json =  SC_Utils_Ex::jsonEncode($arrNewsList);    //JSON形式

        return $json;
    }

    /**
     * エラーメッセージを整形し, JSON 形式で返す.
     *
     * @param array $arrErr エラーメッセージの配列
     * @return string JSON 形式のエラーメッセージ
     */
    function lfGetErrors($arrErr)
    {
        $messages = '';
        foreach ($arrErr as $val) {
            $messages .= $val . "\n";
        }
        return SC_Utils_Ex::jsonEncode(array('error' => $messages));
    }
}
