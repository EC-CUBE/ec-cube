<?php
$now_dir = realpath(dirname(__FILE__));
require_once($now_dir . "/../../../data/lib/slib.php");	
require_once($now_dir . "/../../../data/conf/core_os.php");
require_once($now_dir . "/../../../data/conf/conf_os.php");
require_once($now_dir . "/../../../data/class/SC_View.php");
require_once($now_dir . "/../../../data/class/SC_Query.php");
require_once($now_dir . "/../../../data/class/SC_CheckError.php");
require_once($now_dir . "/../../../data/class/SC_FormParam.php");
require_once($now_dir . "/../../../data/class/SC_Customer.php");
require_once($now_dir . "/../../../data/class/SC_Cookie.php");
require_once($now_dir . "/../../../data/module/Archive/Tar.php");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = 'test/iketest/test.tpl';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

rmdir("test");

$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(SITE_FRAME);		//�ƥ�ץ졼�Ȥν���

//-------------------------------------------------------------------------------------------------------

//����ե�����ˤ�ꡢ������ե�������񤭤���
function lfSetExtractFile($extract_file, $extract_top_file, $install_success) {
	//�ǥ��쥯�ȥ�Ǥʤ����
	if(!is_dir($extract_file)) {
		return false;
	}

	//�ǥ��쥯�ȥ�򳫤�
	if($handle = opendir($extract_file)) {
		//�ǥ��쥯�ȥ����Ȥ��ɤ߹���
		while($file = readdir($handle)) {
			//'.'��'..'�ե�����Ͻ���
			if($file != "." && $file != "..") {
				//�ǥ��쥯�ȥ�Ǥ���
				if(is_dir($extract_file . "/" . $file)) {
					//�Ƶ��ƤӽФ�
					lfSetExtractFile($extract_file . "/" . $file, $extract_top_file, $install_success);
				} else {
					//����ե�����Υѥ������
					$replace_file = ereg_replace("^" . $extract_top_file . "/", "", $extract_file);
					//�ե�����򥳥ԡ�(���)����
					if(!copy($extract_file . "/" . $file, ROOT_DIR . $replace_file . "/" . $file)) {
						$install_success = false;
					}
				}
			}
		}
		//�ǥ��쥯�ȥ���Ĥ���
		closedir($handle);
	} else {
		$install_success = false;
	}
	return $install_success;
	
}	

?>
