<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//---- アップロードファイル加工クラス(thumb.phpとセットで使用する)
class SC_Image {
	
	var $tmp_dir;
		
	function SC_Image($tmp_dir) {
		// ヘッダファイル読込
		$include_dir = realpath(dirname( __FILE__));
		require_once($include_dir . "/../lib/thumb.php");
		if(!ereg("/$", $tmp_dir)) {
			$this->tmp_dir = $tmp_dir . "/";
		} else {
			$this->tmp_dir = $tmp_dir;
		}
	}
	
	//--- 一時ファイル生成(サムネイル画像生成用)
	function makeTempImage($keyname, $max_width, $max_height) {
		// 一意なIDを取得する。
		$mainname = uniqid("").".";
		// 拡張子以外を置き換える。
		$newFileName = ereg_replace("^.*\.",$mainname, $_FILES[$keyname]['name']);
		$result  = MakeThumb($_FILES[$keyname]['tmp_name'], $this->tmp_dir , $max_width, $max_height, $newFileName);
		gfDebugLog($result);
		return $newFileName;
	}

	//--- ファイルを指定保存DIRへ移動
	function moveTempImage($filename, $save_dir) {
		// コピー元ファイル、コピー先ディレクトリが存在する場合にのみ実行する
		if(file_exists($this->tmp_dir.$filename) && file_exists($save_dir)) {
			if(copy($this->tmp_dir . $filename , $save_dir."/".$filename)) {
				unlink( $this->tmp_dir . $filename );
			}
		} else {
			gfDebugLog($this->tmp_dir.$filename."の移動に失敗しました。");
		}
	}

	//---- 指定ファイルを削除	
	function deleteImage($filename, $dir) {
		if(file_exists($dir."/".$filename)) {
			unlink($dir."/".$filename);
		}
	}
}
?>