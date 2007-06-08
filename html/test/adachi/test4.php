<?php

$str = 'test@<script>alert(document.cookie)</script>.com;';
$pt = "/<script.*?>|<\/script>/";

preg_match_all($pt, $str, $match)

//$p = preg_replace("/<script.*?>|<\/script>/", '&lt;script&gt;', $value);

var_dump($match);

