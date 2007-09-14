<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../lib/thumb.php");

/*----------------------------------------------------------------------
 * [̾��] GC_Thumb
 * [����] ���åץ��ɥե�����ù����饹(thumb.php�ȥ��åȤǻ��Ѥ���)
 * [����] -
 * [����] -
 * [��¸] thumb.php
 * [���] -
 *----------------------------------------------------------------------*/

Class GC_Thumb {

	var $tempPath;

	function GC_Thumb($tempFilePath = "") {
		$this->tempPath	 = $_SERVER['DOCUMENT_ROOT'] . $tempFilePath;
	}

	//--- ����ե���������(����ͥ������������)
	function makeImageTempFile($fileName, $phpFileName, $max_width,$max_height) {
		// ��դ�ID��������롣
		$mainname = uniqid("").".";
		// ��ĥ�Ұʳ����֤������롣
		$newFileName = ereg_replace("^.*\.",$mainname,$fileName);
		$result  = MakeThumb( $phpFileName, $this->tempPath, $max_width, $max_height, $newFileName );
		return $newFileName;
	}

	//--- ����ե���������
	function makeTempFile($fileName, $phpFileName) {
		$newFileNname = str_replace("'", "��", $fileName );
		$newFileNname = date("siU") . $newFileNname;
		copy( $phpFileName, $this->tempPath . $newFileNname );
		return $newFileNname;	
	}

	//--- �ե�����������¸DIR�ذ�ư
	function fileMove($fileName, $dirName) {
		if(copy( $this->tempPath . $fileName , $_SERVER['DOCUMENT_ROOT'] . $dirName . $fileName)) {
			unlink( $this->tempPath . $fileName );
		}
	}

	//---- ���DIR�Υե����������	
	function execDeleteTempFile() {
		chdir( $this->tempPath );
		$delFile = glob( "*.*" );
		if( is_array($delFile) ) foreach( $delFile as $val ) @unlink( $val );
	}

	//---- ����ե��������	
	function fileDelete($fileName, $dirName) {
		unlink( $_SERVER['DOCUMENT_ROOT'] . $dirName . $fileName );
	}
}
?>