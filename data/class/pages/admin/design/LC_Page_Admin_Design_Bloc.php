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

// {{{ requires
require_once(CLASS_PATH . "pages/admin/LC_Page_Admin.php");

/**
 * ブロック編集 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class LC_Page_Admin_Design_Bloc extends LC_Page_Admin {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
        $this->tpl_mainpage = 'design/bloc.tpl';
        $this->tpl_subnavi = 'design/subnavi.tpl';
        $this->tpl_subno_edit = 'bloc';
        $this->text_row = 13;
        $this->tpl_subno = "bloc";
        $this->tpl_mainno = "design";
        $this->tpl_subtitle = 'ブロック編集';
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のアクション.
     *
     * @return void
     */
    function action() {
        $this->objLayout = new SC_Helper_PageLayout_Ex();
        $package_path = USER_TEMPLATE_PATH . "/" . TEMPLATE_NAME . "/";
        
        // 認証可否の判定
        $objSess = new SC_Session();
        SC_Utils_Ex::sfIsSuccess($objSess);

        // ブロック一覧を取得
        $this->arrBlocList = $this->lfgetBlocData();

        // ブロックIDを取得
        if (isset($_POST['bloc_id'])) {
            $bloc_id = $_POST['bloc_id'];
        }else if (isset($_GET['bloc_id'])) {
            $bloc_id = $_GET['bloc_id'];
        }else{
            $bloc_id = '';
        }
        $this->bloc_id = $bloc_id;

        // bloc_id が指定されている場合にはブロックデータの取得
        if ($bloc_id != '') {
            $arrBlocData = $this->lfgetBlocData(" bloc_id = ? " , array($bloc_id));

            // ユーザー作成ブロックが存在する場合
            if (is_file($package_path . $arrBlocData[0]['tpl_path'])) {
                $arrBlocData[0]['tpl_path'] = $package_path . $arrBlocData[0]['tpl_path'];

            // 存在しない場合は指定テンプレートのブロックを取得
            } else {
                $arrBlocData[0]['tpl_path'] = TEMPLATE_DIR . $arrBlocData[0]['tpl_path'];
            }
            
            // テンプレートファイルの読み込み
            $arrBlocData[0]['tpl_data'] = file_get_contents($arrBlocData[0]['tpl_path']);
            $this->arrBlocData = $arrBlocData[0];
        }

        // メッセージ表示
        if (isset($_GET['msg']) && $_GET['msg'] == "on") {
            // 完了メッセージ
            $this->tpl_onload="alert('登録が完了しました。');";
        }

        if (!isset($_POST['mode'])) $_POST['mode'] = "";
                
        switch($_POST['mode']) {
        case 'preview':
            // プレビューファイル作成
            $prev_path = USER_INC_PATH . 'preview/bloc_preview.tpl';
            // ディレクトリの作成            
            SC_Utils::sfMakeDir($prev_path);            
            $fp = fopen($prev_path,"w");
            fwrite($fp, $_POST['bloc_html']); // FIXME いきなり POST はちょっと...
            fclose($fp);

            // プレビューデータ表示
            $this->preview = "on";
            $this->arrBlocData['tpl_data'] = $_POST['bloc_html'];
            $this->arrBlocData['tpl_path'] = $prev_path;
            $this->arrBlocData['bloc_name'] = $_POST['bloc_name'];
            $this->arrBlocData['filename'] = $_POST['filename'];
            $this->text_row = $_POST['html_area_row'];
        	break;
        case 'confirm':
        	$this->preview = "off";
            // エラーチェック
            $this->arrErr = $this->lfErrorCheck($_POST);

            // エラーがなければ更新処理を行う
            if (count($this->arrErr) == 0) {
                // DBへデータを更新する
                $this->lfEntryBlocData($_POST);

                // 旧ファイルの削除
                $old_bloc_path = $package_path . $arrBlocData[0]['tpl_path'];
                if (file_exists($old_bloc_path)) {
                    unlink($old_bloc_path);
                }
				
                // ファイル作成
                $new_bloc_path = $package_path . BLOC_DIR . $_POST['filename'] . ".tpl";
               	// ディレクトリの作成            
            	SC_Utils::sfMakeDir($new_bloc_path);
                $fp = fopen($new_bloc_path,"w");
                fwrite($fp, $_POST['bloc_html']); // FIXME いきなり POST はちょっと...
                fclose($fp);

                $arrBlocData = $this->lfgetBlocData(" filename = ? " , array($_POST['filename']));

                $bloc_id = $arrBlocData[0]['bloc_id'];
                $this->objDisplay->redirect($this->getLocation("./bloc.php",
                                            array("bloc_id" => $bloc_id,
                                                  "msg" => "on")));
                exit;
            }else{
                // エラーがあれば入力時のデータを表示する
                $this->arrBlocData = $_POST;
            }
        	break;
        case 'delete':
        	$this->preview = "off";
        	 // DBへデータを更新する
            $objQuery = new SC_Query();     // DB操作オブジェクト
            $sql = "";                      // データ更新SQL生成用
            $ret = "";                      // データ更新結果格納用
            $arrDelData = array();          // 更新データ生成用

            // 更新データ生成
            $arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);

            // bloc_id が空でない場合にはdeleteを実行
            if ($_POST['bloc_id'] !== '') {
                // SQL生成
                $sql = " DELETE FROM dtb_bloc WHERE bloc_id = ?";
                // SQL実行
                $ret = $objQuery->query($sql,array($_POST['bloc_id']));

                // ページに配置されているデータも削除する
                $sql = "DELETE FROM dtb_blocposition WHERE bloc_id = ?";
                // SQL実行
                $ret = $objQuery->query($sql,array($_POST['bloc_id']));

                // ファイルの削除
                $del_file = $package_path . BLOC_DIR . $arrBlocData[0]['filename']. '.tpl';
                if(file_exists($del_file)){
                    unlink($del_file);
                }
            }
            $this->objDisplay->redirect($this->getLocation("./bloc.php"));
            exit;
        	break;
        default:
        	if(isset($_POST['mode'])) {
        	   GC_Utils::gfPrintLog("MODEエラー：".$_POST['mode']);
        	}
        	break;
        }        
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
     * ブロック情報を取得する.
     *
     * @param string $where Where句文
     * @param array $arrVal Where句の絞込条件値
     * @return array ブロック情報
     */
    function lfgetBlocData($where = '', $arrVal = ''){
        $objQuery = new SC_Query();		// DB操作オブジェクト
        $sql = "";						// データ取得SQL生成用
        $arrRet = array();				// データ取得用

        // SQL生成
        $sql = " SELECT ";
        $sql .= "	bloc_id";
        $sql .= "	,bloc_name";
        $sql .= "	,tpl_path";
        $sql .= "	,filename";
        $sql .= " 	,create_date";
        $sql .= " 	,update_date";
        $sql .= " 	,php_path";
        $sql .= " 	,del_flg";
        $sql .= " FROM ";
        $sql .= " 	dtb_bloc";

        // where句の指定があれば追加
        if ($where != '') {
            $sql .= " WHERE " . $where;
        }

        $sql .= " ORDER BY 	bloc_id";

        $arrRet = $objQuery->getAll($sql, $arrVal);

        return $arrRet;
    }

    /**
     * ブロック情報を更新する.
     *
     * @param array $arrData 更新データ
     * @return integer 更新結果
     */
    function lfEntryBlocData($arrData){
        $objQuery = new SC_Query();		// DB操作オブジェクト
        $sql = "";						// データ更新SQL生成用
        $ret = ""; 						// データ更新結果格納用
        $arrUpdData = array();			// 更新データ生成用
        $arrChk = array();				// 排他チェック用

        // 更新データ生成
        $arrUpdData = array($arrData['bloc_name'], BLOC_DIR . $arrData['filename'] . '.tpl', $arrData['filename']);

        // データが存在しているかチェックを行う
        if($arrData['bloc_id'] !== ''){
            $arrChk = $this->lfgetBlocData("bloc_id = ?", array($arrData['bloc_id']));
        }


        }
        // bloc_id が空 若しくは データが存在していない場合にはINSERTを行う
        if ($arrData['bloc_id'] === '' or !isset($arrChk[0])) {
            // SQL生成
            $arrUpdData['bloc_id'] = $objQuery->nextVal('dtb_bloc_bloc_id');
            $arrUpdData['create_date'] = "now()";
            $ret = $objQuery->insert('dtb_bloc', $arrUpdData);
        } else {
            $ret = $objQuery->update('dtb_bloc', $arrUpdData, 'bloc_id = ?', array($arrData['bloc_id']));
        }
        return $ret;
    }

    /**
     * 入力項目のエラーチェックを行う.
     *
     * @param array $arrData 入力データ
     * @return array エラー情報
     */
    function lfErrorCheck($array) {
        $objErr = new SC_CheckError($array);

        $objErr->doFunc(array("ブロック名", "bloc_name", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
        $objErr->doFunc(array("ファイル名", "filename", STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK","FILE_NAME_CHECK"));

        // 同一のファイル名が存在している場合にはエラー
        if(!isset($objErr->arrErr['filename']) and $array['filename'] !== ''){
            $arrChk = $this->lfgetBlocData("filename = ?", array($array['filename']));

            if (count($arrChk[0]) >= 1 and $arrChk[0]['bloc_id'] != $array['bloc_id']) {
                $objErr->arrErr['filename'] = '※ 同じファイル名のデータが存在しています。別の名称を付けてください。';
            }
        }

        return $objErr->arrErr;
    }
}
?>
