<?
	for($i = 0; $i < 100; $i++) {
		$path = "/var/tmp/test." . $i;
		fopen($path, "w");
	}
	
	print("ok");
?>