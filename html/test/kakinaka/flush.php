<?php

print("test");
  MyFlush();

for($i = 0; $i < 100; $i++){
	print("aa");
	MyFlush();
}

function MyFlush() {
	flush();
	sleep(1);
}


?>