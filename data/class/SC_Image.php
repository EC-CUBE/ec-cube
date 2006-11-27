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

	// ����Ψ����ꤷ�Ʋ�����¸
	function saveResizeImage($file, $zip_scale = 1) {
		// �ǥ��쥯�ȥ����
		$dir = dirname($file);
		
		// �����������������
		list($src_w, $src_h) = getimagesize($file);
	
		// ����Ψ����
		$zip_width = $src_w * $zip_scale;
		$zip_height = $src_h * $zip_scale;
		
		
		$src_im = ImageCreateFromJPEG($file);//������
		
		// ���������
		$dst_im = imagecreatetruecolor($zip_width, $zip_height);	
		imagecopyresampled($dst_im, $src_im, 0, 0, 0,0, $zip_width, $zip_height, $src_w, $src_h);

		// ��դ�ID��������롣
		$uniqname = date("mdHi") . "_" . uniqid("");

		// �ե�����γ�ĥ�Ҽ���		
		$arrFileInfo = pathinfo($file);
		$extension = $arrFileInfo["extension"];
		
		// �ե�����̾����¸������
		$filename = $uniqname . "." . $extension;
		$path = $dir . "/" . $filename;
		
		// �ե�����γ�ĥ�Ҥˤ�äƽ�����ʬ����
		if(is_dir($dir)) {
			switch ($extension)	{
				case "jpg":
				case "jpeg":
					ImageJPEG($dst_im, $path);
					break;
				case "gif":
					ImageGIF($dst_im, $path);
					break;
				case "png":
					ImagePNG($dst_im, $path);
					break;
				case "default":
					print("��ĥ�Ҥ������Ǥ���");
					return "";
					break;
			}
			sfprintr($extension);
			return $path;
		}
		
		print("��������¸�˼��Ԥ��ޤ�����");
		return "";
	}
}
?>