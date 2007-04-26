<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*  [名称] SC_CustomerList
 *  [概要] 会員検索用クラス
 */
class SC_CustomerList extends SC_SelectSql {

	var $arrColumnCSV;
	
	function SC_CustomerList($array, $mode = '') {
		parent::SC_SelectSql($array);
		
		if($mode == "") {
			// 会員本登録会員で削除していない会員
			$this->setWhere("status = 2 AND del_flg = 0 ");		
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer.update_date';
		}
		
		if($mode == "customer") {
			// 管理者ページ顧客検索の場合仮登録会員も検索
			$this->setWhere( "(status = 1 OR status = 2) AND del_flg = 0 ");		
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer.update_date';
		}
				
		// メールマガジンの場合		
		if($mode == "magazine") {
			$this->setWhere("(del_flg = 0 OR del_flg IS NULL)");
			$this->setWhere("status = 2");
			
			/*　会員のみ対象とするため削除
			if(is_array($this->arrSql['customer'])) {
				$tmp_where = "";
				foreach($this->arrSql['customer'] as $val) {
					if($tmp_where != "") {
						$tmp_where.= " OR ";
					}					
					switch($val) {
					// 会員
					case '1':
						$tmp_where.= "status = 2";
						break;
					// メルマガ登録
					case '2':
						$tmp_where.= "customer_id IS NULL";
						break;
					// CSV登録
					case '3':
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
			*/
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer.create_date';
		}
				
		// 顧客ID
		if ( strlen($this->arrSql['customer_id']) > 0 ) {
			$this->setWhere( "customer_id =  ?" );
			$this->arrVal[] = $this->arrSql['customer_id'];
		}
		
		// 名前
		if ( strlen($this->arrSql['name']) > 0 ) {
			if(DB_TYPE == "pgsql"){
				$this->setWhere("(name01 || name02 LIKE ?)" );
			}elseif(DB_TYPE == "mysql"){
				$this->setWhere("concat(name01,name02) LIKE ?" );
			}

			$searchName = $this->addSearchStr($this->arrSql['name']);
			$this->arrVal[] = mb_ereg_replace("[ 　]+","",$searchName);
		}

		//　名前（カナ）
		if ( strlen($this->arrSql['kana']) > 0 ) {
			if(DB_TYPE == "pgsql"){
				$this->setWhere("(kana01 || kana02 LIKE ?)");
			}elseif(DB_TYPE == "mysql"){
				$this->setWhere("concat(kana01,kana02) LIKE ?" );
			}
			$searchKana = $this->addSearchStr($this->arrSql['kana']);
			$this->arrVal[] = mb_ereg_replace("[ 　]+","",$searchKana);
		}
		
		//　都道府県
		if ( strlen($this->arrSql['pref']) > 0 ) {
			$this->setWhere( "pref = ?" );
			$this->arrVal[] = $this->arrSql['pref'];
		}

		//　電話番号
		if ( is_numeric( $this->arrSql['tel'] ) ) {
			if(DB_TYPE == "pgsql"){
				$this->setWhere( "(tel01 || tel02 || tel03 LIKE ?)" );
			}elseif(DB_TYPE == "mysql"){
				$this->setWhere("concat(tel01,tel02,tel03) LIKE ?" );
			}
			$searchTel = $this->addSearchStr($this->arrSql['tel']);
			$this->arrVal[] = ereg_replace("-", "", $searchTel);
		}
		
		//　性別
		if ( is_array( $this->arrSql['sex'] ) ){
			$arrSexVal = $this->setItemTerm( $this->arrSql['sex'] ,"sex" );
			foreach ($arrSexVal as $data) {
				$this->arrVal[] = $data;
			}
		}

		//　職業
		if ( is_array( $this->arrSql['job'] ) ){
			if ( in_array("不明", $this->arrSql['job'] ) ) {
				$arrJobVal = $this->setItemTermWithNull( $this->arrSql['job'] ,"job" );
			} else {
				$arrJobVal = $this->setItemTerm( $this->arrSql['job'] ,"job" );
			}
			if (is_array($arrJobVal)) {
				foreach ($arrJobVal as $data) {
					$this->arrVal[] = $data;
				}
			}
		}

		//　E-MAIL
		if (strlen($this->arrSql['email']) > 0) {
			//カンマ区切りで複数の条件指定可能に
			$this->arrSql['email'] = explode(",", $this->arrSql['email']);
			$sql_where = "";
			foreach($this->arrSql['email'] as $val) {
				$val = trim($val);
				//検索条件を含まない
				if($this->arrSql['not_emailinc'] == '1') {
					if($sql_where == "") {
						$sql_where .= "dtb_customer.email NOT ILIKE ? ";
					} else {
						$sql_where .= "AND dtb_customer.email NOT ILIKE ? ";
					}
				} else {				
					if($sql_where == "") {
						$sql_where .= "dtb_customer.email ILIKE ? ";
					} else {
						$sql_where .= "OR dtb_customer.email ILIKE ? ";
					}
				}
				$searchEmail = $this->addSearchStr($val);
				$this->arrVal[] = $searchEmail;
			}
			$this->setWhere($sql_where);
		}

		//　E-MAIL(mobile)
		if (strlen($this->arrSql['email_mobile']) > 0) {
			//カンマ区切りで複数の条件指定可能に
			$this->arrSql['email_mobile'] = explode(",", $this->arrSql['email_mobile']);
			$sql_where = "";
			foreach($this->arrSql['email_mobile'] as $val) {
				$val = trim($val);
				//検索条件を含まない
				if($this->arrSql['not_email_mobileinc'] == '1') {
					if($sql_where == "") {
						$sql_where .= "dtb_customer.email_mobile NOT ILIKE ? ";
					} else {
						$sql_where .= "AND dtb_customer.email_mobile NOT ILIKE ? ";
					}
				} else {				
					if($sql_where == "") {
						$sql_where .= "dtb_customer.email_mobile ILIKE ? ";
					} else {
						$sql_where .= "OR dtb_customer.email_mobile ILIKE ? ";
					}
				}
				$searchemail_mobile = $this->addSearchStr($val);
				$this->arrVal[] = $searchemail_mobile;
			}
			$this->setWhere($sql_where);
		}
				
		//　配信メールアドレス種別
		if ( $mode == 'magazine' ){
			if ( strlen($this->arrSql['mail_type']) > 0 && $this->arrSql['mail_type'] == 2) {
				$this->setWhere( " dtb_customer.email_mobile <> ''  ");
			}
		}
							
		//　HTML-mail
		if ( $mode == 'magazine' ){
			if ( strlen($this->arrSql['htmlmail']) > 0 ) {
				$this->setWhere( " mailmaga_flg = ? ");
				$this->arrVal[] = $this->arrSql['htmlmail'];
			} else {
				$this->setWhere( " (mailmaga_flg = 1 or mailmaga_flg = 2) ");
			}
		}
		
		// 購入金額指定
		if( is_numeric( $this->arrSql["buy_total_from"] ) || is_numeric( $this->arrSql["buy_total_to"] ) ) {
			$arrBuyTotal = $this->selectRange($this->arrSql["buy_total_from"], $this->arrSql["buy_total_to"], "buy_total");
			foreach ($arrBuyTotal as $data1) {
				$this->arrVal[] = $data1;
			}
		}

		// 購入回数指定
		if( is_numeric( $this->arrSql["buy_times_from"] ) || is_numeric( $this->arrSql["buy_times_to"] ) ) {
			$arrBuyTimes = $this->selectRange($this->arrSql["buy_times_from"], $this->arrSql["buy_times_to"], "buy_times");
			foreach ($arrBuyTimes as $data2) {
				$this->arrVal[] = $data2;
			}
		}
		
		// 誕生日期間指定
		if ( (strlen($this->arrSql['b_start_year']) > 0 && strlen($this->arrSql['b_start_month']) > 0 && strlen($this->arrSql['b_start_day']) > 0) ||
			  strlen($this->arrSql['b_end_year']) > 0 && strlen($this->arrSql['b_end_month']) > 0 && strlen($this->arrSql['b_end_day']) > 0) {

			$arrBirth = $this->selectTermRange($this->arrSql['b_start_year'], $this->arrSql['b_start_month'], $this->arrSql['b_start_day']
					  , $this->arrSql['b_end_year'], $this->arrSql['b_end_month'], $this->arrSql['b_end_day'], "birth");
			if (is_array($arrBirth)) {
				foreach ($arrBirth as $data3) {
					$this->arrVal[] = $data3;
				}
			}
		}
				
		// 誕生月の検索
		if (is_numeric($this->arrSql["birth_month"])) {
			$this->setWhere(" EXTRACT(month from birth) = ?");  
			$this->arrVal[] = $this->arrSql["birth_month"];
		}
			
		// 登録期間指定
		if ( (strlen($this->arrSql['start_year']) > 0 && strlen($this->arrSql['start_month']) > 0 && strlen($this->arrSql['start_day']) > 0 ) || 
				(strlen($this->arrSql['end_year']) > 0 && strlen($this->arrSql['end_month']) >0 && strlen($this->arrSql['end_day']) > 0) ) {

			$arrRegistTime = $this->selectTermRange($this->arrSql['start_year'], $this->arrSql['start_month'], $this->arrSql['start_day']
							, $this->arrSql['end_year'], $this->arrSql['end_month'], $this->arrSql['end_day'], $regdate_col);
			if (is_array($arrRegistTime)) {
				foreach ($arrRegistTime as $data4) {
					$this->arrVal[] = $data4;
				}
			}
		}
			
		// 最終購入日指定
		if ( (strlen($this->arrSql['buy_start_year']) > 0 && strlen($this->arrSql['buy_start_month']) > 0 && strlen($this->arrSql['buy_start_day']) > 0 ) || 
				(strlen($this->arrSql['buy_end_year']) > 0 && strlen($this->arrSql['buy_end_month']) >0 && strlen($this->arrSql['buy_end_day']) > 0) ) {
			$arrRegistTime = $this->selectTermRange($this->arrSql['buy_start_year'], $this->arrSql['buy_start_month'], $this->arrSql['buy_start_day']
							, $this->arrSql['buy_end_year'], $this->arrSql['buy_end_month'], $this->arrSql['buy_end_day'], "last_buy_date");
			if (is_array($arrRegistTime)) {
				foreach ($arrRegistTime as $data4) {
					$this->arrVal[] = $data4;
				}
			}
		}
		
		//購入商品コード
		if ( strlen($this->arrSql['buy_product_code']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_code LIKE ? ))");
			$search_buyproduct_code = $this->addSearchStr($this->arrSql['buy_product_code']);
			$this->arrVal[] = $search_buyproduct_code;
		}

