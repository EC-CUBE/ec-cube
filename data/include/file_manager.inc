<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
 
/* 
 * 関数名：sfGetFileList()
 * 説明　：指定パス配下のディレクトリ取得
 * 引数1 ：取得するディレクトリパス
 */
function sfGetFileList($dir) {
	$arrFileList = array();
	$arrDirList = array();
	
	if (is_dir($dir)) {
		if ($dh = opendir($dir)) { 
			$cnt = 0;
			// 行末の/を取り除く
			while (($file = readdir($dh)) !== false) $arrDir[] = $file;
			$dir = ereg_replace("/$", "", $dir);
			// アルファベットと数字でソート
			natcasesort($arrDir);
			foreach($arrDir as $file) {				
				// ./ と ../を除くファイルのみを取得
				if($file != "." && $file != "..") {

					$path = $dir."/".$file;
					// SELECT内の見た目を整えるため指定文字数で切る
					$file_name = sfCutString($file, FILE_NAME_LEN);						
					$file_size = sfCutString(sfGetDirSize($path), FILE_NAME_LEN);
					$file_time = date("Y/m/d", filemtime($path));
					
					// ディレクトリとファイルで格納配列を変える
					if(is_dir($path)) {
						$arrDirList[$cnt]['file_name'] = $file;
						$arrDirList[$cnt]['file_path'] = $path;
						$arrDirList[$cnt]['file_size'] = $file_size;
						$arrDirList[$cnt]['file_time'] = $file_time; 
						$arrDirList[$cnt]['is_dir'] = true;
					} else {
						$arrFileList[$cnt]['file_name'] = $file;
						$arrFileList[$cnt]['file_path'] = $path;
						$arrFileList[$cnt]['file_size'] = $file_size;
						$arrFileList[$cnt]['file_time'] = $file_time; 
						$arrFileList[$cnt]['is_dir'] = false;
					}
					$cnt++;
				}
	        }
	        closedir($dh); 
	    }
	}

	// フォルダを先頭にしてマージ
	return array_merge($arrDirList, $arrFileList);
}

/* 
 * 関数名：sfGetDirSize()
 * 説明　：指定したディレクトリのバイト数を取得
 * 引数1 ：ディレクトリ
 */
