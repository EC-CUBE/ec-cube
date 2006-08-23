<?php

print("test");
  MyFlush();

for($i = 0; $i < 1000; $i++){
	print("aa<br>");
	MyFlush();
}

function MyFlush() {
	flush();
}


?>