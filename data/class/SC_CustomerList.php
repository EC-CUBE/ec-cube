<?php

/*  [̾��] SC_CustomerList
 *  [����] ��������ѥ��饹
 */
class SC_CustomerList extends SC_SelectSql {

	var $arrColumnCSV;
	
	function SC_CustomerList($array, $mode = '') {
		parent::SC_SelectSql($array);
		
		if($mode == "") {
			// �������Ͽ����Ǻ�����Ƥ��ʤ����
			$this->setWhere("status = 2 AND delete = 0 ");		
			// ��Ͽ���򼨤������
			$regdate_col = 'dtb_customer.update_date';
		}
		if($mode == "customer") {
			// �����ԥڡ����ܵҸ����ξ�精��Ͽ����⸡��
			$this->setWhere( "(status = 1 OR status = 2) AND delete = 0 ");		
			// ��Ͽ���򼨤������
			$regdate_col = 'dtb_customer.update_date';
		}
		
		// �᡼��ޥ�����ξ��		
		if($mode == "magazine") {
			$this->setWhere("(delete = 0 OR delete IS NULL)");
			
			if(is_array($this->arrSql['customer'])) {
				$tmp_where = "";
				foreach($this->arrSql['customer'] as $val) {
					if($tmp_where != "") {
						$tmp_where.= " OR ";
					}					
					switch($val) {
					// ���
					case '1':
						$tmp_where.= "status = 2";
						break;
					// ���ޥ���Ͽ
					case '2':
						// dtb_customer_mail�ˤΤ���Ͽ����Ƥ���쥳���ɤ����
						$tmp_where.= "customer_id IS NULL ";
						break;
					// CSV��Ͽ
					case '3':
						// dtb_customer_mail�ˤΤ���Ͽ����Ƥ���쥳���ɤ����
						$tmp_where.= "customer_id IS NULL";
						break;
					default:
						$tmp_where = "";
						break;
					}
				}
				if($tmp_where != "") {
					$tmp_where = "(" . $tmp_where . ")";
				}
				$this->setWhere($tmp_where);
			}
			// ��Ͽ���򼨤������
			$regdate_col = 'dtb_customer_mail.create_date';
		}
				
		// �ܵ�ID
		if ( strlen($this->arrSql['search_customer_id']) > 0 ) {
			$this->setWhere( "customer_id =  ?" );
			$this->arrVal[] = $this->arrSql['search_customer_id'];
		}
		
		// ̾��
		if ( strlen($this->arrSql['search_name']) > 0 ) {
			$this->setWhere("(name01 || name02 LIKE ?)" );
			$searchName = $this->addSearchStr($this->arrSql['search_name']);
			$this->arrVal[] = ereg_replace("[ ��]+","",$searchName);
		}

		//��̾���ʥ��ʡ�
		if ( strlen($this->arrSql['search_kana']) > 0 ) {
			$this->setWhere("(kana01 || kana02 LIKE ?)");
			$searchKana = $this->addSearchStr($this->arrSql['search_kana']);
			$this->arrVal[] = ereg_replace("[ ��]+","",$searchKana);
		}
		
		//����ƻ�ܸ�
		if ( strlen($this->arrSql['search_pref']) > 0 ) {
			$this->setWhere( "pref = ?" );
			$this->arrVal[] = $this->arrSql['search_pref'];
		}

		//�������ֹ�
		if ( is_numeric( $this->arrSql['search_tel'] ) ) {
			$this->setWhere( "(tel01 || tel02 || tel03 LIKE ?)" );
			$searchTel = $this->addSearchStr($this->arrSql['search_tel']);
			$this->arrVal[] = ereg_replace("-", "", $searchTel);
		}
		
		//������
		if ( is_array( $this->arrSql['search_sex'] ) ){
			$arrSexVal = $this->setItemTerm( $this->arrSql['search_sex'] ,"sex" );
			foreach ($arrSexVal as $data) {
				$this->arrVal[] = $data;
			}
		}

		//������
		if ( is_array( $this->arrSql['search_job'] ) ){
			if ( in_array("����", $this->arrSql['search_job'] ) ) {
				$arrJobVal = $this->setItemTermWithNull( $this->arrSql['search_job'] ,"job" );
			} else {
				$arrJobVal = $this->setItemTerm( $this->arrSql['search_job'] ,"job" );
			}
			if (is_array($arrJobVal)) {
				foreach ($arrJobVal as $data) {
					$this->arrVal[] = $data;
				}
			}
		}

		//��E-MAIL
		if ( strlen($this->arrSql['search_email']) > 0 ) {
			$this->setWhere( "email ILIKE ? " );
			$searchEmail = $this->addSearchStr($this->arrSql['search_email']);
			$this->arrVal[] = $searchEmail;
		}

		//��HTML-mail
		if ( $mode == 'magazine' ){
			if ( strlen($this->arrSql['search_htmlmail']) > 0 ) {
				$this->setWhere( " mail_flag = ? ");
				$this->arrVal[] = $this->arrSql['search_htmlmail'];
			} else {
				$this->setWhere( " (mail_flag = 1 or mail_flag = 2) ");
			}
		}
		
		// ������ۻ���
		if( is_numeric( $this->arrSql["buy_total_from"] ) || is_numeric( $this->arrSql["buy_total_to"] ) ) {
			$arrBuyTotal = $this->selectRange($this->arrSql["buy_total_from"], $this->arrSql["buy_total_to"], "buy_total");
			foreach ($arrBuyTotal as $data1) {
				$this->arrVal[] = $data1;
			}
		}

		// �����������
		if( is_numeric( $this->arrSql["buy_times_from"] ) || is_numeric( $this->arrSql["buy_times_to"] ) ) {
			$arrBuyTimes = $this->selectRange($this->arrSql["buy_times_from"], $this->arrSql["buy_times_to"], "buy_times");
			foreach ($arrBuyTimes as $data2) {
				$this->arrVal[] = $data2;
			}
		}
		
		// ���������ֻ���
		if ( (strlen($this->arrSql['search_b_start_year']) > 0 && strlen($this->arrSql['search_b_start_month']) > 0 && strlen($this->arrSql['search_b_start_day']) > 0) ||
			  strlen($this->arrSql['search_b_end_year']) > 0 && strlen($this->arrSql['search_b_end_month']) > 0 && strlen($this->arrSql['search_b_end_day']) > 0) {

			$arrBirth = $this->selectTermRange($this->arrSql['search_b_start_year'], $this->arrSql['search_b_start_month'], $this->arrSql['search_b_start_day']
					  , $this->arrSql['search_b_end_year'], $this->arrSql['search_b_end_month'], $this->arrSql['search_b_end_day'], "birth");
			if (is_array($arrBirth)) {
				foreach ($arrBirth as $data3) {
					$this->arrVal[] = $data3;
				}
			}
		}

		// ������θ���
		if (is_numeric($this->arrSql["birth_month"])) {
			$this->setWhere(" EXTRACT(month from birth) = ?");  
			$this->arrVal[] = $this->arrSql["birth_month"];
		}

		// ��Ͽ���ֻ���
		if ( (strlen($this->arrSql['search_start_year']) > 0 && strlen($this->arrSql['search_start_month']) > 0 && strlen($this->arrSql['search_start_day']) > 0 ) || 
				(strlen($this->arrSql['search_end_year']) > 0 && strlen($this->arrSql['search_end_month']) >0 && strlen($this->arrSql['search_end_day']) > 0) ) {

			$arrRegistTime = $this->selectTermRange($this->arrSql['search_start_year'], $this->arrSql['search_start_month'], $this->arrSql['search_start_day']
							, $this->arrSql['search_end_year'], $this->arrSql['search_end_month'], $this->arrSql['search_end_day'], $regdate_col);
			if (is_array($arrRegistTime)) {
				foreach ($arrRegistTime as $data4) {
					$this->arrVal[] = $data4;
				}
			}
		}

		// �ǽ�����������
		if ( (strlen($this->arrSql['search_buy_start_year']) > 0 && strlen($this->arrSql['search_buy_start_month']) > 0 && strlen($this->arrSql['search_buy_start_day']) > 0 ) || 
				(strlen($this->arrSql['search_buy_end_year']) > 0 && strlen($this->arrSql['search_buy_end_month']) >0 && strlen($this->arrSql['search_buy_end_day']) > 0) ) {
			$arrRegistTime = $this->selectTermRange($this->arrSql['search_buy_start_year'], $this->arrSql['search_buy_start_month'], $this->arrSql['search_buy_start_day']
							, $this->arrSql['search_buy_end_year'], $this->arrSql['search_buy_end_month'], $this->arrSql['search_buy_end_day'], "last_buy_date");
			if (is_array($arrRegistTime)) {
				foreach ($arrRegistTime as $data4) {
					$this->arrVal[] = $data4;
				}
			}
		}
		
		//�������ʥ�����
		if ( strlen($this->arrSql['search_buy_product_code']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_code LIKE ? ))");
			$search_buyproduct_code = $this->addSearchStr($this->arrSql['search_buy_product_code']);
			$this->arrVal[] = $search_buyproduct_code;
		}

