<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* �����ȥ��å����������饹 */
class SC_SiteSession {
	/* ���󥹥ȥ饯�� */
	function SC_SiteSession() {
		sfDomainSessionStart();
		// ���ڡ����Ǥ���Ͽ����Ƚ�������Ѥ�
		$_SESSION['site']['pre_regist_success'] = $_SESSION['site']['regist_success'];
		$_SESSION['site']['regist_success'] = false;
		$_SESSION['site']['pre_page'] = $_SESSION['site']['now_page'];
		$_SESSION['site']['now_page'] = $_SERVER['PHP_SELF'];
	}
	
	/* ���ڡ����������Ǥ��뤫��Ƚ�� */
	function isPrePage() {
		if($_SESSION['site']['pre_page'] != "" && $_SESSION['site']['now_page'] != "") {
			if($_SESSION['site']['pre_regist_success'] || $_SESSION['site']['pre_page'] == $_SESSION['site']['now_page']) {
				return true;
			}
		}
		return false;
	}
	
	function setNowPage($path) {
		$_SESSION['site']['now_page'] = $path;
	}
	
	/* �ͤμ��� */
	function getValue($keyname) {
		return $_SESSION['site'][$keyname];
	}
	
	/* ��ˡ���ID�μ��� */
	function getUniqId() {
		// ��ˡ���ID�����åȤ���Ƥ��ʤ����ϥ��åȤ��롣
		if(!isset($_SESSION['site']['uniqid']) || $_SESSION['site']['uniqid'] == "") {
			$this->setUniqId();
		}
		return $_SESSION['site']['uniqid'];
	}
	
	/* ��ˡ���ID�Υ��å� */
	function setUniqId() {
		// ͽ¬����ʤ��褦�˥�����ʸ�������Ϳ���롣
		$_SESSION['site']['uniqid'] = sfGetUniqRandomId();
	}
	
	/* ��ˡ���ID�Υ����å� */
	function checkUniqId() {
		if($_POST['uniqid'] != "") {
			if($_POST['uniqid'] != $_SESSION['site']['uniqid']) {
				return false;
			}
		}
		return true;
	}
	
	/* ��ˡ���ID�β�� */
	function unsetUniqId() {
		$_SESSION['site']['uniqid'] = "";
	}
	
	/* ��Ͽ������Ͽ */
	function setRegistFlag() {
		$_SESSION['site']['regist_success'] = true;
	}
}
?>