<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
/* 
 * 関数名：lfGetFileList()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：ツリーを格納配列
 * 引数2 ：取得するディレクトリパス
 */
function lfGetFileList($dir) {
	$arrFileList = array();
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) { 
			$cnt = 0;
			while (($file = readdir($dh)) !== false) { 
				// ./ と ../を除くファイルのみを取得
				if($file != "." && $file != "..") {
					// 行末の/を取り除く
					$dir = ereg_replace("\/$", "", $dir);
					$path = $dir."/".$file;
					$arrFileList[$cnt]['file_name'] = $file;
					$arrFileList[$cnt]['file_path'] = $path;
					$arrFileList[$cnt]['file_size'] = getDirSize($path);
					$arrFileList[$cnt]['file_time'] = date("Y/m/d", filemtime($path)); 
					$cnt++;
				}
	        }
	        closedir($dh); 
	    }
	} 
	
	return $arrFileList;
}

/* 
 * 関数名：getDirSize()
 * 説明　：指定したディレクトリのバイト数を取得
 * 引数1 ：ファイル格納配列
 */
function getDirSize($dir) {
	if(file_exists($dir)) {
		// ディレクトリの場合下層ファイルの総量を取得
		if (is_dir($dir)) {
		    $handle = opendir($dir); 
		    while ($file = readdir($handle)) {
				// 行末の/を取り除く
				$dir = ereg_replace("\/$", "", $dir);
				$path = $dir."/".$file;
		        if ($file != '..' && $file != '.' && !is_dir($path)) { 
		            $bytes += filesize($path); 
		        } else if (is_dir($path) && $file != '..' && $file != '.') {
					// 下層ファイルのバイト数を取得する為、再帰的に呼び出す。
		            $bytes += getDirSize($path); 
		        } 
		    } 
		} else {
			// ファイルの場合
			$bytes = filesize($dir);
		}
	} else {
		// ディレクトリが存在しない場合は0byteを返す
		$bytes = 0;
	}
	
	if($bytes == "") $bytes = 0;
	
    return $bytes;
}
/* 
 * 関数名：lfDownloadFile()
  * 引数1 ：ファイルパス
  * 説明　：エラーチェック
 */
function lfDownloadFile($file) {
 	// ファイルの場合はダウンロードさせる
	Header("Content-disposition: attachment; filename=".basename($file));
	Header("Content-type: application/octet-stream; name=".basename($file));
	Header("Cache-Control: ");
	Header("Pragma: ");
	echo (lfReadFile($file));
}

/* 
 * 関数名：lfReadFile()
 * 引数1 ：ファイルパス
 * 説明　：ファイル読込
 */
function lfReadFile($filename) { 
    $str = ""; 
    // バイナリモードでオープン 
    $fp = @fopen($filename, "rb" ); 
    //ファイル内容を全て変数に読み込む 
    if($fp) { 
        $str = @fread($fp, filesize($filename)+1); 
    } 
    @fclose($fp);
sfprintr($str);
    return $str; 
}
?>