		//��������̾��
		if ( strlen($this->arrSql['search_buy_product_name']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_name LIKE ? ))");
			$search_buyproduct_name = $this->addSearchStr($this->arrSql['search_buy_product_name']);
			$this->arrVal[] = $search_buyproduct_name;
		}
		
		//���ƥ��꡼�����򤷤Ƥ�����Τ߹ʹ�������Ԥ�
		if ( strlen($this->arrSql['search_category_id']) != ""){
			//���ƥ��꡼�ǹʹ�������Ԥ�SQLʸ����
			list($tmp_where, $tmp_arrval) = sfGetCatWhere(sfManualEscape($this->arrSql['search_category_id']));

			//���ƥ��꡼�ǹʹ��ߤ���ǽ�ξ��
			if($tmp_where != "") {
				$this->setWhere( " customer_id IN (SELECT distinct customer_id FROM dtb_order WHERE order_id IN (SELECT distinct order_id FROM dtb_order_detail WHERE product_id IN (SELECT product_id FROM dtb_products WHERE ".$tmp_where." ))) ");
				$this->arrVal = array_merge($this->arrVal, $tmp_arrval);
			}
		}

		$this->setOrder( "customer_id DESC" );
	}

	// ������SQL
	function getList() {
		$this->select = "SELECT customer_id,name01,name02,kana01,kana02,sex,email,tel01,tel02,tel03,pref,status FROM dtb_customer ";
		return $this->getSql(0);	
	}

	function getListMailMagazine() {
		$this->select = "SELECT customer_id,name01,name02,kana01,kana02,sex,email,tel01,tel02,tel03,pref, mail_flag FROM dtb_customer_mail LEFT OUTER JOIN dtb_customer USING(email)";
		return $this->getSql(0);	
	}
	
	function getListMailMagazineCount() {
		$this->select = "SELECT COUNT(*) FROM dtb_customer_mail LEFT OUTER JOIN dtb_customer USING(email)";
		return $this->getSql(0);	
	}
	//�������ʥ����ɸ�����SQL
	function getBuyList(){
		$this->select = "SELECT A.customer_id, A.name01, A.name02, A.kana01, A.kana02, A.sex, A.email, A.tel01, A.tel02, A.tel03, A.pref, A.mail_flag, B.order_email, B.order_id, C.product_code 
						FROM (dtb_customer LEFT OUTER JOIN dtb_customer_mail USING (email)) AS A LEFT OUTER JOIN dtb_order AS B ON 
						A.email=B.order_email LEFT OUTER JOIN dtb_order_detail AS C ON B.order_id = C.order_id";
	}

	//������������������SQL
	function getListCount() {
		$this->select = "SELECT COUNT (customer_id) FROM dtb_customer ";	
		return $this->getSql(1);
	}

	//��CSV�����������SQL
	function getListCSV($arrColumnCSV) {
		$this->arrColumnCSV = $arrColumnCSV;
		$i = 0;
		foreach ($this->arrColumnCSV as $val) {
			if ($i != 0) $state .= ", ";
			$state .= $val["sql"];
			$i ++;
		}

		$this->select = "SELECT " .$state. " FROM dtb_customer ";
		return $this->getSql(2);	
	}
	
	function getWhere() {
		return array($this->where, $this->arrVal);
	}
	
}

?>