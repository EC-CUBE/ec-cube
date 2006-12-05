<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

//---- ���åץ��ɥե�����ù����饹(thumb.php�ȥ��åȤǻ��Ѥ���)
class SC_Image {
	
	var $tmp_dir;
		
	function SC_Image($tmp_dir) {
		// �إå��ե������ɹ�
		$include_dir = realpath(dirname( __FILE__));
		require_once($include_dir . "/../lib/thumb.php");
		if(!ereg("/$", $tmp_dir)) {
			$this->tmp_dir = $tmp_dir . "/";
		} else {
			$this->tmp_dir = $tmp_dir;
		}
	}
	
	//--- ����ե���������(����ͥ������������)
	function makeTempImage($keyname, $max_width, $max_height) {
		// ��դ�ID��������롣
		$mainname = uniqid("").".";
		// ��ĥ�Ұʳ����֤������롣
		$newFileName = ereg_replace("^.*\.",$mainname, $_FILES[$keyname]['name']);
		$result  = MakeThumb($_FILES[$keyname]['tmp_name'], $this->tmp_dir , $max_width, $max_height, $newFileName);
		gfDebugLog($result);
		return $newFileName;
	}

	//--- �ե�����������¸DIR�ذ�ư
	function moveTempImage($filename, $save_dir) {
		// ���ԡ����ե����롢���ԡ���ǥ��쥯�ȥ꤬¸�ߤ�����ˤΤ߼¹Ԥ���
		if(file_exists($this->tmp_dir.$filename) && file_exists($save_dir)) {
			if(copy($this->tmp_dir . $filename , $save_dir."/".$filename)) {
				unlink( $this->tmp_dir . $filename );
			}
		} else {
			gfDebugLog($this->tmp_dir.$filename."�ΰ�ư�˼��Ԥ��ޤ�����");
		}
	}

	//---- ����ե��������	
	function deleteImage($filename, $dir) {
		if(file_exists($dir."/".$filename)) {
			unlink($dir."/".$filename);
		}
	}

}
?>