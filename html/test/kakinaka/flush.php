<?php

print("test");
  MyFlush();

for($i = 0; $i < 1; $i++){
	print("aa");
	MyFlush();
}

function MyFlush() {
	flush();
	ob_end_flush();
	ob_start();
	sleep(1);
}


?>