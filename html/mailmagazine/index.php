<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/mailmagazine/index.css';		// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'mailmagazine/index.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_page_category = 'mailmagazine';
		$this->tpl_title = '���ޥ���Ͽ�����';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

$entry_email = strtolower($_POST['entry']);			//��Ͽ�������᡼�륢�ɥ쥹�ե��������ϥե�������
$stop_email = strtolower($_POST['stop']);			//����᡼�륢�ɥ쥹�ե�����������
$checkbox = $_POST['kind'];							//��Ͽ���������ޥ����������å��ܥå���

$arrErr = lfErrorCheck();
$objPage->arrErr = $arrErr;

$entry_email_subject = sfMakeSubject('���ޥ�����Ͽ����λ���ޤ�����');
$stop_email_subject = sfMakeSubject('���ޥ����������λ���ޤ�����');

//��Ͽ��ʣ�����å��ʽ�ʣ�ʤ���0  ��ʣ����:1)
$ent_flag = $objQuery->count("dtb_customer_mail", "email = ? AND mail_flag=? " ,array($entry_email,$checkbox));

//������ǧ�ʹ���ɬ��:1��������ɬ��:2)
$update_flag = $objQuery->count("dtb_customer_mail", "email = ? AND NOT mail_flag = ? ",array($entry_email,$checkbox));

//���POST�ͤΥ᡼�륢�ɥ쥹��¸�ߥ����å�
$stop_email_flag = $objQuery->count("dtb_customer_mail", "email = ? ",array($stop_email));
//����Ͽ�ơ��֥�����å�
$ent_temp_flag = $objQuery->count("dtb_customer_mail_temp", "email = ? " ,array($entry_email));
$stop_temp_flag = $objQuery->count("dtb_customer_mail_temp", "email = ? " ,array($stop_email));
//�ܥơ��֥뤫����ϿPOST�ͤΥ᡼�륢�ɥ쥹�����
$arrRetEnt = $objQuery->select("*","dtb_customer_mail","email = ?",array($entry_email));
$arrEmailEnt = $arrRetEnt[0]['email'];

//��Ͽ����Ƥ��뤫�ɤ���
$email_flag = $objQuery->count("dtb_customer_mail","email = ?",array($entry_email));

//�ܥơ��֥뤫����POST�ͤΥ᡼�륢�ɥ쥹�����
$arrRetStop = $objQuery->select("*","dtb_customer_mail" , " email = ? ",array($stop_email));
$arrEmailStop = $arrRetStop[0]['email'];

//��λ��å������ν����
$mes1="";
$mes2="";

//������ID����		
$randomid = sfGetUniqRandomId();

