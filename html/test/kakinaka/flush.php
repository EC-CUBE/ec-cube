<?php

print("test");
  MyFlush();


sleep(2);

print("aa");
  MyFlush();

function MyFlush() {
  flush();
  ob_end_flush();
  ob_start();
}


?>