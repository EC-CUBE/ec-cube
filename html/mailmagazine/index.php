<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/mailmagazine/index.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'mailmagazine/index.tpl';			// メインテンプレート
		$this->tpl_page_category = 'mailmagazine';
		$this->tpl_title = 'メルマガ登録･解除';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query(); 

$entry_email = strtolower($_POST['entry']);			//登録・更新メールアドレスフォーム入力フォーム値
$stop_email = strtolower($_POST['stop']);			//削除メールアドレスフォーム入力値
$checkbox = $_POST['kind'];							//登録・更新メルマガ形式チェックボックス

$arrErr = lfErrorCheck();
$objPage->arrErr = $arrErr;

$entry_email_subject = sfMakeSubject('メルマガ仮登録が完了しました。');
$stop_email_subject = sfMakeSubject('メルマガ仮解除が完了しました。');

//登録重複チェック（重複なし：0  重複あり:1)
$ent_flag = $objQuery->count("dtb_customer_mail", "email = ? AND mail_flag=? " ,array($entry_email,$checkbox));

//更新確認（更新必要:1　更新不必要:2)
$update_flag = $objQuery->count("dtb_customer_mail", "email = ? AND NOT mail_flag = ? ",array($entry_email,$checkbox));

//解除POST値のメールアドレスの存在チェック
$stop_email_flag = $objQuery->count("dtb_customer_mail", "email = ? ",array($stop_email));
//仮登録テーブルチェック
$ent_temp_flag = $objQuery->count("dtb_customer_mail_temp", "email = ? " ,array($entry_email));
$stop_temp_flag = $objQuery->count("dtb_customer_mail_temp", "email = ? " ,array($stop_email));
//本テーブルから登録POST値のメールアドレスを取得
$arrRetEnt = $objQuery->select("*","dtb_customer_mail","email = ?",array($entry_email));
$arrEmailEnt = $arrRetEnt[0]['email'];

//登録されているかどうか
$email_flag = $objQuery->count("dtb_customer_mail","email = ?",array($entry_email));

//本テーブルから解除POST値のメールアドレスを取得
$arrRetStop = $objQuery->select("*","dtb_customer_mail" , " email = ? ",array($stop_email));
$arrEmailStop = $arrRetStop[0]['email'];

//完了メッセージの初期化
$mes1="";
$mes2="";

//ランダムID生成		
$randomid = sfGetUniqRandomId();

