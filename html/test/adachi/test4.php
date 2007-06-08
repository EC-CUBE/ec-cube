<?php

$str = 'test@<script>alert(document.cookie)</script>.com;';
$pt = "/<script.*?>|<\/script>/";

if (preg_match_all($pt, $str, $match)) {
    print('ok');
}



var_dump($match);

