<?php

print("test");
  MyFlush();


sleep(2);

for($i = 0; $i < 100; $i++){
	print("aa");
	MyFlush();
}

function MyFlush() {
  flush();
  ob_end_flush();
  ob_start();
}


?>