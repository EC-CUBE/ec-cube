<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("../require.php");

class LC_Page{
	function LC_Page(){
		$this->tpl_mainpage = USER_PATH . 'templates/mypage/refusal.tpl';
		$this->tpl_title = "MY�ڡ���/����³��(���ϥڡ���)";
		$this->tpl_navi = USER_PATH . 'templates/mypage/navi.tpl';
		$this->tpl_mainno = 'mypage';
		$this->tpl_mypageno = 'refusal';
		//session_cache_limiter('private-no-expire');
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objCustomer = new SC_Customer();
$objQuery = new SC_Query();
$objSiteSess = new SC_SiteSession();

//������Ƚ��
if (!$objCustomer->isLoginSuccess()){
	sfDispSiteError(CUSTOMER_ERROR);
}else {
	//�ޥ��ڡ����ȥå׸ܵҾ���ɽ����
	$objPage->CustomerName1 = $objCustomer->getvalue('name01');
	$objPage->CustomerName2 = $objCustomer->getvalue('name02');
	$objPage->CustomerPoint = $objCustomer->getvalue('point');
}


// �쥤�����ȥǥ���������
$objPage = sfGetPageLayout($objPage, false, "mypage/index.php");

switch ($_POST['mode']){
	case 'confirm':
    
	$objPage->tpl_mainpage = USER_PATH . 'templates/mypage/refusal_confirm.tpl';
	$objPage->tpl_title = "MY�ڡ���/����³��(��ǧ�ڡ���)";
    
    // ��ǧ�ڡ������ͳ�������Ȥ���Ͽ
    $objSiteSess->setRegistFlag();
    // hidden��uniqid��������
    $objPage->tpl_uniqid = $objSiteSess->getUniqId();
    
	break;
	
	case 'complete':
    // ���������ܤ��ɤ���������å�
    lfIsValidMovement($objSiteSess);
    
	//������
	$objQuery->exec("UPDATE dtb_customer SET del_flg=1, update_date=now() WHERE customer_id=?", array($objCustomer->getValue('customer_id')));

	$objCustomer->EndSession();
	//��λ�ڡ�����
	header("Location: ./refusal_complete.php");
	exit;
}

$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

// ���������ܤ��ɤ���������å�
function lfIsValidMovement($objSiteSess) {
    // ��ǧ�ڡ�����������ܤ��ɤ���������å�
    sfIsPrePage($objSiteSess);
    
    // uniqid ��POST����Ƥ��뤫������å�
    $uniqid = $objSiteSess->getUniqId();
    if ( !empty($_POST['uniqid']) && ($_POST['uniqid'] === $uniqid) ) {
        return;
    } else {
        sfDispSiteError(PAGE_ERROR, $objSiteSess);
    }
}
?>