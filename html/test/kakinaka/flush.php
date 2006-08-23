<?php

print("test");
  MyFlush();


sleep(2);

print("aa");

function MyFlush() {
  flush();
  ob_end_flush();
  ob_start();
}


?>