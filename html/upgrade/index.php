<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

require_once("../require.php");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = 'upgrade/index.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();

$con = ftp_connect("localhost");
$res = ftp_login($con, "osuser", "password");
if($con != false && $res != false) {
	//�ե�����Υ��������
	if($_POST['filename'] != "") {
		
		$download_file = DATA_PATH . "module/upload/" . $_POST['filename'];
		
		if(ftp_get($con, $download_file, $_POST['filename'], FTP_BINARY)) {
			ftp_quit($con);
			//�ե������ͭ���ѹ�
			echo "�ե�����Υ�������ɤ��������ޤ�����";
			sfPrintR(exec("tar zxvf " . $download_file . " ./", $arrRes));
		} else {
			echo '�ե�����Υ�������ɤ˼��Ԥ��ޤ�����';
		}
	}
	
	//�ǥ��쥯�ȥ�������ƤΥե���������
	$arrRet = ftp_nlist($con, ".");
	$i = 0;
	//ɬ�פʥե������������
	foreach($arrRet as $val) {
		if(!ereg("^\.|^\..", $val)) {
			$arrFile[$i]['filename'] = $val;
			$arrFile[$i]['date'] = date("Yǯm��d��", ftp_mdtm($con, $val));
			$arrFile[$i]['filesize'] = number_format(ftp_size($con, $val))."Byte";
			$i++;
		}
	}
	$objPage->arrFile = $arrFile;
}

$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(SITE_FRAME);		//�ƥ�ץ졼�Ȥν���

//-------------------------------------------------------------------------------------------------------



?>