function sfGetDirSize($dir) {
	if(file_exists($dir)) {
		// ディレクトリの場合下層ファイルの総量を取得
		if (is_dir($dir)) {
		    $handle = opendir($dir); 
		    while ($file = readdir($handle)) {
				// 行末の/を取り除く
				$dir = ereg_replace("/$", "", $dir);
				$path = $dir."/".$file;
		        if ($file != '..' && $file != '.' && !is_dir($path)) { 
		            $bytes += filesize($path); 
		        } else if (is_dir($path) && $file != '..' && $file != '.') {
					// 下層ファイルのバイト数を取得する為、再帰的に呼び出す。
		            $bytes += sfGetDirSize($path); 
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
 * 関数名：sfDeleteDir()
 * 説明　：指定したディレクトリを削除
 * 引数1 ：削除ファイル
 */
function sfDeleteDir($dir) {
	$arrResult = array();
	if(file_exists($dir)) {
		// ディレクトリかチェック
		if (is_dir($dir)) {
			if ($handle = opendir("$dir")) {
				$cnt = 0;
				while (false !== ($item = readdir($handle))) {
					if ($item != "." && $item != "..") {
						if (is_dir("$dir/$item")) {
							sfDeleteDir("$dir/$item");
						} else {
							$arrResult[$cnt]['result'] = @unlink("$dir/$item");
							$arrResult[$cnt]['file_name'] = "$dir/$item";
						}
					}
					$cnt++;
				}
			}
			closedir($handle);
			$arrResult[$cnt]['result'] = @rmdir($dir);
			$arrResult[$cnt]['file_name'] = "$dir/$item";
		} else {
			// ファイル削除
			$arrResult[0]['result'] = @unlink("$dir");
			$arrResult[0]['file_name'] = "$dir";			
		}
	}
	
	return $arrResult;
}

/* 
 * 関数名：sfGetFileTree()
 * 説明　：ツリー生成用配列取得(javascriptに渡す用)
 * 引数1 ：ディレクトリ
 * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
 */
function sfGetFileTree($dir, $tree_status) {
	
	$cnt = 0;
	$arrTree = array();
	$default_rank = count(split('/', $dir));

	// 文末の/を取り除く
	$dir = ereg_replace("/$", "", $dir);	
	// 最上位層を格納(user_data/)
	if(sfDirChildExists($dir)) {
		$arrTree[$cnt]['type'] = "_parent";
	} else {
		$arrTree[$cnt]['type'] = "_child";	
	}
	$arrTree[$cnt]['path'] = $dir;
	$arrTree[$cnt]['rank'] = 0;
	$arrTree[$cnt]['count'] = $cnt;
	// 初期表示はオープン
	if($_POST['mode'] != '') {
		$arrTree[$cnt]['open'] = lfIsFileOpen($dir, $tree_status);
	} else {
		$arrTree[$cnt]['open'] = true;
	}
	$cnt++;	

	sfGetFileTreeSub($dir, $default_rank, $cnt, $arrTree, $tree_status);

	return $arrTree;
}

/* 
 * 関数名：sfGetFileTree()
 * 説明　：ツリー生成用配列取得(javascriptに渡す用)
 * 引数1 ：ディレクトリ
 * 引数2 ：デフォルトの階層(/区切りで　0,1,2・・・とカウント)
 * 引数3 ：連番
 * 引数4 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
 */
function sfGetFileTreeSub($dir, $default_rank, &$cnt, &$arrTree, $tree_status) {
	
	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) $arrDir[] = $item; 
			// アルファベットと数字でソート
			natcasesort($arrDir);
			foreach($arrDir as $item) {
				if ($item != "." && $item != "..") {
					// 文末の/を取り除く
					$dir = ereg_replace("/$", "", $dir);
					$path = $dir."/".$item;
					// ディレクトリのみ取得
					if (is_dir($path)) {
						$arrTree[$cnt]['path'] = $path;
						if(sfDirChildExists($path)) {
							$arrTree[$cnt]['type'] = "_parent";
						} else {
							$arrTree[$cnt]['type'] = "_child";	
						}
						
						// 階層を割り出す
						$arrCnt = split('/', $path);
						$rank = count($arrCnt);
						$arrTree[$cnt]['rank'] = $rank - $default_rank + 1;
						$arrTree[$cnt]['count'] = $cnt;
						// フォルダが開いているか
						$arrTree[$cnt]['open'] = lfIsFileOpen($path, $tree_status);
						$cnt++;
						// 下層ディレクトリ取得の為、再帰的に呼び出す
						sfGetFileTreeSub($path, $default_rank, $cnt, $arrTree, $tree_status);
					}
				}
			}
		}
		closedir($handle);
	}
}

/* 
 * 関数名：sfDirChildExists()
 * 説明　：指定したディレクトリ配下にファイルがあるか
 * 引数1 ：ディレクトリ
 */
function sfDirChildExists($dir) {
	if(file_exists($dir)) {
		if (is_dir($dir)) {
		    $handle = opendir($dir); 
		    while ($file = readdir($handle)) {
				// 行末の/を取り除く
				$dir = ereg_replace("/$", "", $dir);
				$path = $dir."/".$file;
				if ($file != '..' && $file != '.' && is_dir($path)) { 
					return true;
		        } 
		    } 
		}
	}
    
	return false;
}

/* 
 * 関数名：lfIsFileOpen()
 * 説明　：指定したファイルが前回開かれた状態にあったかチェック
 * 引数1 ：ディレクトリ
 * 引数2 ：現在のツリーの状態開いているフォルダのパスが | 区切りで格納
 */
function lfIsFileOpen($dir, $tree_status) {
	$arrTreeStatus = split('\|', $tree_status);
	if(in_array($dir, $arrTreeStatus)) {
		return true;
	}
	
	return false;
}

/* 
 * 関数名：sfDownloadFile()
 * 引数1 ：ファイルパス
 * 説明　：ファイルのダウンロード
 */
function sfDownloadFile($file) {
 	// ファイルの場合はダウンロードさせる
	Header("Content-disposition: attachment; filename=".basename($file));
	Header("Content-type: application/octet-stream; name=".basename($file));
	Header("Cache-Control: ");
	Header("Pragma: ");
	echo (sfReadFile($file));
}

/* 
 * 関数名：sfCreateFile()
 * 引数1 ：ファイルパス
 * 引数2 ：パーミッション
 * 説明　：ファイル作成
 */
function sfCreateFile($file, $mode = "") {
	// 行末の/を取り除く
	if($mode != "") {
		$ret = @mkdir($file, $mode);
	} else {
		$ret = @mkdir($file);
	}
	
	return $ret;
}

/* 
 * 関数名：sfReadFile()
 * 引数1 ：ファイルパス
 * 説明　：ファイル読込
 */
function sfReadFile($filename) { 
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
