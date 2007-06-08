<?php

$str = 'test@<script>alert(document.cookie)</script>.com;';
$pt = "/<script.*?>|<\/script>/";

preg_match_all($pt, $str, $match);



var_dump($match);

