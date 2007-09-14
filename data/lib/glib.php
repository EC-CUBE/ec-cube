<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*----------------------------------------------------------------------
 * [̾��] gfDownloadCsv
 * [����] �����ǡ�����CSV�Ȥ��ơ����饤����Ȥ˥�������ɤ�����
 * [����] 1:�إå�ʸ���� 2:CSV�ǡ���
 * [����] -
 * [��¸] -
 * [���] �����ϣ������Ȥ⥫��޶��ڤ�ˤʤäƤ��뤳��
 *----------------------------------------------------------------------*/
function gfDownloadCsv($header, $contents){
	
	$fiest_name = date("YmdHis") .".csv";
	
	/* HTTP�إå��ν��� */
	Header("Content-disposition: attachment; filename=${fiest_name}");
	Header("Content-type: application/octet-stream; name=${fiest_name}");
	
	$return = $header.$contents;
	if (mb_detect_encoding($return) == CHAR_CODE){						//ʸ���������Ѵ�
		$return = mb_convert_encoding($return,'SJIS',CHAR_CODE);
		$return = str_replace( array( "\r\n", "\r" ), "\n", $return);	// ������ˡ������
	}
	echo $return;
}

/*----------------------------------------------------------------------
 * [̾��] gfSetCsv
 * [����] �����������CSV�������Ѵ�����
 * [����] 1:CSV�ˤ������� 2:����1��Ϣ���������ź��������ꤷ������
 * [����] CSV�ǡ���
 * [��¸] -
 * [���] -
 *----------------------------------------------------------------------*/
function gfSetCsv( $array, $arrayIndex = "" ){	
	//����$arrayIndex�ϡ�$array��Ϣ������ΤȤ���ź��������ꤷ�Ƥ�뤿��˻��Ѥ���
	
	$return = "";
	for ($i=0; $i<count($array); $i++){
		
		for ($j=0; $j<count($array[$i]); $j++ ){
			if ( $j > 0 ) $return .= ",";
			$return .= "\"";			
			if ( $arrayIndex ){
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$arrayIndex[$j]] )) ."\"";			
			} else {
				$return .= mb_ereg_replace("<","��",mb_ereg_replace( "\"","\"\"",$array[$i][$j] )) ."\"";
			}
		}
		$return .= "\n";			
	}
	
	return $return;
}

/*----------------------------------------------------------------------
 * [̾��] gfGetAge
 * [����] ���դ��ǯ���׻����롣
 * [����] 1:����ʸ����(yyyy/mm/dd��yyyy-mm-dd hh:mm:ss��)
 * [����] ǯ��ο���
 * [��¸] -
 * [���] -
 *----------------------------------------------------------------------*/
function gfGetAge($dbdate)
{
    $ty = date("Y");
    $tm = date("m");
    $td = date("d");
    list($by, $bm, $bd) = split("[-/ ]", $dbdate);
    $age = $ty - $by;
	if($tm * 100 + $td < $bm * 100 + $bd) $age--;
    return $age;
}

/*----------------------------------------------------------------------
 * [̾��] gfDebugLog
 * [����] ���ե�������ѿ��ξܺ٤���Ϥ��롣
 * [����] �оݤȤʤ��ѿ�
 * [����] �ʤ�
 * [��¸] gfPrintLog
 * [���] -
 *----------------------------------------------------------------------*/
function gfDebugLog($obj, $path = DEBUG_LOG_PATH){
		gfPrintLog("*** start Debug ***");
		ob_start();
		print_r($obj);
		$buffer = ob_get_contents();
		ob_end_clean();
		$fp = fopen($path, "a+");
		fwrite( $fp, $buffer."\n" );
		fclose( $fp );
		gfPrintLog("*** end Debug ***");

		// ���ơ������
		gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, $path);
}

/*----------------------------------------------------------------------
 * [̾��] gfPrintLog
 * [����] ���ե�����������������ե�����̾����å����������
 * [����] ɽ����������å�����
 * [����] �ʤ�
 * [��¸] �ʤ�
 * [���] -
 *----------------------------------------------------------------------*/
function gfPrintLog($mess, $path = '') {
	// ���դμ���
	$today = date("Y/m/d H:i:s");
	// ���ϥѥ��κ���
	if ($path == "") {
		$path = LOG_PATH;
	}

	// ���������פ���Ƥ���ʸ�����Ȥ��᤹
	$trans_tbl = get_html_translation_table (HTML_ENTITIES);
    $trans_tbl = array_flip ($trans_tbl);
    $mess = strtr($mess, $trans_tbl);

	$fp = fopen($path, "a+");
	if($fp) {
		fwrite( $fp, $today." [".$_SERVER['PHP_SELF']."] ".$mess." from ". $_SERVER['REMOTE_ADDR']. "\n" );
		fclose( $fp );
	}
	
	// ���ơ������
	gfLogRotation(MAX_LOG_QUANTITY, MAX_LOG_SIZE, $path);
}

