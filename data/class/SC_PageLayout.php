<?php
/*
 * Copyright ��� 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

class SC_PageLayout {
	
    var $arrPageData;		// �ڡ����ǡ�����Ǽ��
    var $arrPageList;		// �ڡ����ǡ�����Ǽ��
	
    // ���󥹥ȥ饯��
    function SC_PageLayout() {
		$this->arrPageList = $this->getPageData();
	}
    
	/**************************************************************************************************************
	 * �ؿ�̾	��getPageData
	 * ��������	���֥�å�������������
	 * ����1	��$where  ������ Where��ʸ
	 * ����2	��$arrVal ������ Where��ιʹ������
	 * �����	���֥�å�����
	 **************************************************************************************************************/
	function getPageData($where = '', $arrVal = ''){
		$objDBConn = new SC_DbConn;		// DB���֥�������
		$sql = "";						// �ǡ�������SQL������
		$arrRet = array();				// �ǡ���������
		
		// SQL����
		$sql .= " SELECT";
		$sql .= " page_id";				// �ڡ���ID
		$sql .= " ,page_name";			// ̾��
		$sql .= " ,url";				// URL
		$sql .= " ,php_dir";			// php��¸��ǥ��쥯�ȥ�
		$sql .= " ,tpl_dir";			// tpl��¸��ǥ�d�쥯�ȥ�
		$sql .= " ,filename";			// �ե�����̾��
		$sql .= " ,header_chk ";		// �إå�������FLG
		$sql .= " ,footer_chk ";		// �եå�������FLG
		$sql .= " ,author";				// author����
		$sql .= " ,description";		// description����
		$sql .= " ,keyword";			// keyword����
		$sql .= " ,update_url";			// ����URL
		$sql .= " ,create_date";		// �ǡ���������
		$sql .= " ,update_date";		// �ǡ���������
		$sql .= " FROM ";
		$sql .= "     dtb_pagelayout";
		
		// where��λ��꤬������ɲ�	
		if ($where != '') {
			$sql .= " WHERE " . $where;
		}
		
		$sql .= " ORDER BY 	page_id";
		
		$arrRet = $objDBConn->getAll($sql, $arrVal);
		
		$this->arrPageData = $arrRet;
		
		return $arrRet;
	}


}
?>