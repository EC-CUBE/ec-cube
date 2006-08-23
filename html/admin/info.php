<?php
print($_SERVER['HTTPS']);	

$val = "Now()";

if(eregi("now\(\)", $val)) {
	print("onaji");
}

phpinfo();
	
?>