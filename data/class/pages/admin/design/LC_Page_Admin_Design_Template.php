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
require_once(DATA_PATH . "module/Tar.php");

/**
 * テンプレート設定 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Template extends LC_Page {

    // }}}
    // {{{ functions

    /** テンプレートデータ種別 */
    var $arrSubnavi = array(
                     'title' => array(
                                1 => 'top',
                                2 => 'product',
                                3 => 'detail',
                                4 => 'mypage'
                                             ),
                     'name' =>array(
                                1 => 'TOPページ',
                                2 => '商品一覧ページ',
                                3 => '商品詳細ページ',
                                4 => 'MYページ'
                              )
                     );

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->arrTemplateName = $this->arrSubnavi;
        $this->tpl_mainpage = 'design/template.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno = 'template';
        $this->tpl_subno_template = $this->arrSubnavi['title'][1];
        $this->tpl_TemplateName = $this->arrTemplateName['name'][1];
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'テンプレート設定';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $objView = new SC_AdminView();
        $objSess = new SC_Session();

        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // GETの値を受け取る
        $get_tpl_subno_template = isset($_GET['tpl_subno_template']) 
            ? $_GET['tpl_subno_template'] : "";
        if (!isset($_POST['tpl_subno_template'])) $_POST['tpl_subno_template'] = "";
        if (!isset($_POST['mode'])) $_POST['mode'] = "";

        // GETで値が送られている場合にはその値を元に画面表示を切り替える
        if ($get_tpl_subno_template != ""){
            // 送られてきた値が配列に登録されていなければTOPを表示
            if (in_array($get_tpl_subno_template,$this->arrSubnavi['title'])){
                $tpl_subno_template = $get_tpl_subno_template;
            }else{
                $tpl_subno_template = $this->arrSubnavi['title'][1];
            }
        } else {
            // GETで値がなければPOSTの値を使用する
            if ($_POST['tpl_subno_template'] != ""){
                $tpl_subno_template = $_POST['tpl_subno_template'];
            }else{
                $tpl_subno_template = $this->arrSubnavi['title'][1];
            }
        }
        $this->tpl_subno_template = $tpl_subno_template;
        $key = array_keys($this->arrSubnavi['title'], $tpl_subno_template);
        $this->template_name = $this->arrSubnavi['name'][$key[0]];

        // 登録を押された場合にはDBへデータを更新に行く
        switch($_POST['mode']) {
        case 'confirm':
            // DBへデータ更新
            $this->lfUpdData();

            // テンプレートの上書き
            $this->lfChangeTemplate();

            // 完了メッセージ
            $this->tpl_onload="alert('登録が完了しました。');";
            break;
        case 'download':
            $this->lfDownloadTemplate($_POST['check_template']);
            break;
        default:
            break;
        }

        // POST値の引き継ぎ
        $this->arrForm = $_POST;

        // 画像取得
        $tpl_arrTemplate = array();
        $this->arrTemplate = $this->lfgetTemplate();

        // デフォルトチェック取得
        $this->MainImage = $this->arrTemplate['check'];
        $this->arrTemplate['check'] = array($this->arrTemplate['check']=>"check");

        // 画面の表示
        $objView->assignobj($this);
        $objView->display(MAIN_FRAME);

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
     * 画面に表示する画像を取得する.
     *
     * @return array 画面に表示する画像の配列
     */
    function lfgetTemplate(){
        $filepath = "user_data/templates/";

        $arrTemplateImage = array();	// 画面表示画像格納用
        $Image = "";					// イメージの配列要素名格納用
        $disp = "";
        $arrDefcheck = array();			// radioボタンのデフォルトチェック格納用

        // DBから現在選択されているデータ取得
        $arrDefcheck = $this->lfgetTemplaeBaseData();

        // テンプレートデータを取得する
        $objQuery = new SC_Query();
        $sql = "SELECT template_code,template_name FROM dtb_templates ORDER BY create_date DESC";
        $arrTemplate = $objQuery->getall($sql);

        switch($this->tpl_subno_template) {
            // TOP
        case $this->arrSubnavi['title'][1]:
            $Image = "TopImage.jpg";            // イメージの配列要素名格納用
            $disp = $this->arrSubnavi['title'][1];
            break;

            // 商品一覧
        case $this->arrSubnavi['title'][2]:
            $Image = "ProdImage.jpg";           // イメージの配列要素名格納用
            $disp = $this->arrSubnavi['title'][2];
            break;

            // 商品詳細
        case $this->arrSubnavi['title'][3]:
            $Image = "DetailImage.jpg";         // イメージの配列要素名格納用
            $disp = $this->arrSubnavi['title'][3];
            break;

            // MYページ
        case $this->arrSubnavi['title'][4]:
            $Image = "MypageImage.jpg";         //イメージの配列要素名格納用
            $disp = $this->arrSubnavi['title'][4];
            break;
        }

        // 画像表示配列作成
        foreach($arrTemplate as $key => $val){
            $arrTemplateImage['image'][$val['template_code']] = $filepath . $val['template_code'] . "/" . $Image;
            $arrTemplateImage['code'][$key] = $val['template_code'];
            $arrTemplateImage['name'][$key] = $val['template_name'];
        }

        // 初期チェック
        if (isset($arrDefcheck[$disp])){
            $arrTemplateImage['check'] = $arrDefcheck[$disp];
        }else{
            $arrTemplateImage['check'] = 1;
        }

        return $arrTemplateImage;
    }

    /**
     * DBに保存されているテンプレートデータを取得する.
     *
     * @return array DBに保存されているテンプレートデータの配列
     */
    function lfgetTemplaeBaseData(){
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用

        $sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
        $arrRet = $objDBConn->getAll($sql);

        return $arrRet[0];
    }

    /**
     * DBにデータを保存する.
     *
     * @return integer 成功した場合 1
     */
    function lfUpdData(){
        $objDBConn = new SC_DbConn;		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用(更新判定)

        // データ取得
        $sql = "SELECT top_tpl AS top, product_tpl AS product, detail_tpl AS detail, mypage_tpl AS mypage FROM dtb_baseinfo";
        $arrRet = $objDBConn->getAll($sql);

        $chk_tpl = $_POST['check_template'];
        // データが取得できなければINSERT、できればUPDATE
        if (isset($arrRet[0])){
            // UPDATE
            $arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
            $sql= "update dtb_baseinfo set top_tpl = ?, product_tpl = ?, detail_tpl = ?, mypage_tpl = ?, update_date = now()";
        }else{
            // INSERT
            $arrVal = array($chk_tpl,$chk_tpl,$chk_tpl,$chk_tpl);
            $sql= "insert into dtb_baseinfo (top_tpl,product_tpl,detail_tpl,mypage_tpl, update_date) values (?,?,?,?,now());";
        }

        // SQL実行
        $arrRet = $objDBConn->query($sql,$arrVal);

        return $arrRet;
    }

    /**
     * テンプレートを変更する.
     *
     * @return void
     */
    function lfChangeTemplate(){
        $data = array();
        $masterData = new SC_DB_MasterData_Ex();

        // FIXME DBのデータを更新
        $masterData->updateMasterData("mtb_constants", array(),
                array("TEMPLATE_NAME" => '"' . $_POST['check_template'] . '"'));

        // 更新したデータを取得
        $mtb_constants = $masterData->getDBMasterData("mtb_constants");

        // キャッシュを生成
        $masterData->clearCache("mtb_constants");
        $masterData->createCache("mtb_constants", $mtb_constants, true,
                                 array("id", "remarks", "rank"));
    }

    /**
     * テンプレートファイル圧縮してダウンロードする.
     *
     * @param string テンプレートコード
     * @return void
     */
    function lfDownloadTemplate($template_code){
        require_once(CLASS_EX_PATH . "helper_extends/SC_Helper_FileManager_Ex.php");
        $objFileManager = new SC_Helper_FileManager_Ex();
        $filename = $template_code. ".tar.gz";
        $dl_file = USER_TEMPLATE_PATH.$filename;
        $target_path = USER_TEMPLATE_PATH . $template_code . "/";

        $mess = "";
        // Smarty テンプレートをコピー
        $target_smarty = $target_path . "Smarty/";
        $mess .= SC_Utils_Ex::sfCopyDir(DATA_PATH . "Smarty/templates/" . $template_code . "/", $target_smarty, $mess);

        // ファイルの圧縮
        $tar = new Archive_Tar($dl_file, TRUE);
        // ファイル一覧取得
        $arrFileHash = $objFileManager->sfGetFileList(USER_TEMPLATE_PATH.$template_code);
        foreach($arrFileHash as $val) {
            $arrFileList[] = $val['file_name'];
        }
        // ディレクトリを移動
        chdir(USER_TEMPLATE_PATH.$template_code);

        //圧縮をおこなう
        $zip = $tar->create($arrFileList);

        // ダウンロード開始
        Header("Content-disposition: attachment; filename=${filename}");
        Header("Content-type: application/octet-stream; name=${dl_file}");
        header("Content-Length: " .filesize($dl_file));
        readfile ($dl_file);
        // 圧縮ファイル削除
        unlink($dl_file);

        exit();
    }
}
?>
