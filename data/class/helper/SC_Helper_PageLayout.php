<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
 * Webページのレイアウト情報を制御するヘルパークラス.
 *
 * @package Helper
 * @author LOCKON CO.,LTD.
 * @version $Id:SC_Helper_PageLayout.php 15532 2007-08-31 14:39:46Z nanasess $
 */
class SC_Helper_PageLayout {

    // }}}
    // {{{ functions

    /**
     * ページのレイアウト情報をセットする.
     *
     * LC_Page オブジェクトにページのレイアウト情報をセットする.
     *
     * @param LC_Page $objPage ページ情報のインスタンス
     * @param boolean $preview プレビュー表示の場合 true
     * @param string $url ページのURL($_SERVER['PHP_SELF'] の情報)
     * @param integer $device_type_id 端末種別ID
     * @return void
     */
    function sfGetPageLayout(&$objPage, $preview = false, $url = "", $device_type_id = DEVICE_TYPE_PC) {
        $debug_message = "";
        $arrPageLayout = array();

        // 現在のURLの取得
        if ($preview === false) {
            $url = preg_replace('|^' . preg_quote(URL_PATH) . '|', '', $url);

            // URLを元にページデザインを取得
            $arrPageData = $this->lfGetPageData("device_type_id = ? AND url = ? AND page_id <> 0" , array($device_type_id, $url));
        } else {
            // TODO
            $arrPageData = $this->lfGetPageData("device_type_id = ? AND page_id = 0", array($device_type_id));
            $objPage->tpl_mainpage = $this->getTemplatePath($device_type_id) 
                . "preview/" . $arrPageData[0]['filename'] . ".tpl";
        }

        $arrPageLayout = $arrPageData[0];

        $objPage->tpl_mainpage = $this->getTemplatePath($device_type_id) . $arrPageLayout['tpl_dir'] . $arrPageLayout['filename'] . ".tpl";

        // ページタイトルを設定
        if (!isset($objPage->tpl_title)) {
            $objPage->tpl_title = $arrPageLayout['page_name'];
        }

        // 全ナビデータを取得する
        $arrNavi = $this->lfGetNaviData($arrPageLayout['page_id'], $device_type_id);
        $masterData = new SC_DB_MasterData();
        $arrTarget = $masterData->getMasterData("mtb_target");

        foreach (array_keys($arrTarget) as $key) {
            if (TARGET_ID_UNUSED != $key) {
                $arrPageLayout[$arrTarget[$key]]
                    = $this->lfGetNavi($arrNavi, $key, $device_type_id);
            }
        }
        $objPage->arrPageLayout = $arrPageLayout;

        // カラム数を取得する
        $objPage->tpl_column_num = $this->lfGetColumnNum($arrPageLayout);
    }

    /**
     * ページ情報を取得する.
     *
     * @param string $where クエリのWHERE句
     * @param array $arrVal WHERE句の条件値
     * @return array ページ情報を格納した配列
     */
    function lfGetPageData($where = 'page_id <> 0', $arrVal = array()) {
        $objQuery =& SC_Query::getSingletonInstance();
        $objQuery->setOrder('page_id');
        return $objQuery->select("*", "dtb_pagelayout", $where, $arrVal);
    }

    /**
     * ナビ情報を取得する.
     *
     * @param integer $page_id ページID
     * @param integer $device_type_id 端末種別ID
     * @return array ナビ情報の配列
     */
    function lfGetNaviData($page_id, $device_type_id = DEVICE_TYPE_PC) {
        $objQuery =& SC_Query::getSingletonInstance();
        $table = <<< __EOF__
            dtb_blocposition AS pos
       JOIN dtb_bloc AS bloc
         ON bloc.bloc_id = pos.bloc_id
        AND bloc.device_type_id = pos.device_type_id
__EOF__;
        $where = "bloc.device_type_id = ? AND (anywhere = 1 OR pos.page_id = ?)";
        $objQuery->setOrder('target_id, bloc_row');
        return $objQuery->select("*", $table, $where,
                                 array($device_type_id, $page_id));
    }

    /**
     * 各部分のナビ情報を取得する.
     *
     * @param array $arrNavi ナビ情報の配列
     * @param integer $target_id ターゲットID
     * @param integer $device_type_id 端末種別ID
     * @return array ブロック情報の配列
     */
    function lfGetNavi($arrNavi, $target_id, $device_type_id = DEVICE_TYPE_PC) {
        $arrRet = array();
        if (is_array($arrNavi)) {
            foreach ($arrNavi as $key => $val) {
                // 指定された箇所と同じデータだけを取得する
                if ($target_id == $val['target_id'] ) {
                    if ($val['php_path'] != '') {
                        $arrNavi[$key]['php_path'] = HTML_FILE_PATH . $val['php_path'];
                    } else {
                        $arrNavi[$key]['tpl_path'] = $this->getTemplatePath($device_type_id) . BLOC_DIR . $val['tpl_path'];
                    }
                    // phpから呼び出されるか、tplファイルが存在する場合
                    if ($val['php_path'] != '' || is_file($arrNavi[$key]['tpl_path'])) {
                        $arrRet[] = $arrNavi[$key];
                    } else {
                        GC_Utils::gfPrintLog("ブロック読み込みエラー：" . $arrNavi[$key]['tpl_path']);
                    }
                }
            }
        }
        return $arrRet;
    }

