<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../lib/thumb.php");

/*----------------------------------------------------------------------
 * [名称] GC_Thumb
 * [概要] アップロードファイル加工クラス(thumb.phpとセットで使用する)
 * [引数] -
 * [戻値] -
 * [依存] thumb.php
 * [注釈] -
 *----------------------------------------------------------------------*/

Class GC_Thumb {

	var $tempPath;

	function GC_Thumb($tempFilePath = "") {
		$this->tempPath	 = $_SERVER['DOCUMENT_ROOT'] . $tempFilePath;
	}

	//--- 一時ファイル生成(サムネイル画像生成用)
	function makeImageTempFile($fileName, $phpFileName, $max_width,$max_height) {
		// 一意なIDを取得する。
		$mainname = uniqid("").".";
		// 拡張子以外を置き換える。
		$newFileName = ereg_replace("^.*\.",$mainname,$fileName);
		$result  = MakeThumb( $phpFileName, $this->tempPath, $max_width, $max_height, $newFileName );
		return $newFileName;
	}

	//--- 一時ファイル生成
	function makeTempFile($fileName, $phpFileName) {
		$newFileNname = str_replace("'", "’", $fileName );
		$newFileNname = date("siU") . $newFileNname;
		copy( $phpFileName, $this->tempPath . $newFileNname );
		return $newFileNname;	
	}

	//--- ファイルを指定保存DIRへ移動
	function fileMove($fileName, $dirName) {
		if(copy( $this->tempPath . $fileName , $_SERVER['DOCUMENT_ROOT'] . $dirName . $fileName)) {
			unlink( $this->tempPath . $fileName );
		}
	}

	//---- 一時DIRのファイルを一括削除	
	function execDeleteTempFile() {
		chdir( $this->tempPath );
		$delFile = glob( "*.*" );
		if( is_array($delFile) ) foreach( $delFile as $val ) @unlink( $val );
	}

	//---- 指定ファイルを削除	
	function fileDelete($fileName, $dirName) {
		unlink( $_SERVER['DOCUMENT_ROOT'] . $dirName . $fileName );
	}
}
?>