/**			
 * �����ơ������ǽ			
 *			
 * @param integer $max_log ����ե������
 * @param integer $max_size ���祵����
 * @param string  $path �ե�����ѥ�
 * @return void �ʤ�
 */			
function gfLogRotation($max_log, $max_size, $path) {
	
	// �ǥ��쥯�ȥ�̾�����
	$dirname = dirname($path);
	// �ե�����̾�����
	$basename = basename($path);
	
	// �ե����뤬���祵������Ķ���Ƥ��ʤ��������å�
	if(filesize($path) > $max_size) {
		if ($dh = opendir($dirname)) {
			while (($file = readdir($dh)) !== false) {
				// �����ơ������ˤƺ������줿�ե���������
				if(ereg("^". $basename . "\." , $file)) {
					$arrLog[] = $file;
				}
			}
			
			// �ե������������Ŀ��ʤ�ʾ�ʤ�Ť��ե����뤫��������
			$count = count($arrLog);
			if($count >= $max_log) {
				$diff = $count - $max_log;
				for($i = 0; $diff >= $i ; $i++) {
					unlink($dirname. "/" .array_pop($arrLog));
				}	
			}
			
			// ���ե������ź�����򤺤餹
			$count = count($arrLog);
			for($i = $count; 1 <= $i; $i--) {
				$move_number = $i + 1;
				@copy("$dirname/" . $arrLog[$i - 1], "$path.$move_number");
			}
			$ret = copy($path, "$path.1");
			
			// �������ե���������
			if($ret) {
				unlink($path);			
				touch($path);
				chmod($path, 0666);
			}
		}
	}
}

/*----------------------------------------------------------------------
 * [̾��] gfMakePassword
 * [����] ������ѥ���������ʱѿ�����
 * [����] �ѥ���ɤη��
 * [����] �������������줿�ѥ����
 * [��¸] �ʤ�
 * [���] -
 *----------------------------------------------------------------------*/
function gfMakePassword($pwLength) {
	
	// ���ɽ�Υ����ɤ����
	srand((double)microtime() * 54234853);
	
	// �ѥ����ʸ�������������
	$character = "abcdefghkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ2345679";
	$pw = preg_split("//", $character, 0, PREG_SPLIT_NO_EMPTY);
	
	$password = "";
	for($i = 0; $i<$pwLength; $i++ ) {
		$password .= $pw[array_rand($pw, 1)];
	}

	return $password;
} 

/*----------------------------------------------------------------------
 * [̾��] sf_explodeExt
 * [����] �ե�����γ�ĥ�Ҽ���
 * [����] �ե�����̾
 * [����] ��ĥ��
 * [��¸] �ʤ�
 * [���] -
 *----------------------------------------------------------------------*/
function gf_explodeExt($fileName) {
	$ext1 = explode(".", $fileName);
	$ext2 = $ext1[count($ext1) - 1];
	$ext2 = strtolower($ext2);
	return $ext2;
}


/*----------------------------------------------------------------------------------------------------------------------
 * [̾��] gfMailHeaderAddr
 * [����] ���Ϥ��줿�᡼�륢�ɥ쥹��᡼��ؿ��Ѥΰ�����Ѵ�
 * [����] �֥᡼�륢�ɥ쥹�פޤ��ϡ�̾��<�᡼�륢�ɥ쥹>�ס�ʣ�����ɥ쥹������ϥ���޶��ڤ�ǻ��ꤹ�롣
 * [����] �֥᡼�륢�ɥ쥹�פޤ��ϡ�JIS_MIME�˥������Ѵ�����̾�� <�᡼�륢�ɥ쥹>�ס�ʣ�����ɥ쥹������ϥ���޶��ڤ���ֵѤ��롣
 * [��¸] �ʤ�
 * [���] -
 *----------------------------------------------------------------------------------------------------------------------*/

function gfMailHeaderAddr($str) {
	$addrs = explode(",", $str); //���ɥ쥹������������
    foreach ($addrs as $addr) {
        if (preg_match("/^(.+)<(.+)>$/", $addr, $matches)) {
            //��������̾��<�᡼�륢�ɥ쥹>�פξ��
            $mailaddrs[] = mb_encode_mimeheader(trim($matches[1]))." <".trim($matches[2]).">";
        } else {
            //�᡼�륢�ɥ쥹�Τߤξ��
            $mailaddrs[] =  trim($addr);
        }
    }
    return implode(", ", $mailaddrs); //ʣ�����ɥ쥹�ϥ���޶��ڤ�ˤ���
}
?>