    /**
     * カラム数を取得する.
     *
     * @param array $arrPageLayout レイアウト情報の配列
     * @return integer $col_num カラム数
     */
    function lfGetColumnNum($arrPageLayout) {
        // メインは確定
        $col_num = 1;
        // LEFT NAVI
        if (count($arrPageLayout['LeftNavi']) > 0) $col_num++;
        // RIGHT NAVI
        if (count($arrPageLayout['RightNavi']) > 0) $col_num++;

        return $col_num;
    }

    /**
     * ページ情報を削除する.
     *
     * @param integer $page_id ページID
     * @param integer $device_type_id 端末種別ID
     * @return integer 削除数
     */
    function lfDelPageData($page_id, $device_type_id = DEVICE_TYPE_PC) {
        $objQuery =& SC_Query::getSingletonInstance();
        $arrDelData = array();      // 抽出データ用

        // page_id が空でない場合にはdeleteを実行
        if ($page_id != '') {

            $arrPageData = $this->lfGetPageData("page_id = ? AND device_type_id = ?" , array($page_id, $device_type_id));
            // SQL実行
            $ret = $objQuery->delete("dtb_pagelayout", "page_id = ? AND device_type_id = ?", array($page_id, $device_type_id));

            // ファイルの削除
            $this->lfDelFile($arrPageData[0]);
        }
        return $ret;
    }

    /**
     * ページのファイルを削除する.
     *
     * @param array $arrData ページ情報の配列
     * @return void // TODO boolean にするべき?
     */
    function lfDelFile($arrData) {
        // ファイルディレクトリ取得
        $del_php = HTML_FILE_PATH . $arrData['php_dir'] . $arrData['filename'] . ".php";
        $del_tpl = HTML_FILE_PATH . $arrData['tpl_dir'] . $arrData['filename'] . ".tpl";

        // phpファイルの削除
        if (file_exists($del_php)) {
            unlink($del_php);
        }

        // tplファイルの削除
        if (file_exists($del_tpl)) {
            unlink($del_tpl);
        }
    }

    /**
     * データがベースデータかどうか.
     *
     * @param integer $page_id ページID
     * @param integer $device_type_id 端末種別ID
     * @return boolean ベースデータの場合 true
     */
    function lfCheckBaseData($page_id, $device_type_id) {
        $result = false;

        if ($page_id == 0) {
            return $result;
        }

        $arrChkData = $this->lfgetPageData("page_id = ? AND device_type_id = ?",
                                           array($page_id, $device_type_id));

        if ($arrChkData[0]['edit_flg'] == 2) {
            $result = true;
        }

        return $result;
    }

    /**
     * テンプレートのパスを取得する.
     */
    function getTemplatePath($device_type_id = DEVICE_TYPE_PC, $isUser = false) {
        $templateName = "";
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            $dir = MOBILE_TEMPLATE_DIR;
            $userPath = HTML_FILE_PATH . MOBILE_DIR . USER_DIR;
            $templateName = MOBILE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            $dir = SMARTPHONE_TEMPLATE_DIR;
            $userPath = HTML_FILE_PATH . SMARTPHONE_DIR . USER_DIR;
            $templateName = SMARTPHONE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_PC:
        default:
            $dir = TEMPLATE_DIR;
            $userPath = USER_FILE_PATH;
            $templateName = TEMPLATE_NAME;
        }
        if ($isUser) {
            $dir = $userPath . USER_PACKAGE_DIR . $templateName . "/";
        }
        return $dir;
    }

    /**
     * user_data の絶対パスを返す.
     *
     * @param integer $device_type_id 端末種別ID
     * @return string 端末に応じた user_data の絶対パス
     */
    function getUserPath($device_type_id = DEVICE_TYPE_PC) {
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            return HTML_FILE_PATH . MOBILE_DIR . USER_DIR;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            return HTML_FILE_PATH . SMARTPHONE_DIR . USER_DIR;
            break;

        case DEVICE_TYPE_PC:
        default:
        }
        return USER_FILE_PATH;
    }

    /**
     * DocumentRoot から user_data のパスを取得する.
     *
     * 引数 $hasPackage を true にした場合は, user_data/packages/template_name
     * を取得する.
     *
     * @param integer $device_type_id 端末種別ID
     * @param boolean $hasPackage パッケージのパスも含める場合 true
     * @return string 端末に応じた DocumentRoot から user_data までのパス
     */
    function getUserDir($device_type_id = DEVICE_TYPE_PC, $hasPackage = false) {
        switch ($device_type_id) {
        case DEVICE_TYPE_MOBILE:
            $userDir = URL_PATH . MOBILE_DIR . USER_DIR;
            $templateName = MOBILE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_SMARTPHONE:
            $userDir = URL_PATH . SMARTPHONE_DIR . USER_DIR;
            $templateName = SMARTPHONE_TEMPLATE_NAME;
            break;

        case DEVICE_TYPE_PC:
        default:
            $userDir = URL_PATH . USER_DIR;
            $templateName = TEMPLATE_NAME;
        }
        if ($hasPackage) {
            return $userDir . USER_PACKAGE_DIR . $templateName . "/";
        }
        return $userDir;
    }
}
?>
