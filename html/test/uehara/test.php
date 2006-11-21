<?php

$arrTree = array();
$cnt = 0;
print_r(sfGetFileTree("/home/web/test.ec-cube.net/html/user_data/"));
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
		// ディレクトリかチェック
		if (is_dir($dir)) {
			if ($handle = opendir("$dir")) {
				$cnt = 0;
				while (false !== ($item = readdir($handle))) {
					if ($item != "." && $item != "..") {
						if (is_dir("$dir/$item")) {
							sfGetFileTree("$dir/$item");
						} else {
							echo $arrTree[$cnt]['file_name'] = "$dir/$item<br/>";
						}
					}
					$cnt++;
				}
			}
			closedir($handle);
			echo $arrResult[$cnt]['file_name'] = "$dir/$item<br/>";
		}
	}
}


?>