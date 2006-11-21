<?php


$default_dir = "/home/web/test.ec-cube.net/html/user_data/";
sfGetFileTree($default_dir);
print_r($arrTree);

/* 
 * 関数名：sfGetFileTree()
 * 説明　：ツリー生成用配列取得(javascriptに渡す用)
 * 引数1 ：ディレクトリ
 */
function sfGetFileTree($dir) {
	
	$cnt = 0;
	$arrTree = array();
	$default_rank = count(split('/', $dir));
	
	sfGetFileTreeSub($dir, $default_rank, $cnt, $arrTree);
}

function sfGetFileTreeSub($dir, $default_rank, $cnt, &$arrTree) {
	
	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					// 文末の/を取り除く
					$dir = ereg_replace("/$", "", $dir);
					$path = $dir."/".$item;
					// ディレクトリのみ取得
					if (is_dir($path)) {
						$path;
						if(sfDirChildExists($path)) {
							$file_type = "_parent";
						} else {
							$file_type = "_child";	
						}
						
						// 階層を割り出す
						$arrCnt = split('/', $path);
						$rank = count($arrCnt);
						$rank = $rank - $default_rank;
						
						// javascriptのツリー生成用の配列を作成
						$arrTree[] = array($cnt, $file_type, $path, $rank);
						$cnt++;
						// 下層ディレクトリ取得の為、再帰的に呼び出す
						sfGetFileTreeSub($path, $default_rank, $cnt, $arrTree);
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
		// ディレクトリの場合下層ファイルの総量を取得
		if (is_dir($dir)) {
		    $handle = opendir($dir);
		    while ($file = readdir($handle)) {
				// 行末の/を取り除く
				$dir = ereg_replace("/$", "", $dir);
				$path = $dir."/".$file;
		        if ($file != '..' && $file != '.') {
					return true;
		        }
		    }
		}
	}
    
	return false;
}
?>