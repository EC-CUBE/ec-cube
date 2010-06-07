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
require_once(CLASS_PATH . "pages/LC_Page.php");

/**
 * RSS のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_RSS extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = "rss/index.tpl";
        $this->encode = "UTF-8";
        $this->description = "新着情報";
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objQuery = new SC_Query();
        $objView = new SC_SiteView(false);

        //新着情報を取得
        $arrNews = $this->lfGetNews($objQuery);

        //キャッシュしない(念のため)
        header("pragma: no-cache");

        //XMLテキスト(これがないと正常にRSSとして認識してくれないツールがあるため)
        header("Content-type: application/xml");

        //新着情報をセット
        $this->arrNews = $arrNews;
        $this->timestamp = SC_Utils_Ex::sf_mktime("r", $arrNews[0]['hour'], $arrNews[0]['minute'], $arrNews[0]['second'], $arrNews[0]['month'], $arrNews[0]['day'], $arrNews[0]['year']);

        //店名をセット
        $this->site_title = $arrNews[0]['shop_name'];

        //代表Emailアドレスをセット
        $this->email = $arrNews[0]['email'];

        //セットしたデータをテンプレートファイルに出力
        $objView->assignobj($this);

        //画面表示
        $objView->display($this->tpl_mainpage, true);
    }

    /**
     * デストラクタ.
     *
     * @return void
     */
    function destroy() {
        parent::destroy();
    }

    /**
     * 新着情報を取得する
     *
     * @param SC_Query $objQuery DB操作クラス
     * @return array $arrNews 取得結果を配列で返す
     */
    function lfGetNews(&$objQuery){
        $col = "";
        $col .= "     news_id ";								//新着情報ID
        $col .= "     ,news_title ";							//新着情報タイトル
        $col .= "     ,news_comment ";							//新着情報本文

        if (DB_TYPE == "pgsql") {
            $col .= "     ,to_char(news_date, 'YYYY') AS YEAR ";	//日付(年)
            $col .= "     ,to_char(news_date, 'MM') AS MONTH ";		//日付(月)
            $col .= "     ,to_char(news_date, 'DD') AS DAY ";		//日付(日)
            $col .= "     ,to_char(news_date, 'HH24') AS HOUR ";	//日付(時間)
            $col .= "     ,to_char(news_date, 'MI') AS MINUTE ";	//日付(分)
            $col .= "     ,to_char(news_date, 'SS') AS SECOND ";	//日付(秒)
        }else if (DB_TYPE == "mysql") {
            $col .= "     ,DATE_FORMAT(news_date, '%Y') AS YEAR ";		//日付(年)
            $col .= "     ,DATE_FORMAT(news_date, '%m') AS MONTH ";		//日付(月)
            $col .= "     ,DATE_FORMAT(news_date, '%d') AS DAY ";		//日付(日)
            $col .= "     ,DATE_FORMAT(news_date, '%H') AS HOUR ";		//日付(時間)
            $col .= "     ,DATE_FORMAT(news_date, '%i') AS MINUTE ";	//日付(分)
            $col .= "     ,DATE_FORMAT(news_date, '%s') AS SECOND ";	//日付(秒)
        }
        $col .= "     ,news_url ";								//新着情報URL
        $col .= "     ,news_select ";							//新着情報の区分(1:URL、2:本文)
        $col .= "     ,(SELECT shop_name FROM dtb_baseinfo limit 1) AS shop_name  ";	//店名
        $col .= "     ,(SELECT email04 FROM dtb_baseinfo limit 1) AS email ";			//代表Emailアドレス
        $from = "dtb_news";
        $where = "del_flg = '0'";
        $order = "rank DESC";
        $objQuery->setOrder($order);
        $arrNews = $objQuery->select($col,$from,$where);
        return $arrNews;
    }
}
?>
