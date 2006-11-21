<?php



sfGetFileTree($default_dir);
print_r($arrTree);

/* 
 * �ؿ�̾��sfGetFileTree()
 * ���������ĥ꡼�������������(javascript���Ϥ���)
 * ����1 ���ǥ��쥯�ȥ�
 */
function sfGetFileTree($dir) {
	$default_dir = "/home/web/test.ec-cube.net/html/user_data/";
	$arrCnt = split('/', $default_dir);
	$default_rank = count($arrCnt);
	
	$arrTree = array();
	$cnt = 0;

	function sfGetFileTreeSub($dir) {
	
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
							$rank = $rank - $default_rank;
							
							// javascript�Υĥ꡼�����Ѥ���������
							$arrTree[$cnt] = array($cnt, $file_type, $path, $rank);
							$cnt++;
							// ���إǥ��쥯�ȥ�����ΰ١��Ƶ�Ū�˸ƤӽФ�
							sfGetFileTreeSub($path);
						}
					}
				}
			}
			closedir($handle);
		}
	}
	sfGetFileTreeSub($default_dir);
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