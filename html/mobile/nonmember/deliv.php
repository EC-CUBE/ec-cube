<?php 

require_once("../require.php");

class LC_Page {
    var $arrSession;
    var $tpl_mode;
    var $tpl_login_email;
    function LC_Page() {
        $this->tpl_mainpage = 'nonmember/index.tpl';
        global $arrPref;
        $this->arrPref = $arrPref;
        global $arrSex;
        $this->arrSex = $arrSex;
        global $arrJob;
        $this->arrJob = $arrJob;
        $this->tpl_onload = 'fnCheckInputDeliv();';
        
        /*
         session_start時のno-cacheヘッダーを抑制することで
         「戻る」ボタン使用時の有効期限切れ表示を抑制する。
         private-no-expire:クライアントのキャッシュを許可する。
        */
        session_cache_limiter('private-no-expire');             
    }
}

$conn = new SC_DBConn();
$objPage = new LC_Page();
$objView = new SC_MobileView();
$objSiteSess = new SC_SiteSession();
$objCartSess = new SC_CartSession();
$objCustomer = new SC_Customer();
$objCookie = new SC_Cookie();
$objFormParam = new SC_FormParam();         // フォーム用
lfInitParam();                              // パラメータ情報の初期化
$objFormParam->setParam($_POST);            // POST値の取得

$uniqid = sfCheckNormalAccess($objSiteSess, $objCartSess);
// ユニークIDを引き継ぐ
$objPage->tpl_uniqid = $uniqid;

if(!empty($_POST["mode2"])){
if ($_POST["mode2"] == "deliv") {
            
            $objFormParam = new SC_FormParam();
            // パラメータ情報の初期化
           
            // POST値の取得
            $objFormParam->setParam($_POST);
            $arrRet = $objFormParam->getHashArray();
            $sqlval = $objFormParam->getDbArray();
            
            // 入力値の取得
            $objPage->arrForm = $objFormParam->getFormParamList();
            $objPage->arrErr = $arrErr;
           
           foreach($_POST as $key => $value){
               $objPage->arrAddr[0][$key] = $value;
           }
            lfRegistDataTemp($objPage->arrAddr[0]['uniqid'],$objPage->arrAddr[0]); 
            
            lfCopyDeliv($objPage->tpl_uniqid, $_POST);
           
            $objPage->tpl_mainpage = 'nonmember/nonmember_deliv.tpl';
            $objPage->tpl_title = 'お届け先情報';
            //objPageの情報をobjViewに格納
            $objView->assignobj($objPage);
            $objView->display(SITE_FRAME);
        }
        
         if ($_POST["mode2"] == "customer_addr") {
            //print_r($_POST);
            if ($_POST['deli'] != "") {
           
           header("Location:" . gfAddSessionId("./payment.php"));
            exit;
    }else{
        // エラーを返す
        $arrErr['deli'] = '※ お届け先を選択してください。';
            }
        }
    }elseif(!empty($_POST["mode"]) && $_POST["mode"]=="deliv_date"){  
        $objQuery = new SC_Query();
        //print($objPage->tpl_uniqid);
        $objPage->tpl_mainpage = 'nonmember/nonmember_deliv.tpl';
        $objPage->tpl_title = 'お届け先情報';
        //objPageの情報をobjViewに格納
        
        $where = "order_temp_id = ?";
        $arrRet = $objQuery->select("*", "dtb_order_temp", $where, array($objPage->tpl_uniqid));
        $objFormParam->setParam($arrRet[0]);
        $objPage->arrForm = $objFormParam->getFormParamList();        
        
             foreach($objPage->arrForm as $key => $value){
               $objPage->arrAddr[0][$key] = $value;
           }
        print_r($objPage->arrAddr[0]);
        $objView->assignobj($objPage);
        $objView->display(SITE_FRAME);
    }

//入力された情報をデータベースdtb_order_tempに格納する
function lfRegistDataTemp($uniqid,$array) {
    global $objFormParam;
    $arrRet = $objFormParam->getHashArray();
    $sqlval = $objFormParam->getDbArray();
        
    // 登録データの作成
    $sqlval['order_temp_id'] = $uniqid;
    $sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
    $sqlval['update_date'] = 'Now()';
    $sqlval['customer_id'] = '0';
    
    $sqlval['order_name01'] = $array['name01'];
    $sqlval['order_name02'] = $array['name02'];
    $sqlval['order_kana01'] = $array['kana01'];
    $sqlval['order_kana02'] = $array['kana02'];
    $sqlval['order_zip01'] = $array['zip01'];
    $sqlval['order_zip02'] = $array['zip02'];
    $sqlval['order_pref'] = $array['pref'];
    $sqlval['order_addr01'] = $array['addr01'];
    $sqlval['order_addr02'] = $array['addr02'];
    $sqlval['order_tel01'] = $array['tel01'];
    $sqlval['order_tel02'] = $array['tel02'];
    $sqlval['order_tel03'] = $array['tel03'];
    $sqlval['order_email'] = $array['email'];
    $sqlval['order_sex'] = $array['sex'];
          
    // 既存データのチェック
    $objQuery = new SC_Query();
    $where = "order_temp_id = ?";
    $cnt = $objQuery->count("dtb_order_temp", $where, array($uniqid));
    // 既存データがない場合
    if ($cnt == 0) {
        $sqlval['create_date'] = 'Now()';
        $objQuery->insert("dtb_order_temp", $sqlval);
    } else {
        $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    }
}

