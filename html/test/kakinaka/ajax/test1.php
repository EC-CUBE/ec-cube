<?php
/*
	//POST�����
	$filename = $_POST['fn'] ;
	
	//�ե�����ǡ������ɤ߹���
	$data = file_get_contents('./'.$filename.'.txt');
	
	//URI���󥳡���
	$data  = rawurlencode($data);

	
	
*/
	$data = $_POST['fn'];
	
	//����charset��ut-8��
	mb_http_output ( 'EUC-JP' );
	//�إå�
	header ("Content-Type: text/html; charset=EUC-JP"); 
	//����
	echo($data);
?> 
