<?php
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir."/../require.php");
$conn = new SC_DbConn();
$objSite = new SC_SiteInfo($conn);

if($_GET['mode'] == 'now') {
	//----　未送信データを取得する
	$time_data = $conn->getAll( "SELECT send_id FROM dtb_send_history  WHERE complete_count = 0 AND del_flg = 0 ORDER BY send_id ASC, start_date ASC" );
} else {
	$sql = "SELECT send_id FROM dtb_send_history  ";
	$sql.= "WHERE start_date  BETWEEN current_timestamp + '- 5 minutes' AND current_timestamp + '5 minutes' AND del_flg = 0 ORDER BY send_id ASC, start_date ASC";
	//----　30分毎にCronが送信時間データ確認
	$time_data = $conn->getAll($sql);
}

$count = count($time_data);

if( $count > 0 ){
	print("start sending <br />\n");
} else {
	print("not found <br />\n");
	exit;
}

//---- メール送信
for( $i = 0; $i < count( $time_data ); $i++ ) {

	$sql = "SELECT * FROM dtb_send_customer WHERE send_id = ? AND (send_flag = 2 OR send_flag IS NULL)";
	$list_data[] = $conn->getAll( $sql, array( $time_data[$i]["send_id"] ) );
	
	$sql = "SELECT * FROM dtb_send_history WHERE send_id = ?";
	$mail_data[] = $conn->getAll( $sql, array(  $time_data[$i]["send_id"] ) );

}

//---- 送信結果フラグ用SQL
$sql_flag ="UPDATE dtb_send_customer SET send_flag = ? WHERE send_id = ? AND customer_id = ?";
$objMail = new GC_SendMail();

//----　メール生成と送信
for( $i = 0; $i < count( $time_data ); $i++ ) {

	for( $j = 0; $j < count( $list_data[$i] ); $j ++ ) {

		$customerName = "";
		$mailBody = "";
		$sendFlag = "";

		//-- 顧客名の変換
		$name = trim($list_data[$i][$j]["name"]);
		
		if ($name == "") {
			$name = "お客";
		}
		
		$customerName = htmlspecialchars($name);
		$subjectBody = ereg_replace( "{name}", $customerName , $mail_data[$i][0]["subject"] );
		$mailBody = ereg_replace( "{name}", $customerName ,  $mail_data[$i][0]["body"] );

		//-- テキストメール配信の場合	
		if( $mail_data[$i][0]["mail_method"] == 2 ) {

			$sendResut = MAIL_SENDING(
										 $list_data[$i][$j]["email"]				//　顧客宛先
										,$subjectBody								//　Subject
										,$mailBody									//　メール本文
										,$objSite->data["email03"]					//　送信元メールアドレス
										,$objSite->data["company_name"]				//　送信元名
										,$objSite->data["email03"]					//　reply_to
										,$objSite->data["email04"]					//　return_path
										,$objSite->data["email04"]					//　errors_to
																			 );

		//--  HTMLメール配信の場合	
		} elseif( $mail_data[$i][0]["mail_method"] == 1 || $mail_data[$i][0]["mail_method"] == 3) {
			
			$sendResut = HTML_MAIL_SENDING(
											 $list_data[$i][$j]["email"]
											,$subjectBody
											,$mailBody
											,$objSite->data["email03"]					//　送信元メールアドレス
											,$objSite->data["company_name"]				//　送信元名
											,$objSite->data["email03"]					//　reply_to
											,$objSite->data["email04"]					//　return_path
											,$objSite->data["email04"]					//　errors_to
																	 );
		}

	
		//-- 送信完了なら1、失敗なら0をメール送信結果フラグとしてDBに挿入
		if( ! $sendResut ){
			 $sendFlag = "-1";
		} else {
			$sendFlag = "1";
			
			// 完了を1こ増やす
			$sql = "UPDATE dtb_send_history SET complete_count = complete_count + 1 WHERE send_id = ?";
			$conn->query( $sql, array($mail_data[$i][0]["send_id"]) );
		}

		$conn->query( $sql_flag, array( $sendFlag, $mail_data[$i][0]["send_id"], $list_data[$i][$j]["customer_id"] ) );
		
		

	}

	//--- メール全件送信完了後の処理
	$completeSql = "UPDATE dtb_send_history SET end_date = now() WHERE send_id = ?";
	$conn->query( $completeSql, array( $time_data[$i]["send_id"] ) );

	//---　送信完了　報告メール
	$compData =  date("Y年m月d日H時i分" . "  下記メールの配信が完了しました。" );
	MAIL_SENDING(
					 $objSite->data["email03"]	
					,$compData
					,$mail_data[$i][0]["body"]
					,$objSite->data["email03"]					//　送信元メールアドレス
					,$objSite->data["company_name"]				//　送信元名
					,$objSite->data["email03"]					//　reply_to
					,$objSite->data["email04"]					//　return_path
					,$objSite->data["email04"]					//　errors_to
				 );
	
	if ($_GET['mode'] = "now") {
		header("Location: /admin/mail/history.php");
	}
	echo "complete\n";

}


//--- テキストメール配信
function MAIL_SENDING( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {


	$mail_obj = new GC_SendMail();	
	$mail_obj->setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc );
		
	if( $mail_obj->sendMail() ) {
		return true;
	}
	
}

//--- HTMLメール配信
function HTML_MAIL_SENDING( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {


	$html_mail_obj = new GC_SendMail();
	$html_mail_obj->setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc );

			
	if( $html_mail_obj->sendHtmlMail() ) {
		return true;	
	}
	
}


?>