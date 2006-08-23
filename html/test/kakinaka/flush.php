<?php

print("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaatest");
  flush();
  ob_end_flush();
  ob_start();


sleep(2);

print("aa");
  flush();
  ob_end_flush();
  ob_start();


?>