<?php
	foreach($_POST as $key => $val){
		if(ereg("^item", $key)) {
			if($val == 'in') {
				print($key . "�����ե졼�����¸�ߤ��ޤ���</br>\n");
			}
		}
	}
?>