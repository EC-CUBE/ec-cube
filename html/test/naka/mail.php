<?php

require_once("../../admin/require.php");

			//-- 文字を日本語に設定
	        Mb_language( "Japanese" );
	              
            //-- 送信するメールの内容と送信先
            $sendResut = array( 
            	"to" => 'abnana210@softbank.ne.jp',  	 		//　顧客宛先 
	 	        "Subject" => mb_encode_mimeheader("てすと"),	//　Subject  
	 	        "From" => 'test01@lockon.co.jp',              	//　送信元メールアドレス 
                "Reply-To" => 'test02@lockon.co.jp',          	//　reply_to 
                "Return-Path" => 'test03@lockon.co.jp',         //　return_path
            );
			
            //-- Mail_mimeオブジェクトにHTMLの本文を追加
            $mailBody = mb_convert_encoding("テストです。", "JIS", CHAR_CODE);
            
            //-- Mail_mimeオブジェクト作成
            $mail_mimeObj = new Mail_mime();
                        
            //-- メッセージの構築
            $enc_param['head_charset'] = "ISO-2022-JP";
            $enc_param['html_encoding'] = "ISO-2022-JP";
            $enc_param['html_charset'] = "ISO-2022-JP";
			$mail_mimeObj->setHTMLBody($mailBody);
                        
			/*
			$enc_param['text_charset'] = "ISO-2022-JP";
            $mail_mimeObj->setTXTBody($mailBody);
            */
            
            $param = array(   
                       //'host' => '216.255.239.201',
                       'host' => '210.188.192.18',
                       'port' => 25                  
                                                  );

            /*
            	array $headers - ヘッダの連想配列。 
				ヘッダ名が配列のキー、ヘッダの値が配列の値となります。
				メールの envelope sender を書き換えたい場合は Return-Path ヘッダを設定します。 
				すると、この値が From ヘッダの値の代わりに用いられます。
           */
            
            $body = $mail_mimeObj->get($enc_param);
            $header = $mail_mimeObj->headers($sendResut);
            
            //-- PEAR::Mailを使ってメール送信オブジェクト作成
            $mailObj =& Mail::factory("smtp", $param);
            // メール送信
            $result = $mailObj->send($sendResut["to"], $header, $body);
            
			if (PEAR::isError($result)) { 
				print($result->getMessage());
			} else {
				print("ok");
			}
?>