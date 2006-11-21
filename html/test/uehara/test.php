<?php

$arrTree = array();
$cnt = 0;
sfGetFileTree("/home/web/test.ec-cube.net/html/user_data/");
print_r($arrTree);
/*
function sfGetFileTree($dir) {
	global $arrTree;

	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						$arrResult[$cnt]['file_name'] = "$dir/$item";
						$cnt++;
						sfGetFileTree("$dir/$item");
					}
				}
				$cnt++;
			}
		}
		closedir($handle);
		//$arrResult[$cnt]['file_name'] = "$dir/$item";
	}

	return $arrResult;
}
*/

function sfGetFileTree($dir) {
	global $arrTree;
	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			$cnt = 0;
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					// 文末の/を取り除く
					$dir = ereg_replace("/$", "", $dir);
					// ディレクトリのみ取得
					if (is_dir("$dir/$item")) {
						$arrTree[]['file_name'] = "$dir/$item";
						// 下層ディレクトリ取得の為、再帰的に呼び出す
						sfGetFileTree("$dir/$item");
					}
				}
				$cnt++;
			}
		}
		closedir($handle);
	}
}


?>