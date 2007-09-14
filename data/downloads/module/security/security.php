<?php
/**
 * 
 * @copyright	2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 * @version	CVS: $Id: ebis_tag.php,v 1.0 2006/10/26 04:02:40 naka Exp $
 * @link		http://www.lockon.co.jp/
 *
 */
 
//�ڡ����������饹
class LC_Page {
	//���󥹥ȥ饯��
	function LC_Page() {
		//�ᥤ��ƥ�ץ졼�Ȥλ���
		$this->tpl_mainpage = MODULE_PATH . 'security/security.tpl';
		$this->tpl_subtitle = '�������ƥ������å�';
	}
}

$objPage = new LC_Page();
$objView = new SC_AdminView();

switch($_POST['mode']) {
case 'edit':
    $inst_inc = DATA_PATH . 'install.php';
    // install.php�α���
    $hidden_inc = MODULE_PATH . 'security/install_inc.php';
    if(sfIsNormalInstallInc()) {
        if(copy($inst_inc, $hidden_inc)) {
            if(file_exists($hidden_inc)) {
		        $require = "<?php\n".
		        		   "    require_once('$hidden_inc');\n".
		        		   "?>";
		        if($fp = fopen($inst_inc,"w")) {
					fwrite($fp, $require);
					fclose($fp);
		        }
            }
        }
	}
	break;
default:
    break;
}

$arrList[] = sfCheckOpenData();
$arrList[] = sfCheckInstall();
$arrList[] = sfCheckIDPass('admin', 'password');
$arrList[] = sfCheckInstallInc();

$objPage->arrList = $arrList;

$objView->assignobj($objPage);					//�ѿ���ƥ�ץ졼�Ȥ˥������󤹤�
$objView->display($objPage->tpl_mainpage);		//�ƥ�ץ졼�Ȥν���
//-------------------------------------------------------------------------------------------------------
// ����ե�����(data)�Υѥ��������ѥ��Ǥʤ�����ǧ����
function sfCheckOpenData() {
    // �ɥ�����ȥ롼�ȤΥѥ����¬���롣
    $doc_root = ereg_replace(URL_DIR . "$","/",HTML_PATH);
    $data_path = realpath(DATA_PATH);
    
    // data�Υѥ����ɥ�����ȥ롼�Ȱʲ��ˤ��뤫Ƚ��
    if(ereg("^".$doc_root, $data_path)) {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "����ե����뤬����������Ƥ����ǽ��������ޤ���<br>";
        $arrResult['detail'].= "/data/�ǥ��쥯�ȥ�ϡ�������Υѥ������֤��Ʋ�������";
    } else {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "����ե�����ϡ������ѥ��۲���¸�ߤ��ޤ���";        
    }
    
    $arrResult['title'] = "����ե��������¸�ѥ�";
    return $arrResult;
}

// ���󥹥ȡ���ե����뤬¸�ߤ��뤫��ǧ����
function sfCheckInstall() {
    // ���󥹥ȡ���ե������¸�ߥ����å�
    $inst_path = HTML_PATH . "install/index.php";
    
    if(file_exists($inst_path)) {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "/install/index.php�ϡ����󥹥ȡ��봰λ��˥ե�����������Ƥ���������";            
    } else {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "/install/index.php�ϡ����Ĥ���ޤ���Ǥ�����";    
    }
    
    $arrResult['title'] = "���󥹥ȡ���ե�����Υ����å�";
    return $arrResult;
}

// �����ԥ桼����ID/�ѥ���ɥ����å�
function sfCheckIDPass($user, $password) {
    $objQuery = new SC_Query();
    $sql = "SELECT password FROM dtb_member WHERE login_id = ? AND del_flg = 0";
	// DB����Ź沽�ѥ���ɤ�������롣
	$arrRet = $objQuery->getAll($sql, array($user));
	// �桼�����ϥѥ���ɤ�Ƚ��
	$ret = sha1($password . ":" . AUTH_MAGIC);
    
    if($ret == $arrRet[0]['password']) {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "���˿�¬�Τ��䤹��������ID�ȤʤäƤ��ޤ����Ŀ;���ϳ�̤δ���������ޤ���";       
    } else {
        if(count($arrRet) > 0) {
	        $arrResult['result'] = "��";
	        $arrResult['detail'] = "������̾�ˡ�admin�פ����Ѥ��ʤ��褦�ˤ��Ʋ�������";               
        } else {
            $arrResult['result'] = "��";
            $arrResult['detail'] = "�ȼ���ID���ѥ���ɤ����ꤵ��Ƥ���褦�Ǥ���";               
        }
    }
    
    $arrResult['title'] = "ID/�ѥ���ɤΥ����å�";
    return $arrResult;
}


// install.php�Υե����������å�����
function sfCheckInstallInc() {
    // install.php�����ø�Τ�Τ�Ƚ�ꤹ��
    if(sfIsNormalInstallInc()) {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "install.php���ñ��ɽ���Ǥ��ʤ����뤳�Ȥ��Ǥ��ޤ������Ƥ��ä��ޤ�����";
        $arrResult['detail'].= "<input type='submit' value='���ä���'>";        
    } else {
        $arrResult['result'] = "��";
        $arrResult['detail'] = "install.php�α����к����Ȥ��Ƥ��ޤ���";                       
    }
    $arrResult['title'] = "install.php�β����������å�";
    return $arrResult;
}

// install.php�����ø�Τ�Τ�Ƚ�ꤹ��
function sfIsNormalInstallInc() {
    // install.php�Υѥ����������
    $inst_inc = DATA_PATH . 'install.php';
    if(file_exists($inst_inc)) {
        if($fp = fopen($inst_inc, "r")) {
            $data = fread($fp, filesize($inst_inc));
            fclose($fp);
        }
        if(ereg("DB_PASSWORD", $data)) {
            return true;
        }
    }
    return false;
}

?>