foreach($_POST as $key => $val) {
	
	switch ($key) {
	case 'entry':
	if (count($arrErr) == ""){
		//登録がなければ、仮登録実行
		if ($email_flag == 0){
			$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
			$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
				switch ($checkbox){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;
					case '2':
					$objPage->tpl_kindname = "テキスト";
					break;
				}
			$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
			$objPage->tpl_name = "登録";
			$objPage->tpl_email = $entry_email;
			$objPage->tpl_mailtitle = "メルマガ仮登録が完了しました。";
			sfSendTplMail($entry_email, $entry_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
			
			if ($ent_temp_flag == 0){				
			$sql = "INSERT INTO dtb_customer_mail_temp";
			$sql.= "(email,mail_flag,temp_id,end_flag) VALUES ('$entry_email' , '$checkbox' ,'$randomid','0')";
			$objQuery->exec($sql);
			}else{
			$sql = "UPDATE dtb_customer_mail_temp SET temp_id = '$randomid' , mail_flag='$checkbox' , end_flag='0'";
			$sql.= "WHERE email = ?";
			$objQuery->exec($sql,array($entry_email));
			}
			//仮会員の場合
		}elseif (($email_flag == 1) && ($arrRetEnt[0]['mail_flag'] >= 4)){
			$objPage->arrErr['entry'] = "※　会員登録が終了していません。先に本会員登録を済ませてください。";
		
		//正会員で、既に登録されているメールアドレス及び配信形式の場合
		}elseif ($ent_flag == 1){
			$objPage->arrErr['entry']= "※　既に登録されているメールアドレス及び配信形式です。";
			
		//正会員で、既にメルマガ配信を変更したことがあり、配信形式が二重でなければ、配信形式変更・再配信実行（仮テーブル更新）
		}else{
			if (($email_flag == 1) && ($update_flag == 1)  && ($arrRetEnt[0]['mail_flag'] <= 3) && ($ent_temp_flag != 0)){
			$sql = "UPDATE dtb_customer_mail_temp SET temp_id = '$randomid' , mail_flag='$checkbox' , end_flag='0'";
			$sql.= "WHERE email = ?";
			$objQuery->exec($sql,array($entry_email));
			}else{
				//  正会員で、一度もメルマガ配信を変更したことがなく、配信形式が二重でなければ、配信形式変更・再配信実行（仮テーブル登録）
				if (($email_flag ==1) && ($update_flag == 1) && ($arrRetEnt[0]['mail_flag'] <= 3) && ($ent_temp_flag == 0)){
					$sql = "INSERT INTO dtb_customer_mail_temp";
					$sql.= "(email,mail_flag,temp_id,end_flag) VALUES ('$entry_email' , '$checkbox' ,'$randomid','0')";
					$objQuery->exec($sql);
				}
			}
			$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
			$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
			
			switch($arrRetEnt[0]['mail_flag']){
				
			case '1':
			$objPage->tpl_kindname = "HTML→テキスト";
			break;
			
			case '2':
			$objPage->tpl_kindname = "テキスト→HTML";
			
			break;
			
			case '3':
				switch($checkbox){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;	
					case '2':
					$objPage->tpl_kindname = "テキスト";
					break;
				}	
			break;
			
			}
			$objPage->tpl_email = $entry_email;
			$objPage->tpl_name = "登録";
			$objPage->tpl_mailtitle = "メルマガ仮登録が完了しました。";
			$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
			sfSendTplMail($entry_email, $entry_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
		}
			$mes1.="メルマガ仮登録が完了しました。";
			$mes2.="確認メールを送付しましたのでご確認ください。";
	}
		break;
		
	case 'stop':
	if (count($arrErr) == ""){									
		if ($stop_email_flag == 1){					//本登録されていれば
			if ($arrRetStop[0]['mail_flag'] <= 2){
				switch ($stop_temp_flag){
					case '0':	//　本会員登録後、初めてメルマガ配信停止を希望したとき
					$sql= "INSERT INTO dtb_customer_mail_temp";
					$sql.="(email,mail_flag,temp_id,end_flag) VALUES ('$stop_email','3','$randomid','0')";
					$objQuery->exec($sql);
					break;
					
					case '1':	// 過去にメルマガ履歴を変更したことがあるとき
					$sql = "UPDATE dtb_customer_mail_temp SET temp_id='$randomid' , mail_flag='3' , end_flag='0' ";
					$sql.= "WHERE email = ? ";
					$objQuery->exec($sql,array($stop_email));
					break;
				}
						//削除
				$mes1.="メルマガ仮解除が完了しました。";
				$mes2.="確認メールを送付しましたのでご確認ください。";
				$objPage->tpl_css = '/css/layout/mailmagazine/complete.css';
				$objPage->tpl_mainpage =  "mailmagazine/complete.tpl";
				switch ($arrRetStop[0]['mail_flag']){
					case '1':
					$objPage->tpl_kindname = "HTML";
					break;
					case '2':
					$objPage->tpl_kindname = "テキスト";
					break;
				}
				$objPage->tpl_mailtitle = "メルマガ仮解除が完了しました。";
				$objPage->tpl_email = $stop_email;
				$objPage->tpl_name = "解除";
				$objPage->tpl_url = SSL_URL."mailmagazine/regist.php?temp_id=$randomid";
				sfSendTplMail($stop_email, $stop_email_subject, "mail_templates/mailmagazine_temp.tpl", $objPage);
			}elseif ($arrRetStop[0]['mail_flag'] >= 4){
				$objPage->arrErr['stop']= "※　会員登録が終了していません。先に本会員登録を済ませてください。";
			}else{
				$objPage->arrErr['stop']= "※　既に配信停止されているメールアドレスです。";
			}
		}else{
				$objPage->arrErr['stop']= "※　登録されていないメールアドレスです。";
		}
	}
		break;
}

}

$objPage->mes1 = $mes1;
$objPage->mes2 = $mes2;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);					

//-----------------------------------------------------------------------------------------------------------------------------------


//エラーチェック

function lfErrorCheck() {
	$objErr = new SC_CheckError();
		switch ($_POST['mode']) {
			case 'entry':
			$objErr->doFunc(array("登録メールアドレス", "entry", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"));
			break;
	
			case 'stop':
			$objErr->doFunc(array("配信停止メールアドレス", "stop", STEXT_LEN), array("EXIST_CHECK", "SPTAB_CHECK", "EMAIL_CHECK", "MAX_LENGTH_CHECK"));
			break;
		}
	return $objErr->arrErr;
}

/* 取得文字列の変換 */
function lfConvertParam($array) {
	/*
	 *	文字列の変換
	 *	K :  「半角(ﾊﾝｶｸ)片仮名」を「全角片仮名」に変換
	 *	C :  「全角ひら仮名」を「全角かた仮名」に変換
	 *	V :  濁点付きの文字を一文字に変換。"K","H"と共に使用します	
	 *	n :  「全角」数字を「半角(ﾊﾝｶｸ)」に変換
	 */
	// 人物基本情報
	
	// スポット商品
	$arrConvList['name'] = "KVa";
	$arrConvList['main_list_comment'] = "KVa";
	$arrConvList['price01'] = "n";
	$arrConvList['price02'] = "n";
	$arrConvList['stock'] = "n";
	$arrConvList['sale_limit'] = "n";
	$arrConvList['point_rate'] = "n";
	$arrConvList['product_code'] = "KVna";
	$arrConvList['deliv_fee'] = "n";

	// 文字変換
	foreach ($arrConvList as $key => $val) {
		// POSTされてきた値のみ変換する。
		if(isset($array[$key])) {
			$array[$key] = mb_convert_kana($array[$key] ,$val);
		}
	}
	
	global $arrSTATUS;
	$array['product_flag'] = sfMergeCheckBoxes($array['product_flag'], count($arrSTATUS));
	
	return $array;
}
?>
