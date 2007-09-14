<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/* ���å����������饹 */
class SC_Session {
	var $login_id;		// ������桼��̾
	var $authority;		// �桼������
	var $cert;			// ǧ��ʸ����(ǧ��������Ƚ��˻���)
	var $sid;			// ���å����ID
	var $member_id;		// ������桼���μ祭��
    var $uniqid;         // �ڡ������ܤ������������å��˻���
    
	/* ���󥹥ȥ饯�� */
	function SC_Session() {
		// ���å���󳫻�
		sfDomainSessionStart();

		// ���å����������¸
		if(isset($_SESSION['cert'])) {
			$this->sid = session_id();
			$this->cert = $_SESSION['cert'];
			$this->login_id  = $_SESSION['login_id'];
			$this->authority = $_SESSION['authority'];	// ������:0, ����:1, ����:2
			$this->member_id = $_SESSION['member_id'];
            $this->uniqid    = $_SESSION['uniq_id'];
            
			// ���˵�Ͽ����
			gfPrintLog("access : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
		} else {
			// ���˵�Ͽ����
			gfPrintLog("access error.");
		}
	}
	/* ǧ��������Ƚ�� */
	function IsSuccess() {
		global $arrPERMISSION;
		if($this->cert == CERT_STRING) {
			if(isset($arrPERMISSION[$_SERVER['PHP_SELF']])) {
				// ���ͤ���ʬ�θ��°ʾ�Τ�ΤǤʤ��ȥ��������Ǥ��ʤ���
				if($arrPERMISSION[$_SERVER['PHP_SELF']] < $this->authority) {			
					return AUTH_ERROR;
				} 
			}
			return SUCCESS;
		}
		
		return ACCESS_ERROR;
	}
	
	/* ���å����ν񤭹��� */
	function SetSession($key, $val) {
		$_SESSION[$key] = $val;
	}
	
	/* ���å������ɤ߹��� */
	function GetSession($key) {
		return $_SESSION[$key];
	}
	
	/* ���å����ID�μ��� */
	function GetSID() {
		return $this->sid;
	}
	
    /** ��ˡ���ID�μ��� **/ 
    function getUniqId() {
        // ��ˡ���ID�����åȤ���Ƥ��ʤ����ϥ��åȤ��롣
        if( empty($_SESSION['uniqid']) ) {
            $this->setUniqId();
        }
        return $this->GetSession('uniqid');
    }
    
    /** ��ˡ���ID�Υ��å� **/ 
    function setUniqId() {
        // ͽ¬����ʤ��褦�˥�����ʸ�������Ϳ���롣
        $this->SetSession('uniqid', sfGetUniqRandomId());
    }
    
	/* ���å������˴� */
	function EndSession() {
		// �ǥե���Ȥϡ���PHPSESSID��
		$sname = session_name();
		// ���å�����ѿ������Ʋ������
		$_SESSION = array();
		// ���å��������Ǥ���ˤϥ��å���󥯥å����������롣
		// Note: ���å�����������Ǥʤ����å������˲����롣
		if (isset($_COOKIE[$sname])) {
			setcookie($sname, '', time()-42000, '/');
		}
		// �ǽ�Ū�ˡ����å������˲�����
		session_destroy();
		// ���˵�Ͽ����
		gfPrintLog("logout : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
	}
	
	// ��Ϣ���å����Τ��˴����롣
	function logout() {
		unset($_SESSION['cert']);
		unset($_SESSION['login_id']);
		unset($_SESSION['authority']);
		unset($_SESSION['member_id']);
        unset($_SESSION['uniqid']);
		// ���˵�Ͽ����
		gfPrintLog("logout : user=".$this->login_id." auth=".$this->authority." sid=".$this->sid);
	}
}
?>