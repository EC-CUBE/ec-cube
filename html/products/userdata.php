<?php

/*�����ʾܺ٥ڡ�����HTML������ʬ�ƥ�ץ졼��ɽ���ѥե����� */

$objUserView = new SC_UserView(TEMPLATE_FTP_DIR, COMPILE_FTP_DIR);
//HTML�����ѤΥƥ�ץ졼�ȥե�����̾
$tpl_name = "products_detail_share.tpl";
//�ե�����¸�ߥ����å�
if(file_exists(TEMPLATE_FTP_DIR . $tpl_name)) {
	$objUserView->display($tpl_name);
}

?>