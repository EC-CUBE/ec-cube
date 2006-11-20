<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
/* 
 * 関数名：getFileList()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：ツリーを格納配列
 * 引数2 ：取得するディレクトリパス
 */
function getFileList($dir) {
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
 * 引数1 ：ディレクトリ
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
	}
	// ディレクトリ(ファイル)が存在しない場合は0byteを返す
	if($bytes == "") $bytes = 0;
	
    return $bytes;
}

/* 
 * 関数名：deleteDir()
 * 説明　：指定したディレクトリを削除
 * 引数1 ：削除ファイル
 */
function deleteDir($dir) {
	$arrResult = array();
	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			$cnt = 0;
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						deleteDir("$dir/$item");
					} else {
						$arrResult[$cnt]['result'] = unlink("$dir/$item");
						$arrResult[$cnt]['file_name'] = "$dir/$item";
					}
				}
				$cnt++;
			}
		}
		closedir($handle);
		rmdir($dir);
	}
	
	return $arrResult;
}

 
/* 
 * 関数名：lfDownloadFile()
  * 引数1 ：ファイルパス
  * 説明　：エラーチェック
 */
function downloadFile($file) {
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

    return $str; 
}
?>