		//購入商品名称
		if ( strlen($this->arrSql['buy_product_name']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_name LIKE ? ))");
			$search_buyproduct_name = $this->addSearchStr($this->arrSql['buy_product_name']);
			$this->arrVal[] = $search_buyproduct_name;
		}
		
		//カテゴリーを選択している場合のみ絞込検索を行う
		if ( strlen($this->arrSql['category_id']) != ""){
			//カテゴリーで絞込検索を行うSQL文生成
			list($tmp_where, $tmp_arrval) = sfGetCatWhere(sfManualEscape($this->arrSql['category_id']));

			//カテゴリーで絞込みが可能の場合
			if($tmp_where != "") {
				$this->setWhere( " customer_id IN (SELECT distinct customer_id FROM dtb_order WHERE order_id IN (SELECT distinct order_id FROM dtb_order_detail WHERE product_id IN (SELECT product_id FROM dtb_products WHERE ".$tmp_where." ))) ");
				$this->arrVal = array_merge((array)$this->arrVal, (array)$tmp_arrval);
			}
		}
		//　携帯電話番号
		if ( is_numeric( $this->arrSql['cell'] ) ) {
			$this->setWhere( "(cell01 || cell02 || cell03 LIKE ?)" );
			$searchTel = $this->addSearchStr($this->arrSql['cell']);
			$this->arrVal[] = ereg_replace("-", "", $searchTel);
		}

		//　キャンペーン
		if ( is_numeric( $this->arrSql['campaign_id'] ) ) {
			$this->setWhere( " customer_id IN (SELECT distinct customer_id FROM dtb_campaign_order WHERE campaign_id = ?)" );
			$this->arrVal[] = $this->arrSql['campaign_id'];
		}
				
		$this->setOrder( "customer_id DESC" );
	}

	// 検索用SQL
	function getList() {
		$this->select = "SELECT customer_id,name01,name02,kana01,kana02,sex,email,tel01,tel02,tel03,pref,status FROM dtb_customer ";
		return $this->getSql(0);	
	}

	function getListMailMagazine($is_mobile = false) {
			
		$colomn = $this->getMailMagazineColumn($is_mobile);
		$this->select = "
			SELECT 
				$colomn
			FROM 
				dtb_customer";
		return $this->getSql(0);	
	}
	
	function getMailMagazineColumn($is_mobile= false) {
		if($is_mobile == true) {
			$email_column = "dtb_customer.email_mobile as email";
		} else {
			$email_column = "dtb_customer.email";			
		}
		
		$column ="dtb_customer.customer_id,
				dtb_customer.name01,
				dtb_customer.name02,
				dtb_customer.kana01,
				dtb_customer.kana02,
				dtb_customer.sex,
				$email_column,		
				dtb_customer.tel01,
				dtb_customer.tel02,
				dtb_customer.tel03,
				dtb_customer.pref, 
				dtb_customer.mailmaga_flg";
				
		return $column;
	}
	
	//　検索総数カウント用SQL
	function getListCount() {
		$this->select = "SELECT COUNT(customer_id) FROM dtb_customer ";	
		return $this->getSql(1);
	}

	//　CSVダウンロード用SQL
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