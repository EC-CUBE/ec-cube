<?php


$default_dir = "/home/web/test.ec-cube.net/html/user_data/";
print_r(sfGetFileTree($default_dir));

/* 
 * �ؿ�̾��sfGetFileTree()
 * ���������ĥ꡼�������������(javascript���Ϥ���)
 * ����1 ���ǥ��쥯�ȥ�
 */
function sfGetFileTree($dir) {
	
	$cnt = 0;
	$arrTree = array();
	$default_rank = count(split('/', $dir));
	
	// �Ǿ����
	if(sfDirChildExists($dir)) {
		$file_type = "_parent";
	} else {
		$file_type = "_child";	
	}
	$arrTree[$cnt] = array($cnt, $file_type, $dir, 0);
	$cnt++;
	
	sfGetFileTreeSub($dir, $default_rank, $cnt, $arrTree);
	
	return $arrTree;
}

function sfGetFileTreeSub($dir, $default_rank, &$cnt, &$arrTree) {
	
	if(file_exists($dir)) {
		if ($handle = opendir("$dir")) {
			while (false !== ($item = readdir($handle))) {
				if ($item != "." && $item != "..") {
					// ʸ����/�������
					$dir = ereg_replace("/$", "", $dir);
					$path = $dir."/".$item;
					// �ǥ��쥯�ȥ�Τ߼���
					if (is_dir($path)) {
						$path;
						if(sfDirChildExists($path)) {
							$file_type = "_parent";
						} else {
							$file_type = "_child";	
						}
						
						// ���ؤ���Ф�
						$arrCnt = split('/', $path);
						$rank = count($arrCnt);
						$rank = $rank - $default_rank + 1;
						
						// javascript�Υĥ꡼�����Ѥ���������
						$arrTree[$cnt] = array($cnt, $file_type, $path, $rank);
						$cnt++;
						// ���إǥ��쥯�ȥ�����ΰ١��Ƶ�Ū�˸ƤӽФ�
						sfGetFileTreeSub($path, $default_rank, $cnt, $arrTree);
					}
				}
			}
		}
		closedir($handle);
	}
}

/* 
 * �ؿ�̾��sfDirChildExists()
 * �����������ꤷ���ǥ��쥯�ȥ��۲��˥ե����뤬���뤫
 * ����1 ���ǥ��쥯�ȥ�
 */
function sfDirChildExists($dir) {
	if(file_exists($dir)) {
		// �ǥ��쥯�ȥ�ξ�粼�إե���������̤����
		if (is_dir($dir)) {
		    $handle = opendir($dir);
		    while ($file = readdir($handle)) {
				// ������/�������
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