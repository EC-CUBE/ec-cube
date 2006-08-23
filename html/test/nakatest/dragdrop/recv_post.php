<?php
	foreach($_POST as $key => $val){
		if(ereg("^item", $key)) {
			if($val == 'in') {
				print($key . "が、フレーム内に存在します。</br>\n");
			}
		}
	}
?>