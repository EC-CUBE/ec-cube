<?php
/*
 * Copyright ¢í 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
print($_SERVER['HTTPS']);	

$val = "Now()";

if(eregi("now\(\)", $val)) {
	print("onaji");
}

phpinfo();
	
?>