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
print_r($_POST);

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
            lfRegistDataTemp($objPage->tpl_uniqid,$objPage->arrAddr[0]); 
            
            print("test-------------------------------------------<BR>");
            lfCopyDeliv($objPage->tpl_uniqid, $_POST);
           
            $objPage->tpl_mainpage = 'nonmember/nonmember_deliv.tpl';
            $objPage->tpl_title = 'お届け先情報';
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
         
         function lfRegistData($uniqid) {
    global $objFormParam;
    $arrRet = $objFormParam->getHashArray();
    $sqlval = $objFormParam->getDbArray();
    
    // 登録データの作成
    $sqlval['order_temp_id'] = $uniqid;
    $sqlval['order_birth'] = sfGetTimestamp($arrRet['year'], $arrRet['month'], $arrRet['day']);
    $sqlval['update_date'] = 'Now()';
    $sqlval['customer_id'] = '0';
    $sqlval['order_name01'] = $objPage->arrAddr[0]['name01'];
          
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

/* 入力内容のチェック */
function lfCheckError() {
    global $objFormParam;
    // 入力データを渡す。
    $arrRet =  $objFormParam->getHashArray();
    $objErr = new SC_CheckError($arrRet);
    $objErr->arrErr = $objFormParam->checkError();
        
    // 別のお届け先チェック
    if($_POST['deliv_check'] == "1") { 
        $objErr->doFunc(array("お名前（姓）", "deliv_name01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("お名前（名）", "deliv_name02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("フリガナ（セイ）", "deliv_kana01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("フリガナ（メイ）", "deliv_kana02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("郵便番号1", "deliv_zip01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("郵便番号2", "deliv_zip02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("都道府県", "deliv_pref"), array("EXIST_CHECK"));
        $objErr->doFunc(array("住所1", "deliv_addr01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("住所2", "deliv_addr02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("電話番号1", "deliv_tel01"), array("EXIST_CHECK"));
        $objErr->doFunc(array("電話番号2", "deliv_tel02"), array("EXIST_CHECK"));
        $objErr->doFunc(array("電話番号3", "deliv_tel03"), array("EXIST_CHECK"));
    }
    
    // 複数項目チェック
    $objErr->doFunc(array("TEL", "order_tel01", "order_tel02", "order_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("FAX", "order_fax01", "order_fax02", "order_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("郵便番号", "order_zip01", "order_zip02"), array("ALL_EXIST_CHECK"));
    $objErr->doFunc(array("TEL", "deliv_tel01", "deliv_tel02", "deliv_tel03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("FAX", "deliv_fax01", "deliv_fax02", "deliv_fax03", TEL_ITEM_LEN), array("TEL_CHECK"));
    $objErr->doFunc(array("郵便番号", "deliv_zip01", "deliv_zip02"), array("ALL_EXIST_CHECK"));
    $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
    $objErr->doFunc(array("メールアドレス", "メールアドレス（確認）", "order_email", "order_email_check"), array("EQUAL_CHECK"));
    
    // すでにメルマガテーブルに会員としてメールアドレスが登録されている場合
    if(sfCheckCustomerMailMaga($arrRet['order_email'])) {
        $objErr->arrErr['order_email'] = "このメールアドレスはすでに登録されています。<br>";
    }
        
    return $objErr->arrErr;
}

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

//-----------------------------NONMEMBER関数群▼------------------------------------------------------------------
//----　取得文字列の変換
function lfConvertParam($array, $arrRegistColumn) {
    /*
     *  文字列の変換
     *  K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
     *  C :  「全角ひら仮名」を「全角かた仮名」に変換
     *  V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します 
     *  n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
     *  a :  全角英数字を半角英数字に変換する
     */
    // カラム名とコンバート情報
    foreach ($arrRegistColumn as $data) {
        $arrConvList[ $data["column"] ] = $data["convert"];
    }
    // 文字変換
    foreach ($arrConvList as $key => $val) {
        // POSTされてきた値のみ変換する。
        if(strlen(($array[$key])) > 0) {
            $array[$key] = mb_convert_kana($array[$key] ,$val);
        }
    }
    return $array;
}

//---- 入力エラーチェック
function lfErrorCheck1($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("お名前（姓）", 'name01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("お名前（名）", 'name02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" , "MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("お名前（カナ/姓）", 'kana01', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array("お名前（カナ/名）", 'kana02', STEXT_LEN), array("EXIST_CHECK", "NO_SPTAB", "SPTAB_CHECK" ,"MAX_LENGTH_CHECK", "KANA_CHECK"));
    $objErr->doFunc(array('メールアドレス', "email", MTEXT_LEN) ,array("NO_SPTAB", "EXIST_CHECK", "EMAIL_CHECK", "SPTAB_CHECK" ,"EMAIL_CHAR_CHECK", "MAX_LENGTH_CHECK", "MOBILE_EMAIL_CHECK"));

    return $objErr->arrErr;
}

//---- 入力エラーチェック
function lfErrorCheck2($array) {

    global $objConn, $objDate;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("郵便番号1", "zip01", ZIP01_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK"));
    $objErr->doFunc(array("郵便番号2", "zip02", ZIP02_LEN ) ,array("EXIST_CHECK", "SPTAB_CHECK" ,"NUM_CHECK", "NUM_COUNT_CHECK")); 
    $objErr->doFunc(array("郵便番号", "zip01", "zip02"), array("ALL_EXIST_CHECK"));

    $objErr->doFunc(array("性別", "sex") ,array("SELECT_CHECK", "NUM_CHECK")); 
    $objErr->doFunc(array("生年月日 (年)", "year", 4), array("EXIST_CHECK", "SPTAB_CHECK", "NUM_CHECK", "NUM_COUNT_CHECK"));
    if (!isset($objErr->arrErr['year'])) {
        $objErr->doFunc(array("生年月日 (年)", "year", $objDate->getStartYear()), array("MIN_CHECK"));
        $objErr->doFunc(array("生年月日 (年)", "year", $objDate->getEndYear()), array("MAX_CHECK"));
    }
    $objErr->doFunc(array("生年月日 (月日)", "month", "day"), array("SELECT_CHECK"));
    if (!isset($objErr->arrErr['year']) && !isset($objErr->arrErr['month']) && !isset($objErr->arrErr['day'])) {
        $objErr->doFunc(array("生年月日", "year", "month", "day"), array("CHECK_DATE"));
    }
    
    return $objErr->arrErr;
}


//---- 入力エラーチェック
function lfErrorCheck3($array) {

    global $objConn;
    $objErr = new SC_CheckError($array);
    
    $objErr->doFunc(array("都道府県", 'pref'), array("SELECT_CHECK","NUM_CHECK"));
    $objErr->doFunc(array("市区町村", "addr01", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("番地", "addr02", MTEXT_LEN), array("EXIST_CHECK","SPTAB_CHECK" ,"MAX_LENGTH_CHECK"));
    $objErr->doFunc(array("電話番号1", 'tel01'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("電話番号2", 'tel02'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("電話番号3", 'tel03'), array("EXIST_CHECK","SPTAB_CHECK" ));
    $objErr->doFunc(array("電話番号", "tel01", "tel02", "tel03",TEL_ITEM_LEN) ,array("TEL_CHECK"));
    
    return $objErr->arrErr;
}

// 郵便番号から住所の取得
function lfGetAddress($zipcode) {
    global $arrPref;

    $conn = new SC_DBconn(ZIP_DSN);

    // 郵便番号検索文作成
    $zipcode = mb_convert_kana($zipcode ,"n");
    $sqlse = "SELECT state, city, town FROM mtb_zip WHERE zipcode = ?";

    $data_list = $conn->getAll($sqlse, array($zipcode));

    // インデックスと値を反転させる。
    $arrREV_PREF = array_flip($arrPref);

    /*
        総務省からダウンロードしたデータをそのままインポートすると
        以下のような文字列が入っているので   対策する。
        ・（１・１９丁目）
        ・以下に掲載がない場合
    */
    $town =  $data_list[0]['town'];
    $town = ereg_replace("（.*）$","",$town);
    $town = ereg_replace("以下に掲載がない場合","",$town);
    $data_list[0]['town'] = $town;
    $data_list[0]['state'] = $arrREV_PREF[$data_list[0]['state']];

    return $data_list;
}
//NONMEMBER_関数群---------------------------------------------------------------------------------------
?>
         
?>