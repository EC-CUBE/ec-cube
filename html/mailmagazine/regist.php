<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** ɬ�����ꤹ�� **/
		$this->tpl_css = '/css/layout/mailmagazine/complete.css';		// �ᥤ��CSS�ѥ�
		/** ɬ�����ꤹ�� **/
		$this->tpl_mainpage = 'mailmagazine/complete.tpl';			// �ᥤ��ƥ�ץ졼��
		$this->tpl_title = '���ޥ���Ͽ�����';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

//GET�ǻ��ꤷ��id���б�����ǡ��������
$arrtmpdata = $objQuery->select("*", "dtb_customer_mail_temp", "temp_id = ?", array($_GET['temp_id']));

//������URL���Υ��顼����(count = 0 ������������)
$arrcount = $objQuery->count("dtb_customer_mail_temp","temp_id = ?", array($_GET['temp_id']));

//���ơ��֥�Υ᡼�륢�ɥ쥹����� $arremail
$arremail = $arrtmpdata[0]['email'];			
//���ơ��֥�Υե饰��$arrflag��(1:HTML 2:�ƥ����� 3:�ۿ����)
$arrflag = $arrtmpdata[0]['mail_flag'];
//�ܥơ��֥�ǡ��᡼�륢�ɥ쥹����Ͽ�����å�
$arrcnt = $objQuery->count("dtb_customer_mail" ,"email=?",array($arremail));
//�ܥơ��֥�Υե饰
$arrsel = $objQuery->select("*", "dtb_customer_mail", "email LIKE ?", array($arremail));
			
//������å����������
$mes1="";			
$mes2="";

//��̾�Υƥ�ץ졼�Ȥ��Ϥ���å�����
$ent_subject = sfMakeSubject('���ޥ�����Ͽ����λ���ޤ�����');
$stop_subject= sfMakeSubject('���ޥ��������λ���ޤ�����');

if ($arrcount == 0){
	$mes1 = "ǧ�ڤ˼��Ԥ��ޤ�����";
	$mes2 = "��ǧ�᡼���URL�򤪳Τ��᲼������";
}elseif ($arremail != "" && $arrtmpdata[0]['end_flag'] == 1 && $arrflag <= 2){
	$mes1 = "���˥��ޥ���Ͽ����Ƥ���᡼�륢�ɥ쥹�Ǥ���";
	$mes2 = "���Ƥ��ѹ��������ϡ��֥��ޥ���Ͽ������ץե������ꤪ�ꤤ�פ��ޤ���";
}elseif ($arremail != "" && $arrtmpdata[0]['end_flag'] == 1 && $arrflag == 3){
	$mes1 = "���˥��ޥ��������Ƥ���᡼�륢�ɥ쥹�Ǥ���";
	$mes2 = "���Ƥ��ѹ��������ϡ��֥��ޥ���Ͽ������ץե������ꤪ�ꤤ�פ��ޤ���";
}else{

//���ơ��֥�����ƥ����å�

 switch ($arrflag){
	case '1':
	if ($arrcnt == 0){
		$entry_sql = "INSERT INTO dtb_customer_mail (email,mail_flag) VALUES ('$arremail' , '1')";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' WHERE email = ?";
		$objQuery->exec($entry_sql);
		$objQuery->exec($flag_sql,array($arremail));
	}else{
		$change_sql= "UPDATE dtb_customer_mail SET mail_flag='1' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ? ";
		$objQuery->exec($change_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
	}
		$mes1.="���ޥ�����Ͽ����λ���ޤ�����";
		$mes2.="��λ�᡼������դ��ޤ����ΤǤ���ǧ����������";
		$objPage->tpl_mailtitle = "���ޥ�����Ͽ����λ���ޤ�����";
		$objPage->tpl_email = $arremail;
		$objPage->tpl_name = "��Ͽ";
		$objPage->tpl_kindname = "�ƥ����Ȣ�HTML";
		sfSendTplMail($arremail, $ent_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
	break;

	case '2':
	if ($arrcnt == 0){
		$entry_sql = "INSERT INTO dtb_customer_mail (email,mail_flag) VALUES ('$arremail' , '2')";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' WHERE email = ?";
		$objQuery->exec($entry_sql);
		$objQuery->exec($flag_sql,array($arremail));
	}else{
		$change_sql= "UPDATE dtb_customer_mail SET mail_flag='2' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ? ";
		$objQuery->exec($change_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
	}
		$objPage->tpl_mailtitle = "���ޥ�����Ͽ����λ���ޤ�����";
		$objPage->tpl_name = "��Ͽ";
		$objPage->tpl_email = $arremail;
		$objPage->tpl_kindname = "HTML���ƥ�����";
		$mes1.="���ޥ�����Ͽ����λ���ޤ�����";
		$mes2.="��λ�᡼������դ��ޤ����ΤǤ���ǧ����������";
		sfSendTplMail($arremail, $ent_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
	break;
	
	case '3':
	switch ($arrsel[0]['mail_flag']){
			case '1':
			$objPage->tpl_kindname = "HTML";
			break;
			case '2':
			$objPage->tpl_kindname = "�ƥ�����";
			break;
		}
		$stop_sql = "UPDATE dtb_customer_mail SET mail_flag='3' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ?";
		$objQuery->exec($stop_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
		$objPage->tpl_mailtitle = "���ޥ��������λ���ޤ�����";
		$objPage->tpl_name = "���";
		$objPage->tpl_email = $arremail;
		sfSendTplMail($arremail,$stop_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
		$mes1.="���ޥ��������λ���ޤ�����";
		$mes2.="��λ�᡼������դ��ޤ����ΤǤ���ǧ����������";
		break;
 }

}

$objPage->mes1 = $mes1;
$objPage->mes2 = $mes2;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>

