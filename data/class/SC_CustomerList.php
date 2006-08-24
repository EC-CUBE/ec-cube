<?php

/*  [名称] SC_CustomerList
 *  [概要] 会員検索用クラス
 */
class SC_CustomerList extends SC_SelectSql {

	var $arrColumnCSV;
	
	function SC_CustomerList($array, $mode = '') {
		parent::SC_SelectSql($array);
		
		if($mode == "") {
			// 会員本登録会員で削除していない会員
			$this->setWhere("status = 2 AND delete = 0 ");		
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer.update_date';
		}
		if($mode == "customer") {
			// 管理者ページ顧客検索の場合仮登録会員も検索
			$this->setWhere( "(status = 1 OR status = 2) AND delete = 0 ");		
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer.update_date';
		}
		
		// メールマガジンの場合		
		if($mode == "magazine") {
			$this->setWhere("(delete = 0 OR delete IS NULL)");
			
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
						// dtb_customer_mailにのみ登録されているレコードを抽出
						$tmp_where.= "customer_id IS NULL ";
						break;
					// CSV登録
					case '3':
						// dtb_customer_mailにのみ登録されているレコードを抽出
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
			// 登録日を示すカラム
			$regdate_col = 'dtb_customer_mail.create_date';
		}
				
		// 顧客ID
		if ( strlen($this->arrSql['search_customer_id']) > 0 ) {
			$this->setWhere( "customer_id =  ?" );
			$this->arrVal[] = $this->arrSql['search_customer_id'];
		}
		
		// 名前
		if ( strlen($this->arrSql['search_name']) > 0 ) {
			$this->setWhere("(name01 || name02 LIKE ?)" );
			$searchName = $this->addSearchStr($this->arrSql['search_name']);
			$this->arrVal[] = ereg_replace("[ 　]+","",$searchName);
		}

		//　名前（カナ）
		if ( strlen($this->arrSql['search_kana']) > 0 ) {
			$this->setWhere("(kana01 || kana02 LIKE ?)");
			$searchKana = $this->addSearchStr($this->arrSql['search_kana']);
			$this->arrVal[] = ereg_replace("[ 　]+","",$searchKana);
		}
		
		//　都道府県
		if ( strlen($this->arrSql['search_pref']) > 0 ) {
			$this->setWhere( "pref = ?" );
			$this->arrVal[] = $this->arrSql['search_pref'];
		}

		//　電話番号
		if ( is_numeric( $this->arrSql['search_tel'] ) ) {
			$this->setWhere( "(tel01 || tel02 || tel03 LIKE ?)" );
			$searchTel = $this->addSearchStr($this->arrSql['search_tel']);
			$this->arrVal[] = ereg_replace("-", "", $searchTel);
		}
		
		//　性別
		if ( is_array( $this->arrSql['search_sex'] ) ){
			$arrSexVal = $this->setItemTerm( $this->arrSql['search_sex'] ,"sex" );
			foreach ($arrSexVal as $data) {
				$this->arrVal[] = $data;
			}
		}

		//　職業
		if ( is_array( $this->arrSql['search_job'] ) ){
			if ( in_array("不明", $this->arrSql['search_job'] ) ) {
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

		//　E-MAIL
		if ( strlen($this->arrSql['search_email']) > 0 ) {
			$this->setWhere( "email ILIKE ? " );
			$searchEmail = $this->addSearchStr($this->arrSql['search_email']);
			$this->arrVal[] = $searchEmail;
		}

		//　HTML-mail
		if ( $mode == 'magazine' ){
			if ( strlen($this->arrSql['search_htmlmail']) > 0 ) {
				$this->setWhere( " mail_flag = ? ");
				$this->arrVal[] = $this->arrSql['search_htmlmail'];
			} else {
				$this->setWhere( " (mail_flag = 1 or mail_flag = 2) ");
			}
		}
		
		// 購入金額指定
		if( is_numeric( $this->arrSql["search_buy_total_from"] ) || is_numeric( $this->arrSql["search_buy_total_to"] ) ) {
			$arrBuyTotal = $this->selectRange($this->arrSql["buy_total_from"], $this->arrSql["buy_total_to"], "buy_total");
			foreach ($arrBuyTotal as $data1) {
				$this->arrVal[] = $data1;
			}
		}

		// 購入回数指定
		if( is_numeric( $this->arrSql["search_buy_times_from"] ) || is_numeric( $this->arrSql["search_buy_times_to"] ) ) {
			$arrBuyTimes = $this->selectRange($this->arrSql["buy_times_from"], $this->arrSql["buy_times_to"], "buy_times");
			foreach ($arrBuyTimes as $data2) {
				$this->arrVal[] = $data2;
			}
		}
		
		// 誕生日期間指定
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

		// 誕生月の検索
		if (is_numeric($this->arrSql["search_birth_month"])) {
			$this->setWhere(" EXTRACT(month from birth) = ?");  
			$this->arrVal[] = $this->arrSql["search_birth_month"];
		}

		// 登録期間指定
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

		// 最終購入日指定
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
		
		//購入商品コード
		if ( strlen($this->arrSql['search_buy_product_code']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_code LIKE ? ))");
			$search_buyproduct_code = $this->addSearchStr($this->arrSql['search_buy_product_code']);
			$this->arrVal[] = $search_buyproduct_code;
		}

		//購入商品名称
		if ( strlen($this->arrSql['search_buy_product_name']) > 0 ) {
			$this->setWhere( "customer_id IN (SELECT customer_id FROM dtb_order WHERE order_id IN (SELECT order_id FROM dtb_order_detail WHERE product_name LIKE ? ))");
			$search_buyproduct_name = $this->addSearchStr($this->arrSql['search_buy_product_name']);
			$this->arrVal[] = $search_buyproduct_name;
		}
		
		//カテゴリーを選択している場合のみ絞込検索を行う
		if ( strlen($this->arrSql['search_category_id']) != ""){
			//カテゴリーで絞込検索を行うSQL文生成
			list($tmp_where, $tmp_arrval) = sfGetCatWhere(sfManualEscape($this->arrSql['search_category_id']));

			//カテゴリーで絞込みが可能の場合
			if($tmp_where != "") {
				$this->setWhere( " customer_id IN (SELECT distinct customer_id FROM dtb_order WHERE order_id IN (SELECT distinct order_id FROM dtb_order_detail WHERE product_id IN (SELECT product_id FROM dtb_products WHERE ".$tmp_where." ))) ");
				$this->arrVal = array_merge($this->arrVal, $tmp_arrval);
			}
		}

		$this->setOrder( "customer_id DESC" );
	}

	// 検索用SQL
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
	//購入商品コード検索用SQL
	function getBuyList(){
		$this->select = "SELECT A.customer_id, A.name01, A.name02, A.kana01, A.kana02, A.sex, A.email, A.tel01, A.tel02, A.tel03, A.pref, A.mail_flag, B.order_email, B.order_id, C.product_code 
						FROM (dtb_customer LEFT OUTER JOIN dtb_customer_mail USING (email)) AS A LEFT OUTER JOIN dtb_order AS B ON 
						A.email=B.order_email LEFT OUTER JOIN dtb_order_detail AS C ON B.order_id = C.order_id";
	}

	//　検索総数カウント用SQL
	function getListCount() {
		$this->select = "SELECT COUNT (customer_id) FROM dtb_customer ";	
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