foreach($_POST as $key => $val) {
	
	switch ($key) {
	case 'entry':
	if (count($arrErr) == ""){
		//��Ͽ���ʤ���С�����Ͽ�¹�
		if ($email_flag == 0){
			$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
			$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
				switch ($checkbox){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;
					case '2':
					$objPage->tpl_kindname = "�ƥ�����";
					break;
				}
			$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
			$objPage->tpl_name = "��Ͽ";
			$objPage->tpl_email = $entry_email;
			$objPage->tpl_mailtitle = "���ޥ�����Ͽ����λ���ޤ�����";
			sfSendTplMail($entry_email, $entry_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
			
			if ($ent_temp_flag == 0){				
			$sql = "INSERT INTO dtb_customer_mail_temp";
			$sql.= "(email,mail_flag,temp_id,end_flag) VALUES ('$entry_email' , '$checkbox' ,'$randomid','0')";
			$objQuery->exec($sql);
			}else{
			$sql = "UPDATE dtb_customer_mail_temp SET temp_id = '$randomid' , mail_flag='$checkbox' , end_flag='0'";
			$sql.= "WHERE email = ?";
			$objQuery->exec($sql,array($entry_email));
			}
			//������ξ��
		}elseif (($email_flag == 1) && ($arrRetEnt[0]['mail_flag'] >= 4)){
			$objPage->arrErr['entry'] = "���������Ͽ����λ���Ƥ��ޤ�������ܲ����Ͽ��Ѥޤ��Ƥ���������";
		
		//������ǡ�������Ͽ����Ƥ���᡼�륢�ɥ쥹�ڤ��ۿ������ξ��
		}elseif ($ent_flag == 1){
			$objPage->arrErr['entry']= "����������Ͽ����Ƥ���᡼�륢�ɥ쥹�ڤ��ۿ������Ǥ���";
			
		//������ǡ����˥��ޥ��ۿ����ѹ��������Ȥ����ꡢ�ۿ���������ŤǤʤ���С��ۿ������ѹ������ۿ��¹ԡʲ��ơ��֥빹����
		}else{
			if (($email_flag == 1) && ($update_flag == 1)  && ($arrRetEnt[0]['mail_flag'] <= 3) && ($ent_temp_flag != 0)){
			$sql = "UPDATE dtb_customer_mail_temp SET temp_id = '$randomid' , mail_flag='$checkbox' , end_flag='0'";
			$sql.= "WHERE email = ?";
			$objQuery->exec($sql,array($entry_email));
			}else{
				//  ������ǡ����٤���ޥ��ۿ����ѹ��������Ȥ��ʤ����ۿ���������ŤǤʤ���С��ۿ������ѹ������ۿ��¹ԡʲ��ơ��֥���Ͽ��
				if (($email_flag ==1) && ($update_flag == 1) && ($arrRetEnt[0]['mail_flag'] <= 3) && ($ent_temp_flag == 0)){
					$sql = "INSERT INTO dtb_customer_mail_temp";
					$sql.= "(email,mail_flag,temp_id,end_flag) VALUES ('$entry_email' , '$checkbox' ,'$randomid','0')";
					$objQuery->exec($sql);
				}
			}
			$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
			$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
			
			switch($arrRetEnt[0]['mail_flag']){
				
			case '1':
			$objPage->tpl_kindname = "HTML���ƥ�����";
			break;
			
			case '2':
			$objPage->tpl_kindname = "�ƥ����Ȣ�HTML";
			
			break;
			
			case '3':
				switch($checkbox){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;	
					case '2':
					$objPage->tpl_kindname = "�ƥ�����";
					break;
				}	
			break;
			
			}
			$objPage->tpl_email = $entry_email;
			$objPage->tpl_name = "��Ͽ";
			$objPage->tpl_mailtitle = "���ޥ�����Ͽ����λ���ޤ�����";
			$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
			sfSendTplMail($entry_email, $entry_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
		}
			$mes1.="���ޥ�����Ͽ����λ���ޤ�����";
			$mes2.="��ǧ�᡼������դ��ޤ����ΤǤ���ǧ����������";
	}
		break;
		
	case 'stop':
	if (count($arrErr) == ""){									
		if ($stop_email_flag == 1){					//����Ͽ����Ƥ����
			if ($arrRetStop[0]['mail_flag'] <= 2){
				switch ($stop_temp_flag){
					case '0':	//���ܲ����Ͽ�塢���ƥ��ޥ��ۿ���ߤ��˾�����Ȥ�
					$sql= "INSERT INTO dtb_customer_mail_temp";
					$sql.="(email,mail_flag,temp_id,end_flag) VALUES ('$stop_email','3','$randomid','0')";
					$objQuery->exec($sql);
					break;
					
					case '1':	// ���˥��ޥ�������ѹ��������Ȥ�����Ȥ�
					$sql = "UPDATE dtb_customer_mail_temp SET temp_id='$randomid' , mail_flag='3' , end_flag='0' ";
					$sql.= "WHERE email = ? ";
					$objQuery->exec($sql,array($stop_email));
					break;
				}
						//���
				$mes1.="���ޥ����������λ���ޤ�����";
				$mes2.="��ǧ�᡼������դ��ޤ����ΤǤ���ǧ����������";
				$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
				$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
				switch ($arrRetStop[0]['mail_flag']){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;
					case '2':
					$objPage->tpl_kindname = "�ƥ�����";
					break;
				}
				$objPage->tpl_mailtitle = "���ޥ����������λ���ޤ�����";
				$objPage->tpl_email = $stop_email;
				$objPage->tpl_name = "���";
				$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
				sfSendTplMail($stop_email, $stop_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
			}elseif ($arrRetStop[0]['mail_flag'] >= 4){
				$objPage->arrErr['stop']= "���������Ͽ����λ���Ƥ��ޤ�������ܲ����Ͽ��Ѥޤ��Ƥ���������";
			}else{
				$objPage->arrErr['stop']= "���������ۿ���ߤ���Ƥ���᡼�륢�ɥ쥹�Ǥ���";
			}
		}else{
				$objPage->arrErr['stop']= "������Ͽ����Ƥ��ʤ��᡼�륢�ɥ쥹�Ǥ���";
		}
	}
		break;
}

}

$objPage->mes1 = $mes1;
$objPage->mes2 = $mes2;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);					

//-----------------------------------------------------------------------------------------------------------------------------------


//���顼�����å�

function lfErrorCheck() {
	$objErr = new SC_CheckError();
		switch ($_POST['mode']) {
			case 'entry':
			$objErr->doFunc(array("��Ͽ�᡼�륢�ɥ쥹", "entry", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"));
			break;
	
			case 'stop':
			$objErr->doFunc(array("�ۿ���ߥ᡼�륢�ɥ쥹", "stop", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"));
			break;
		}
	return $objErr->arrErr;
}

/* ����ʸ������Ѵ� */
function lfConvertParam($array) {
	/*
	 *	ʸ������Ѵ�
	 *	K :  ��Ⱦ��(�ʎݎ���)�Ҳ�̾�פ�������Ҳ�̾�פ��Ѵ�
	 *	C :  �����ѤҤ鲾̾�פ�����Ѥ�����̾�פ��Ѵ�
	 *	V :  �����դ���ʸ�����ʸ�����Ѵ���"K","H"�ȶ��˻��Ѥ��ޤ�	
	 *	n :  �����ѡ׿������Ⱦ��(�ʎݎ���)�פ��Ѵ�
	 */
	// ��ʪ���ܾ���
	
	// ���ݥåȾ���
	$arrConvList['name'] = "KVa";
	$arrConvList['main_list_comment'] = "KVa";
	$arrConvList['price01'] = "n";
	$arrConvList['price02'] = "n";
	$arrConvList['stock'] = "n";
	$arrConvList['sale_limit'] = "n";
	$arrConvList['point_rate'] = "n";
	$arrConvList['product_code'] = "KVna";
	$arrConvList['deliv_fee'] = "n";

	// ʸ���Ѵ�
	foreach ($arrConvList as $key => $val) {
		// POST����Ƥ����ͤΤ��Ѵ����롣
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	
	global $arrSTATUS;
	$array['product_flag'] = sfMergeCheckBoxes($array['product_flag'], count($arrSTATUS));
	
	return $array;
}
?>
