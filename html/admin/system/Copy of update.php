<?php
$now_dir = realpath(dirname(__FILE__));
require_once($now_dir . "/../../../data/lib/slib.php");	
require_once($now_dir . "/../../../data/class/SC_View.php");
require_once($now_dir . "/../../../data/class/SC_Query.php");
require_once($now_dir . "/../../../data/class/SC_CheckError.php");
require_once($now_dir . "/../../../data/class/SC_FormParam.php");
require_once($now_dir . "/../../../data/class/SC_Customer.php");
require_once($now_dir . "/../../../data/class/SC_Cookie.php");
//require_once($now_dir . "/../../../data/module/Archive/Tar.php");

//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = 'system/update.tpl';
		$this->tpl_subnavi = 'system/subnavi.tpl';
		$this->tpl_mainno = 'system';		
		$this->tpl_subno = 'update';
		$this->tpl_subtitle = '���åץǡ��ȴ���';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();
$objQuery = new SC_Query();

/*
//���åץǡ��Ⱦ�������
$fp = fopen("csv.php", "r");
$i = 0;
$j = 0;
$k = 0;
//�ե������������
if($fp) {
	//CSV���������˳�Ǽ
	while(!feof($fp)) {
		$line = fgetcsv($fp, 40000);
		$arrRet[$i]['update_id'] = $line[0];		//���åץǡ��ȵ�ǽID
		$arrRet[$i]['file_name'] = $line[1];		//�ե�����̾
		$arrRet[$i]['func_name'] = $line[2];		//��ǽ̾
		$arrRet[$i]['func_explain'] = $line[3];		//��ǽ����
		$arrRet[$i]['version'] = $line[4];			//�С������
		$arrRet[$i]['update_date'] = $line[5];		//�ǽ���������
		if($i >= 1) {
			$arrval = array($arrRet[$i]['update_id'], $arrRet[$i]['version']);
			//���˥��󥹥ȡ��뤵��Ƥ��뵡ǽ���ɤ��������å�
			$arrGet = $objQuery->select("*", "dtb_update_list", "update_id = ? AND version = ? ", $arrval);
			//���󥹥ȡ���Ѥ�
			if(count($arrGet) > 0) {
				$arrInstalled[$k] = $arrGet[0];
				$k++;
			} else {
				$arrUpList[$j] = $arrRet[$i];
				$j++;
			}
		}
		$i++;
	}
	//���󥹥ȡ���Ѥ�
	$objPage->arrInstalled = $arrInstalled;
	//���åץǡ��Ȳ�ǽ��ǽ����
	$objPage->arrUpList = $arrUpList;
}

//��ǧ����
if($_POST['mode'] == 'confirm' && sfIsInt($_POST['update_id'])) {
	//�ƥ�ץ졼�Ȼ���
	$objPage->tpl_mainpage = "system/update_confirm.tpl";
	//���åץǡ��ȵ�ǽID
	$update_id = $_POST['update_id'];
	//�ե�����̾
	$comp_file = trim($arrRet[$update_id]['file_name']);
	//FTP��³
	$con = ftp_connect("localhost");
	if($con != false) {
		//FTP����������
		if(ftp_login($con, "osuser", "password")) {
			//���������¸����ѥ�
			$objPage->local_save_dir = ROOT_DIR . "data/install/";
			//���������¸����ե�����̾��ѥ�����
			$local_file = $objPage->local_save_dir . $comp_file;
			//FTP�������������
			if(ftp_get($con, $local_file, $comp_file, FTP_BINARY)) {
				//FTP��³����
				ftp_quit($con);
				//���ߤΥǥ��쥯�ȥ�򡢥��󥹥ȡ���ե�����˰ܹ�
				$current_dir = getcwd();
				//�ǥ��쥯�ȥ�ΰܹ�
				chdir(ROOT_DIR . "data/install/");
				//���������ִ������饹
				$objTar = new Archive_Tar($comp_file);
				//�����Υե�����̾�μ���(��ĥ�Ҥ��������ե�����)
				$extract_file = ereg_replace("\.tar\.gz|\.tar|\.gz|\.tgz", "", $comp_file);
				//���������֥ե�����Υꥹ�Ȥ�ɽ��
				$arrRet = $objTar->listcontent();
				foreach($arrRet as $data) {
					//PHP�ե�����⤷����tpl�ե�����Ǥ���
					if(ereg("\.php$|\.tpl$", $data['filename'])) {
						$main_file = ereg_replace($extract_file . "/", "", $data['filename']);
						$arrFile[]['main_file'] = ROOT_DIR . $main_file; 
					}
					//sql�ե�����Ǥ���
					if(ereg("\.sql$", $data['filename'])) {
						//�ե�����̾����DB̾���������
						$sql_file = ereg_replace($extract_file . "/", "", $data['filename']);
						$db_name = ereg_replace("\.sql$", "", $sql_file);
						$arrFile[]['sql_file'] = $db_name;
					}
				}
				$objPage->arrFile = $arrFile;
				//���̥ե�����κ��
				unlink($comp_file);
				//���ߤΥǥ��쥯�ȥ�򸵤��᤹
				chdir($current_dir);
			} else {
				//FTP��������ɥ��顼
				sfDispSiteError(FTP_DOWNLOAD_ERROR);
			}
		} else {
			//FTP�����󥨥顼
			sfDispSiteError(FTP_LOGIN_ERROR);
		}
	} else {
		//FTP��³���顼
		sfDispSiteError(FTP_CONNECT_ERROR);
	}
}



//���åץǡ��ȵ�ǽ�Υ��󥹥ȡ���
if($_POST['mode'] == 'install' && sfIsInt($_POST['update_id'])) {
	//���åץǡ��ȵ�ǽID
	$update_id = $_POST['update_id'];
	//�ե�����̾
	$comp_file = trim($arrRet[$update_id]['file_name']);
	//FTP��³
	$con = ftp_connect("localhost");
	if($con != false) {
		//FTP����������
		if(ftp_login($con, "osuser", "password")) {
			//���������¸����ѥ������
			$objPage->local_save_dir = ROOT_DIR . "data/install/";
			//���������¸����ե�����̾��ѥ�����
			$local_file = ROOT_DIR . "data/install/" . $comp_file;
			//FTP�������������
			if(ftp_get($con, $local_file, $comp_file, FTP_BINARY)) {
				//FTP��³����
				ftp_quit($con);
				//���ߤΥǥ��쥯�ȥ�򡢥��󥹥ȡ���ե�����˰ܹ�
				$current_dir = getcwd();
				//�ǥ��쥯�ȥ�ΰܹ�
				chdir(ROOT_DIR . "data/install/");
				//���������ִ������饹
				$objTar = new Archive_Tar($comp_file, true);
				//���顼�ξܺ٤��֤�
				//$objTar->setErrorHandling(PEAR_ERROR_PRINT);
				//��������
				if($objTar->extract("./")) {
					//���̥ե��������
					unlink($comp_file);
					//�����Υե�����̾�μ���(��ĥ�Ҥ��������ե�����)
					$extract_file = ereg_replace("\.tar\.gz|\.tar|\.gz|\.tgz", "", $comp_file);
					//�����οƥե�������ѿ����Ϥ�
					$extract_top_file = $extract_file;
					//���󥹥ȡ��������Υե饰
					$install_flag = true;
					//����ե�����ˤ�ꡢ������ե�������񤭤���
					$flag = lfSetExtractFile($extract_file, $extract_top_file, $install_flag);
					//�ե�����ξ�񤭤�����
					if($flag) {
						//��Ǽ���륭�����ͤ����
						$sqlval['update_id'] = $update_id;
						$sqlval['func_name'] = $arrRet[$update_id]['func_name'];
						$sqlval['func_explain'] = $arrRet[$update_id]['func_explain'];
						$sqlval['version'] = $arrRet[$update_id]['version'];
						$sqlval['update_date'] = 'now()';
						
						//���åץǡ��ȴ����ơ��֥�˾�����Ǽ
						$objQuery->insert("dtb_update_list", $sqlval);
						//�ե�����κ��
						system("rm -rf ". $extract_file);
						//����򹹿�
						sfReload();
					} else {
						sfDispSiteError(WRITE_FILE_ERROR);
					}

				} else {
					//�ե�������२�顼
					sfDispSiteError(EXTRACT_ERROR);
				}
				//���ߤΥǥ��쥯�ȥ�򸵤��᤹
				chdir($current_dir);
				
			} else {
				//FTP��������ɥ��顼
				sfDispSiteError(FTP_DOWNLOAD_ERROR);
			}
		} else {
			//FTP�����󥨥顼
			sfDispSiteError(FTP_LOGIN_ERROR);
		}
	} else {
		//FTP��³���顼
		sfDispSiteError(FTP_CONNECT_ERROR);
	}
}
*/
$objView->assignobj($objPage);		//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display(MAIN_FRAME);		//�ƥ�ץ졼�Ȥν���

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
					//�ե�����Ǥ���
					if(is_file($extract_file . "/" . $file)) {
						//sql�ե�����Ǥ���
						if(ereg("\.sql$", $file)) {
							//�ե�������������
							require_once($extract_file . "/" . $file);
							//�����꡼�¹ԥ��饹
							$objQuery = new SC_Query;
							//�����꡼�μ¹�
							$objQuery->query($sql);
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
