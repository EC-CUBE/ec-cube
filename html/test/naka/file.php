<?
	for($i = 0; $i < 2048; $i++) {
		$path = "/var/tmp/test." . $i;
		fopen($path, "w");
	}
	
	print("ok");
?>