<?php

$fp = fopen("test.txt", 'r');
while (!feof($fp)) {
	$ret = fgets($fp, 1024);
	$ret = ereg_replace("\n", "", $ret);
	
	if($ret != "" && !ereg("^-", $ret)) {
		print("#$ret\n");
		print("define host {\n");
		print("\tuse\tgeneric-host\n");
		print("\thost_name\t$ret\n");
		print("\talias\t$ret\n");
		print("\taddress\t$ret\n");
		print("}\n");
		print("\n");
		$line.= $ret . ",";		
	}
}
fclose($fp);

print($line);

?>