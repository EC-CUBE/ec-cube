<?
	for($i = 0; $i < 2048; $i++) {
		$path = "/var/tmp/test." . $i;
		$fp = fopen($path, "w");
		fwrite($fp, "test");
	}
	
	sleep(10);
	
	print("ok");
	
	
?>