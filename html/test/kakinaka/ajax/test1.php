<?php
/*
	//POSTを取得
	$filename = $_POST['fn'] ;
	
	//ファイルデータを読み込む
	$data = file_get_contents('./'.$filename.'.txt');
	
	//URIエンコード
	$data  = rawurlencode($data);

	
	
*/
	$data = $_POST['fn'];
	
	//出力charsetをut-8に
	mb_http_output ( 'EUC-JP' );
	//ヘッダ
	header ("Content-Type: text/html; charset=EUC-JP"); 
	//出力
	echo($data);
?> 
