<?php
require_once("../require.php");

class LC_Page {
	function LC_Page() {
		/** 必ず指定する **/
		$this->tpl_css = '/css/layout/mailmagazine/complete.css';		// メインCSSパス
		/** 必ず指定する **/
		$this->tpl_mainpage = 'mailmagazine/complete.tpl';			// メインテンプレート
		$this->tpl_title = 'メルマガ登録･解除';
	}
}

$objPage = new LC_Page();
$objView = new SC_SiteView();
$objQuery = new SC_Query();

//GETで指定したidに対応するデータを取得
$arrtmpdata = $objQuery->select("*", "dtb_customer_mail_temp", "temp_id = ?", array($_GET['temp_id']));

//不正なURL時のエラー処理(count = 0 で不正な入力)
$arrcount = $objQuery->count("dtb_customer_mail_temp","temp_id = ?", array($_GET['temp_id']));

//仮テーブルのメールアドレスを取得 $arremail
$arremail = $arrtmpdata[0]['email'];			
//仮テーブルのフラグ　$arrflag　(1:HTML 2:テキスト 3:配信停止)
$arrflag = $arrtmpdata[0]['mail_flag'];
//本テーブルで、メールアドレスの登録チェック
$arrcnt = $objQuery->count("dtb_customer_mail" ,"email=?",array($arremail));
//本テーブルのフラグ
$arrsel = $objQuery->select("*", "dtb_customer_mail", "email LIKE ?", array($arremail));
			
//処理メッセージ初期化
$mes1="";			
$mes2="";

//件名のテンプレートに渡すメッセージ
$ent_subject = sfMakeSubject('メルマガ本登録が完了しました。');
$stop_subject= sfMakeSubject('メルマガ解除が完了しました。');

if ($arrcount == 0){
	$mes1 = "認証に失敗しました。";
	$mes2 = "確認メールのURLをお確かめ下さい。";
}elseif ($arremail != "" && $arrtmpdata[0]['end_flag'] == 1 && $arrflag <= 2){
	$mes1 = "既にメルマガ登録されているメールアドレスです。";
	$mes2 = "内容を変更される場合は、「メルマガ登録・解除」フォームよりお願い致します。";
}elseif ($arremail != "" && $arrtmpdata[0]['end_flag'] == 1 && $arrflag == 3){
	$mes1 = "既にメルマガ解除されているメールアドレスです。";
	$mes2 = "内容を変更される場合は、「メルマガ登録・解除」フォームよりお願い致します。";
}else{

//仮テーブルの内容チェック

 switch ($arrflag){
	case '1':
	if ($arrcnt == 0){
		$entry_sql = "INSERT INTO dtb_customer_mail (email,mail_flag) VALUES ('$arremail' , '1')";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' WHERE email = ?";
		$objQuery->exec($entry_sql);
		$objQuery->exec($flag_sql,array($arremail));
	}else{
		$change_sql= "UPDATE dtb_customer_mail SET mail_flag='1' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ? ";
		$objQuery->exec($change_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
	}
		$mes1.="メルマガ本登録が完了しました。";
		$mes2.="完了メールを送付しましたのでご確認ください。";
		$objPage->tpl_mailtitle = "メルマガ本登録が完了しました。";
		$objPage->tpl_email = $arremail;
		$objPage->tpl_name = "登録";
		$objPage->tpl_kindname = "テキスト→HTML";
		sfSendTplMail($arremail, $ent_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
	break;

	case '2':
	if ($arrcnt == 0){
		$entry_sql = "INSERT INTO dtb_customer_mail (email,mail_flag) VALUES ('$arremail' , '2')";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' WHERE email = ?";
		$objQuery->exec($entry_sql);
		$objQuery->exec($flag_sql,array($arremail));
	}else{
		$change_sql= "UPDATE dtb_customer_mail SET mail_flag='2' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ? ";
		$objQuery->exec($change_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
	}
		$objPage->tpl_mailtitle = "メルマガ本登録が完了しました。";
		$objPage->tpl_name = "登録";
		$objPage->tpl_email = $arremail;
		$objPage->tpl_kindname = "HTML→テキスト";
		$mes1.="メルマガ本登録が完了しました。";
		$mes2.="完了メールを送付しましたのでご確認ください。";
		sfSendTplMail($arremail, $ent_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
	break;
	
	case '3':
	switch ($arrsel[0]['mail_flag']){
			case '1':
			$objPage->tpl_kindname = "HTML";
			break;
			case '2':
			$objPage->tpl_kindname = "テキスト";
			break;
		}
		$stop_sql = "UPDATE dtb_customer_mail SET mail_flag='3' WHERE email = ?";
		$flag_sql = "UPDATE dtb_customer_mail_temp SET end_flag='1' , update_date=now() WHERE email = ?";
		$objQuery->exec($stop_sql,array($arremail));
		$objQuery->exec($flag_sql,array($arremail));
		$objPage->tpl_mailtitle = "メルマガ解除が完了しました。";
		$objPage->tpl_name = "解除";
		$objPage->tpl_email = $arremail;
		sfSendTplMail($arremail,$stop_subject, "mail_templates/mailmagazine_comp.tpl" , $objPage);
		$mes1.="メルマガ解除が完了しました。";
		$mes2.="完了メールを送付しましたのでご確認ください。";
		break;
 }

}

$objPage->mes1 = $mes1;
$objPage->mes2 = $mes2;
$objView->assignobj($objPage);
$objView->display(SITE_FRAME);

?>

