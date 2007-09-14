<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir."/../require.php");
$conn = new SC_DbConn();
$objSite = new SC_SiteInfo($conn);

if(MELMAGA_SEND != true) {
    exit;
}

//�ꥢ�륿�����ۿ��⡼�ɤ�����ΤȤ�
if($_GET['mode'] == 'now') {
    //----��̤�����ǡ������������
    $time_data = $conn->getAll( "SELECT send_id FROM dtb_send_history  WHERE complete_count = 0 AND del_flg = 0 ORDER BY send_id ASC, start_date ASC" );
} else {
    
    // postgresql �� mysql �Ȥ�SQL��櫓��
    if (DB_TYPE == "pgsql") {
        $sql = "SELECT send_id FROM dtb_send_history  ";
        $sql.= "WHERE start_date  BETWEEN current_timestamp + '- 5 minutes' AND current_timestamp + '5 minutes' AND del_flg = 0 ORDER BY send_id ASC, start_date ASC";
    }else if (DB_TYPE == "mysql") {
        $sql = "SELECT send_id FROM dtb_send_history  ";
        $sql.= "WHERE start_date  BETWEEN date_add(now(),INTERVAL -5 minute) AND date_add(now(),INTERVAL 5 minute) AND del_flg = 0 ORDER BY send_id ASC, start_date ASC";
    }   
    //----��30ʬ���Cron���������֥ǡ�����ǧ
    $time_data = $conn->getAll($sql);
}

//̤�����᡼��ο�
$count = count($time_data);

//̤�����᡼�뤬���������������³���롣�ʤ�������Ǥ��롣
if( $count > 0 ){
    print("start sending <br />\n");

} else {
    print("not found <br />\n");
    exit;
}

//---- �᡼����������
for( $i = 0; $i < $count; $i++ ) {

    $sql = "SELECT * FROM dtb_send_customer WHERE send_id = ? AND (send_flag = 2 OR send_flag IS NULL)";
    $list_data[] = $conn->getAll( $sql, array( $time_data[$i]["send_id"] ) );
    
    $sql = "SELECT * FROM dtb_send_history WHERE send_id = ?";
    $mail_data[] = $conn->getAll( $sql, array( $time_data[$i]["send_id"] ) );
    
}

//---- ������̥ե饰��SQL
$sql_flag ="UPDATE dtb_send_customer SET send_flag = ? WHERE send_id = ? AND customer_id = ?";
$objMail = new GC_SendMail();



