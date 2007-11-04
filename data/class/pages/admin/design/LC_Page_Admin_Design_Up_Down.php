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
require_once(DATA_PATH. "module/Tar.php");

/**
 * テンプレートアップロード のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id: LC_Page_Admin_Design_Up_Down.php 16582 2007-10-29 03:06:29Z nanasess $
 */
class LC_Page_Admin_Design_Up_Down extends LC_Page {

    // }}}
    // {{{ functions

    /**
     * Page を初期化する.
     *
     * @return void
     */
    function init() {
        parent::init();
	    $this->tpl_mainpage = 'design/up_down.tpl';
	    $this->tpl_subnavi  = 'design/subnavi.tpl';
	    $this->tpl_subno    = 'up_down';
	    $this->tpl_mainno   = "design";
	    $this->tpl_subtitle = 'アップロード/ダウンロード';
	    $this->arrErr  = array();
	    $this->arrForm = array();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    function process() {
    	// ログインチェック
		$objSession = new SC_Session();
		SC_Utils::sfIsSuccess($objSession);
		$this->now_template = $this->lfGetNowTemplate();
		
		// uniqidをテンプレートへ埋め込み
		$this->uniqid = $objSession->getUniqId();
		
		switch($this->lfGetMode()) {
		
		// ダウンロードボタン押下時の処理
		case 'download':
		    // 画面遷移の正当性チェック
		    if (!SC_Utils::sfIsValidTransition($objSession)) {
		        SC_Utils::sfDispError('');
		    }
		    $this->lfDownloadCreatedFiles();
		    exit;
		    break;
		
		// アップロードボタン押下時の処理
		case 'upload':
		    // 画面遷移の正当性チェック
		    if (!SC_Utils::sfIsValidTransition($objSession)) {
		        SC_Utils::sfDispError('');
		    }
		    // フォームパラメータ初期化
		    $objForm = $this->lfInitUpload();
		    // エラーチェック
		    if ($arrErr = $this->lfValidateUpload($objForm)) {
		        $this->arrErr  = $arrErr;
		        $this->arrForm = $objForm->getFormParamList();
		        break;
		    }
		    // アップロードファイル初期化
		    $objUpFile = $this->lfInitUploadFile($objForm);
		    // 一時ファイルへ保存
		    $errMsg = $objUpFile->makeTempFile('template_file', false);
		    // 書き込みエラーチェック
		    if(isset($errMsg)) {
		        $this->arrErr['template_file'] = $errMsg;
		        $this->arrForm = $objForm->getFormParamList();
		        break;
		    }
		    $this->lfAddTemplates($objForm, $objUpFile);
		    $this->tpl_onload = "alert('テンプレートファイルをアップロードしました。');";
		    break;
		
		// 初回表示
		default:
		    break;
		}
		
		// 画面の表示
		$objView = new SC_AdminView();
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
	 * POSTされるmodeパラメータを取得する.
	 *
	 * @param void
	 * @return string modeパラメータ, 無ければnull
	 */
	function lfGetMode(){
	    if (isset($_POST['mode'])) return $_POST['mode'];
	}
	/**
	 * SC_UploadFileクラスの初期化.
	 *
	 * @param object $objForm SC_FormParamのインスタンス
	 * @return object SC_UploadFileのインスタンス
	 */
	function lfInitUploadFile($objForm) {
	    $pkg_dir = TPL_PKG_PATH . $objForm->getValue('template_code');
	    $objUpFile = new SC_UploadFile(TEMPLATE_TEMP_DIR, $pkg_dir);
	    $objUpFile->addFile("テンプレートファイル", 'template_file', array(), TEMPLATE_SIZE, true, 0, 0, false);
	
	    return $objUpFile;
	}
	/**
	 * SC_FormParamクラスの初期化.
	 *
	 * @param void
	 * @retrun object SC_FormParamのインスタンス
	 */
	function lfInitUpload() {
	    $objForm = new SC_FormParam;
	
	    $objForm->addParam("テンプレートコード", "template_code", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK", "ALNUM_CHECK"));
	    $objForm->addParam("テンプレート名", "template_name", STEXT_LEN, "KVa", array("EXIST_CHECK","SPTAB_CHECK","MAX_LENGTH_CHECK"));
	    $objForm->setParam($_POST);
	
	    return $objForm;
	}
	/**
	 * uploadモードのパラメータ検証を行う.
	 *
	 * @param object $objForm SC_FormParamのインスタンス
	 * @return array エラー情報を格納した連想配列, エラーが無ければ(多分)nullを返す
	 */
	function lfValidateUpload($objForm) {
	    $arrErr = $objForm->checkError();
	    if (!empty($arrErr)) {
	        return $arrErr;
	    }
	
	    $arrForm = $objForm->getHashArray();
	
	    // 同名のフォルダが存在する場合はエラー
	    if(file_exists(USER_TEMPLATE_PATH . $arrForm['template_code'])) {
	        $arrErr['template_code'] = "※ 同名のファイルがすでに存在します。<br/>";
	    }
	
	    // 登録不可の文字列チェック
	    $arrIgnoreCode = array(
	        'admin', 'mobile', 'default'
	    );
	    if(in_array($arrForm['template_code'], $arrIgnoreCode)) {
	        $arrErr['template_code'] = "※ このテンプレートコードは使用できません。<br/>";
	    }
	
	    // DBにすでに登録されていないかチェック
	    $objQuery = new SC_Query();
	    $ret = $objQuery->count("dtb_templates", "template_code = ?", array($arrForm['template_code']));
	    if(!empty($ret)) {
	        $arrErr['template_code'] = "※ すでに登録されているテンプレートコードです。<br/>";
	    }
	
	    // ファイルの拡張子チェック(.tar/tar.gzのみ許可)
	    $errFlag = true;
	    $array_ext = explode(".", $_FILES['template_file']['name']);
	    $ext = $array_ext[ count ( $array_ext ) - 1 ];
	    $ext = strtolower($ext);
	    // .tarチェック
	    if ($ext == 'tar') {
	        $errFlag = false;
	    }
	    $ext = $array_ext[ count ( $array_ext ) - 2 ].".".$ext;
	    $ext = strtolower($ext);
	    // .tar.gzチェック
	    if ($ext== 'tar.gz') {
	        $errFlag = false;
	    }
	
	    if($errFlag) {
	        $arrErr['template_file'] = "※ アップロードするテンプレートファイルで許可されている形式は、tar/tar.gzです。<br />";
	    }
	
	    return $arrErr;
	}
	/**
	 * DBおよびTPL_PKG_PATHにテンプレートパッケージを追加する.
	 *
	 * @param object $objForm SC_FormParamのインスタンス
	 * @param object $objUpFile SC_UploadFileのインスタンス
	 * @return void
	 */
	function lfAddTemplates($objForm, $objUpFile) {
	    $template_code = $objForm->getValue('template_code');
	    $template_dir = TPL_PKG_PATH . $objForm->getValue('template_code');
	    $compile_dir  = COMPILE_DIR . "/$template_code";
	    // フォルダ作成
	    mkdir($template_dir);
	    mkdir($compile_dir);
	    // 一時フォルダから保存ディレクトリへ移動
	    $objUpFile->moveTempFile();
	    // 解凍
	    lfUnpacking($template_dir, $_FILES['template_file']['name']);
	    // DBにテンプレート情報を保存
	    lfRegisterTemplates($objForm->getHashArray());
	}
	/**
	 * アップロードされたtarアーカイブを解凍する.
	 *
	 * TODO 処理がわかりにくいので直す,
	 * $file_nameは$objUpFileの初期化時にTPL_PKG_PATHが保存先になっているため必要
	 *
	 * @param string $dir 解凍先ディレクトリ
	 * @param strin $file_name アーカイブのファイル名
	 * @return string Archive_Tar::extractModify()のエラー
	 */
	function lfUnpacking($dir, $file_name) {
	
	    // 圧縮フラグTRUEはgzip解凍をおこなう
	    $tar = new Archive_Tar("$dir/$file_name", true);
	
	    // 拡張子を切り取る
	    $unpacking_name = preg_replace("/(\.tar|\.tar\.gz)$/", "", $file_name);
	
	    // 指定されたフォルダ内に解凍する
	    $err = $tar->extractModify("$dir/", $unpacking_name);
	
	    // フォルダ削除
	    @sfDelFile("$dir/$unpacking_name");
	    // 圧縮ファイル削除
	    @unlink("$dir/$file_name");
	
	    return $err;
	}
	/**
	 * dtb_templatesへ入力内容を登録する.
	 *
	 * @param array $arrForm POSTされたパラメータ
	 * @return void
	 */
	function lfRegisterTemplates($arrForm) {
	    $objQuery = new SC_Query();
	    $objQuery->insert('dtb_templates', $arrForm);
	}
	/**
	 * ユーザが作成したファイルをアーカイブしダウンロードさせる
	 * TODO 要リファクタリング
	 * @param void
	 * @return void
	 */
	function lfDownloadCreatedFiles() {
	    $dlFileName = 'tpl_package_' . date('YmdHis') . '.tar.gz';
	    $tmpDir = TEMPLATE_TEMP_DIR . time() . '/';
	    $tmpUserEditDir = $tmpDir . 'user_edit/';
	
	    if (!mkdir($tmpDir)) return ;
	    if (!mkdir($tmpDir . 'templates')) return ;
	    if (!mkdir($tmpUserEditDir)) return ;
	
	    $this->lfCopyTplPackage($tmpDir);
	    $this->lfCopyUserEdit($tmpUserEditDir);
	
	    // ファイル一覧取得
	    $arrFileHash = SC_Utils::sfGetFileList($tmpDir);
	    foreach($arrFileHash as $val) {
	        $arrFileList[] = $val['file_name'];
	    }
	
	    // ディレクトリを移動
	    chdir($tmpDir);
	    // 圧縮をおこなう
	    $tar = new Archive_Tar($dlFileName, true);
	    $tar->create($arrFileList);
	
	    // ダウンロード用HTTPヘッダ出力
	    header("Content-disposition: attachment; filename=${dlFileName}");
	    header("Content-type: application/octet-stream; name=${dlFileName}");
	    header("Content-Length: " . filesize($dlFileName));
	    readfile($dlFileName);
	
	    // 圧縮ファイル削除
	    unlink($dlFileName);
	    // 一時フォルダ削除
	    SC_Utils::sfDelFile($tmpDir);
	}
	/**
	 * デザイン管理で作成されたファイルをupload/temp_template/以下にコピーする
	 *
	 * @param string $to
	 * @return void
	 */
	function lfCopyUserEdit($to) {
	    $arrDirs = array(
	        'css',
	        'include',
	        'templates'
	    );
	
	    foreach ($arrDirs as $dir) {
	        $from = USER_PATH .  $dir;
	        SC_Utils::sfCopyDir($from, $to, '', true);
	    }
	}
	/**
	 * 現在選択しているテンプレートパッケージをupload/temp_template/以下にコピーする
	 *
	 * @param string $to 保存先パス
	 * @return void
	 */
	function lfCopyTplPackage($to) {
	    $nowTpl = $this->lfGetNowTemplate();
	    if (!$nowTpl) return;
	
	    $from = TPL_PKG_PATH . $nowTpl . '/';
	    SC_Utils::sfCopyDir($from, $to, '');
	}
	/**
	 * 現在適用しているテンプレートパッケージ名を取得する.
	 *
	 * @param void
	 * @return string テンプレートパッケージ名
	 */
	function lfGetNowTemplate() {
	    $objQuery = new SC_Query();
	    $arrRet = $objQuery->select('top_tpl', 'dtb_baseinfo');
	    if (isset($arrRet[0]['top_tpl'])) {
	        return $arrRet[0]['top_tpl'];
	    }
	    return null;
	}    
}
?>
