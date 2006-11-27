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

	// 拡大率を指定して画像保存
	function saveResizeImage($file, $zip_scale = 1, $header = false) {
		// ディレクトリ取得
		$dir = dirname($file);
		
		// 元画像サイズを取得
		list($src_w, $src_h) = getimagesize($file);
	
		// 圧縮率指定
		$zip_width = $src_w * $zip_scale;
		$zip_height = $src_h * $zip_scale;
		
		// ファイルの拡張子取得	
		$arrFileInfo = pathinfo($file);
		$extension = $arrFileInfo["extension"];

		// 一意なIDを取得する。
		$uniqname = date("mdHi") . "_" . uniqid("");
		
		// ファイル名、保存先設定
		$filename = $uniqname . "." . $extension;
		$path = $dir . "/" . $filename;

		// ファイルの拡張子によって処理を分ける
		if(is_dir($dir)) {
			switch ($extension)	{
				case "jpg":
				case "jpeg":
					//元画像
					$src_im = ImageCreateFromJPEG($file);
					
					// 圧縮先画像
					$dst_im = imagecreatetruecolor($zip_width, $zip_height);	
					imagecopyresampled($dst_im, $src_im, 0, 0, 0,0, $zip_width, $zip_height, $src_w, $src_h);
					
					// 画像出力
					if($header){
						header("Content-Type: image/jpeg");	
						ImageJPEG($dst_im);
					}else{
						ImageJPEG($dst_im, $path);
					}

					break;
				case "gif":
					//元画像
					$src_im = ImageCreateFromGIF($file);
					
					// 圧縮先画像
					$dst_im = imagecreatetruecolor($zip_width, $zip_height);	
					imagecopyresampled($dst_im, $src_im, 0, 0, 0,0, $zip_width, $zip_height, $src_w, $src_h);
					
					// 画像出力
					if($header) header("Content-Type: image/gif");
					
					ImageGIF($dst_im, $path);
					break;
				case "png":
					//元画像
					$src_im = ImageCreateFromPNG($file);
					
					// 圧縮先画像
					$dst_im = imagecreatetruecolor($zip_width, $zip_height);	
					imagecopyresampled($dst_im, $src_im, 0, 0, 0,0, $zip_width, $zip_height, $src_w, $src_h);

					// 画像出力
					if($header) header("Content-Type: image/png");
					
					ImagePNG($dst_im, $path);
					break;
				default:
					print("拡張子が不正です。");
					$path = "";
					break;
			}
			ImageDestroy($src_im);
			ImageDestroy($dst_im);

			if(!$header){
				return $path;
			}else{
				return "";
			}
		}
		
		print("画像の保存に失敗しました。");
		return "";
	}
}
?>