//----���᡼������������
for( $i = 0; $i < $count; $i++ ) {

    for( $j = 0; $j < count( $list_data[$i] ); $j ++ ) {

        $customerName = "";
        $mailBody = "";
        $sendFlag = "";

		//-- �ܵ�̾���Ѵ�
		$name = trim($list_data[$i][$j]["name"]);
		
		if ($name == "") {
			$name = "����";
		}
		
		$customerName = htmlspecialchars($name);
		$subjectBody = ereg_replace( "{name}", $customerName , $mail_data[$i][0]["subject"] );
		$mailBody = ereg_replace( "{name}", $customerName ,  $mail_data[$i][0]["body"] );


        //-- ���ޥ��ۿ���֥쥤��Ϣ�ȤǹԤ����
        if(lfGetBlayn()){
	        
	        //-- ʸ�������ܸ������
	        Mb_language( "Japanese" );
	              
            //-- ��������᡼������Ƥ�������
            $sendResut = array( 
                          "to" => $list_data[$i][$j]["email"]        //���ܵҰ��� 
	 	            ,"subject" => mb_encode_mimeheader($subjectBody) //��Subject  
	 	               ,"from" => $objSite->data["email03"]          //���������᡼�륢�ɥ쥹 
                  ,"replay_to" => $objSite->data["email03"]          //��reply_to 
                ,"return_path" => $objSite->data["email04"]          //��return_path
                                                                       );
            //-- ��å������ι���
            $html_param['head_charset'] = "ISO-2022-JP";
            $html_param['html_encoding'] = "ISO-2022-JP";
            $html_param['html_charset'] = "JIS";
            
            //-- �֥쥤��SMTP�����С�IP���ɥ쥹 
            $sql = "SELECT blayn_ip FROM dtb_blayn";
            $host = $conn->getAll($sql);
            $param = array(   
                       'host' => $host[0][blayn_ip]
                      ,'port' => SMTP_PORT_BLAYN                  
                                                  );

	 	    //-- Mail_mime���֥������Ⱥ���
            $mail_mimeObj = new Mail_mime();
            
            //-- Mail_mime���֥������Ȥ�HTML����ʸ���ɲ�
            $mailBody = mb_convert_encoding($mailBody, "JIS", CHAR_CODE);
            $mail_mimeObj->setHTMLBody($mailBody);
            
            $body = $mail_mimeObj->get($html_param);
            $header = $mail_mimeObj->headers($sendResut);
            
            //-- PEAR::Mail��Ȥäƥ᡼���������֥������Ⱥ���
            $mailObj =& Mail::factory("smtp", $param);
            // �᡼������
            $result = $mailObj->send($sendResut["to"], $header, $body);

		} else {
	        //-- �ƥ����ȥ᡼���ۿ��ξ��
	        if( $mail_data[$i][0]["mail_method"] == 2 ) {

		        $sendResut = MAIL_SENDING(
									     $list_data[$i][$j]["email"]				//���ܵҰ���
									    ,$subjectBody								//��Subject
									    ,$mailBody									//���᡼����ʸ
									    ,$objSite->data["email03"]					//���������᡼�륢�ɥ쥹
									    ,$objSite->data["company_name"]				//��������̾
									    ,$objSite->data["email03"]					//��reply_to
									    ,$objSite->data["email04"]					//��return_path
									    ,$objSite->data["email04"]					//��errors_to
																		 );
																		 

            //--  HTML�᡼���ۿ��ξ��  
            } elseif( $mail_data[$i][0]["mail_method"] == 1 || $mail_data[$i][0]["mail_method"] == 3) {
            
                $sendResut = HTML_MAIL_SENDING(
                                             $list_data[$i][$j]["email"]
                                            ,$subjectBody
                                            ,$mailBody
                                            ,$objSite->data["email03"]                  //���������᡼�륢�ɥ쥹
                                            ,$objSite->data["company_name"]             //��������̾
                                            ,$objSite->data["email03"]                  //��reply_to
                                            ,$objSite->data["email04"]                  //��return_path
                                            ,$objSite->data["email04"]                  //��errors_to
                                                                     );
            }
        }
  
        //-- ������λ�ʤ�1�����Ԥʤ�-1��᡼��������̥ե饰�Ȥ���DB������
        if( ! $sendResut ){
            $sendFlag = "-1";
        } else {
            $sendFlag = "1";
            
            // ��λ�� 1 ���䤹
            $sql = "UPDATE dtb_send_history SET complete_count = complete_count + 1 WHERE send_id = ?";
            $conn->query( $sql, array($mail_data[$i][0]["send_id"]));
        }

        $conn->query( $sql_flag, array( $sendFlag, $mail_data[$i][0]["send_id"], $list_data[$i][$j]["customer_id"] ) );
        
        

    }

    //--- �᡼������������λ��ν���
    $completeSql = "UPDATE dtb_send_history SET end_date = now() WHERE send_id = ?";
    $conn->query( $completeSql, array( $time_data[$i]["send_id"] ) );

    //---��������λ�����᡼��
    $compData =  date("Yǯm��d��H��iʬ" . "  �����᡼����ۿ�����λ���ޤ�����" );

    HTML_MAIL_SENDING(
                     $objSite->data["email03"]  
                    ,$compData
                    ,$mail_data[$i][0]["body"]
                    ,$objSite->data["email03"]                  //���������᡼�륢�ɥ쥹
                    ,$objSite->data["company_name"]             //��������̾
                    ,$objSite->data["email03"]                  //��reply_to
                    ,$objSite->data["email04"]                  //��return_path
                    ,$objSite->data["email04"]                  //��errors_to
                 );
               
    if ($_GET['mode'] = "now") {
        header("Location: " . URL_DIR . "admin/mail/history.php");
    }
    echo "complete\n";

}


//--- �ƥ����ȥ᡼���ۿ�
function MAIL_SENDING( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {

    $mail_obj = new GC_SendMail();  
    $mail_obj->setItem( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc );
        
    if( $mail_obj->sendMail() ) {
        return true;
    }
    
}

//--- HTML�᡼���ۿ�
function HTML_MAIL_SENDING( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to="", $bcc="", $cc ="" ) {

    $html_mail_obj = new GC_SendMail();
    $html_mail_obj->setItemHtml( $to, $subject, $body, $fromaddress, $from_name, $reply_to, $return_path, $errors_to, $bcc, $cc );
            
    if( $html_mail_obj->sendHtmlMail() ) {
        return true;    
    }
    
}

// �֥쥤�󥨥󥸥�����Ѥߤ���ǧ
function lfGetBlayn() {
    
    $objQuery = new SC_Query();
    
    $arrRet[now_version] = $objQuery->count("dtb_module", "now_version = (SELECT now_version FROM dtb_module WHERE main_php='blayn/blayn.php')");
    $arrRet[blayn_ip] = $objQuery->count("dtb_blayn");
    
    if (!empty($arrRet[now_version]) && !empty($arrRet[blayn_ip])) {
        return true;
    } else {
        return false;
    }
}
?>