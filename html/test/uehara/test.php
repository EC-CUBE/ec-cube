<?php

$arrTree = array();
$cnt = 0;
print_r(sfGetFileTree("/home/web/test.ec-cube.net/html/user_data/"));

function sfGetFileTree($dir) {
	global $arrTree;

	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					if (is_dir("$dir/$item")) {
						sfGetFileTree("$dir/$item");
					}
				}
				$cnt++;
			}
		}
		closedir($handle);
		$arrResult[$cnt]['file_name'] = "$dir/$item";
	}

	return $arrResult;
}

?>