<?php

print("test");
  MyFlush();

for($i = 0; $i < 100; $i++){
	print("aa<br>");
	MyFlush();
}

function MyFlush() {
	flush();
}


?>