/* パラメータ情報の初期化 */
function lfInitParam() {
    global $objFormParam;
    $objFormParam->addParam("お名前（姓）", "order_name01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("お名前（名）", "order_name02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("フリガナ（セイ）", "order_kana01", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("フリガナ（メイ）", "order_kana02", STEXT_LEN, "KVCa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("郵便番号1", "order_zip01", ZIP01_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("郵便番号2", "order_zip02", ZIP02_LEN, "n", array("EXIST_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("都道府県", "order_pref", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("住所1", "order_addr01", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("住所2", "order_addr02", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("電話番号1", "order_tel01", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("電話番号2", "order_tel02", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("電話番号3", "order_tel03", TEL_ITEM_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX番号1", "order_fax01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX番号2", "order_fax02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("FAX番号3", "order_fax03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("メールアドレス", "order_email", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"));
    $objFormParam->addParam("メールアドレス（確認）", "order_email_check", STEXT_LEN, "KVa", array("EXIST_CHECK", "SPTAB_CHECK", "NO_SPTAB", "MAX_LENGTH_CHECK", "EMAIL_CHECK", "EMAIL_CHAR_CHECK"), "", false);
    $objFormParam->addParam("年", "year", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("月", "month", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("日", "day", INT_LEN, "n", array("MAX_LENGTH_CHECK"), "", false);
    $objFormParam->addParam("性別", "order_sex", INT_LEN, "n", array("EXIST_CHECK", "MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("職業", "order_job", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("別のお届け先", "deliv_check", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("お名前（姓）", "deliv_name01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("お名前（名）", "deliv_name02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("フリガナ（セイ）", "deliv_kana01", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("フリガナ（メイ）", "deliv_kana02", STEXT_LEN, "KVCa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("郵便番号1", "deliv_zip01", ZIP01_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("郵便番号2", "deliv_zip02", ZIP02_LEN, "n", array("NUM_CHECK", "NUM_COUNT_CHECK"));
    $objFormParam->addParam("都道府県", "deliv_pref", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"));
    $objFormParam->addParam("住所1", "deliv_addr01", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("住所2", "deliv_addr02", STEXT_LEN, "KVa", array("SPTAB_CHECK", "MAX_LENGTH_CHECK"));
    $objFormParam->addParam("電話番号1", "deliv_tel01", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("電話番号2", "deliv_tel02", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("電話番号3", "deliv_tel03", TEL_ITEM_LEN, "n", array("MAX_LENGTH_CHECK" ,"NUM_CHECK"));
    $objFormParam->addParam("メールマガジン", "mail_flag", INT_LEN, "n", array("MAX_LENGTH_CHECK", "NUM_CHECK"), 1);
}

/* DBへデータの登録 */


// 受注一時テーブルのお届け先をコピーする
function lfCopyDeliv($uniqid, $arrData) {
    $objQuery = new SC_Query();
    
    // 別のお届け先を指定していない場合、配送先に登録住所をコピーする。
    if($arrData["deliv_check"] != "1") {
        $sqlval['deliv_name01'] = $arrData['order_name01'];
        $sqlval['deliv_name02'] = $arrData['order_name02'];
        $sqlval['deliv_kana01'] = $arrData['order_kana01'];
        $sqlval['deliv_kana02'] = $arrData['order_kana02'];
        $sqlval['deliv_pref'] = $arrData['order_pref'];
        $sqlval['deliv_zip01'] = $arrData['order_zip01'];
        $sqlval['deliv_zip02'] = $arrData['order_zip02'];
        $sqlval['deliv_addr01'] = $arrData['order_addr01'];
        $sqlval['deliv_addr02'] = $arrData['order_addr02'];
        $sqlval['deliv_tel01'] = $arrData['order_tel01'];
        $sqlval['deliv_tel02'] = $arrData['order_tel02'];
        $sqlval['deliv_tel03'] = $arrData['order_tel03'];
        $where = "order_temp_id = ?";
        $objQuery->update("dtb_order_temp", $sqlval, $where, array($uniqid));
    }
}

?>