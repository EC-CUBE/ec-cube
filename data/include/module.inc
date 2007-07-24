<?php

// エビスタグ出力用クラス
class LC_EbisPage {
    function LC_EbisPage() {
        $this->tpl_mainpage = MODULE_PATH . "mdl_ebis_tag/ebis_tag_text.tpl";
    }
}

// エビスタグの発行
function sfPrintEbisTag($pid = "") {
	$objQuery = new SC_Query();
	$arrRet   = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(EBIS_TAG_MID));
	$arrSubData = unserialize($arrRet[0]['sub_data']);
    
    if ( empty($arrSubData['cid']) ) return;
    
    $arrEbis = array(
        'cid'       => $arrSubData['cid'],
        'login_url' => $arrSubData['login_url'],
    );
    
	if(!is_array($pid) && $pid != "") {
		if(!ereg(".tpl$", $pid)) {
			// ページIDを上書きする
			$arrEbis['pid'] = $pid;
		} else {
			// テンプレートのパスが与えられている場合
            $temp_id = str_replace(USER_TEMPLATE_PATH, '', $pid);
            $temp_id = str_replace(HTML_PATH,   '',  $temp_id);
            $temp_id = preg_replace('|^/+|',    '',  $temp_id);
            $temp_id = preg_replace('|\.tpl$|', '',  $temp_id);
            $temp_id = preg_replace('|[\./]|',  '_', $temp_id);
			$arrEbis['pid'] = $temp_id;
		}
	}	
	
	// 商品一覧ページは、特殊IDを発行
	if(ereg("/products/list.php\?category_id=[0-9]+$", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace("list.php\?category_id=", "list-c", $filename);
	}
	
	// 商品詳細ページは、特殊IDを発行
	if(ereg("/products/detail.php\?product_id=[0-9]+$", $_SERVER["REQUEST_URI"])) {
		$filename = basename($_SERVER["REQUEST_URI"]);
		$arrEbis['pid'] = ereg_replace("detail.php\?product_id=", "detail-p", $filename);
	}

    // 購入完了ページ(thanksページ)は特殊IDを発行
    lfSetThanksPegeTag($arrEbis, $arrSubData);

	// ID割り当てされていないページは、自動的に生成する。
	if($arrEbis['pid'] == "") {				
		$temp_id = ereg_replace("^[/]+","",$_SERVER['PHP_SELF']);
		$temp_id = ereg_replace(".php$","",$temp_id);
		$temp_id = ereg_replace("[\./]","_",$temp_id);
		$arrEbis['pid'] = $temp_id;
	}

	// ページIDが登録されている場合のみタグを出力する。
	if($arrEbis['pid'] != "") {
		$objSubPage = new LC_EbisPage();
		$objSubPage->arrEbis = $arrEbis;
		$objSubView = new SC_SiteView();
		$objSubView->assignobj($objSubPage);
		$objSubView->display($objSubPage->tpl_mainpage);
	}
}

// 購入完了画面のエビスタグを設定
function lfSetThanksPegeTag(&$arrEbis, $arrSubData){
    $pattern = "|/shopping/complete.php$|";
    $target  = $_SERVER["REQUEST_URI"];
    if( !preg_match($pattern, $target) ) return;
    
    global $arrEBiSTagAttrTagName;
    global $arrJobEN;
    global $arrSexEN;
    global $order_id;
    
    $objQuery = new SC_Query();
    $arrRet   = $objQuery->select('*', 'dtb_order', 'order_id = ?', array($order_id));
    $arrCustomerInfo = $arrRet[0];
    $arrEbis['pid']  = 'thanks'; // ページIDをセット
    
    /**
     * $arrSubData == Array (
     *      [user] => username
     *      [pass] => password
     *      [login_url] => http://login_url/
     *      [cid] => ebis_parameter
     *      [m1id] => 1
     *      [a1id] => 2
     *      [o1id] => 3
     *      [o2id] => 4
     *      [o3id] => 0
     *      [o4id] => 0
     *      [o5id] => 0
     * )
     * $arrEBiSTagAttrTagName == Array(
     *      'm1id', 'a1id', 'o1id', 'o2id'
     *      'o3id', 'o4id', 'o5id'
     * )
     **/
    
    foreach ($arrEBiSTagAttrTagName as $tagname) {
        if ( empty($arrSubData[$tagname]) ) continue;
        
        $value = $arrSubData[$tagname];
        
        // 属性情報をセット
        switch ($value) {
        case EBiS_TAG_ATTR_CUSTOMER_ID:
            $arrEbis[$tagname] = $arrCustomerInfo['customer_id'];
            break;
        case EBiS_TAG_ATTR_PAYMENT:
            $arrEbis[$tagname] = $arrCustomerInfo['payment_total'];
            break;
        case EBiS_TAG_ATTR_JOB:
            if ( empty($arrCustomerInfo['order_job']) ) break;
            $arrEbis[$tagname] = $arrJobEN[$arrCustomerInfo['order_job']];
            break;
        case EBiS_TAG_ATTR_SEX:
            $arrEbis[$tagname] = $arrSexEN[$arrCustomerInfo['order_sex']];
            break;
        case EBiS_TAG_ATTR_AGE:
            if ( empty($arrCustomerInfo['order_birth']) ) break;
            $arrEbis[$tagname] = gfGetAge($arrCustomerInfo['order_birth']);
            break;
        case EBiS_TAG_ATTR_IS_CUSTOMER:
            if ( $arrCustomerInfo['customer_id'] == '0' ) {
                $arrEbis[$tagname] = '0';
            } else {
                $arrEbis[$tagname] = '1';
            }
            break;
        default:
        }
    }
}

// コンバージョンタグの発行
function sfPrintAffTag($conv_page, $option) {
	if(is_numeric($conv_page)) {
		// sub_dataよりタグ情報を読み込む
		$objQuery = new SC_Query();
		$arrRet = $objQuery->select("sub_data", "dtb_module", "module_id = ?", array(AFF_TAG_MID));
		$arrSubData = unserialize($arrRet[0]['sub_data']);
		$aff_tag = $arrSubData[$conv_page];
		
		$array = split("\|", $option);
		
		// 特定文字の置き換え
		foreach($array as $each) {
			list($key, $value) = split("=", $each);
			$aff_tag = ereg_replace("\[\[" . $key . "\]\]", $value, $aff_tag);
		}
		print($aff_tag);		
	}
}

// dtb_paymentに汎用項目が存在していなければ追加する
function sfAlterMemo(){
	$objQuery = new SC_Query();
	
	// 汎用項目の存在チェック
	if(!sfColumnExists("dtb_payment", "memo01")){
		
		// モジュールIDを追加
		$objQuery->query("alter table dtb_payment add module_id int4;");
		
		// モジュールパスを追加
		$objQuery->query("alter table dtb_payment add module_path text;");
		
		// 汎用項目を10個追加
		for($i=1; $i<=9; $i++){
			$objQuery->query("alter table dtb_payment add memo0".$i." text;");
		}
		$objQuery->query("alter table dtb_payment add memo10 text;");
	}
}

?>