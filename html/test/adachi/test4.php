<?php
$value = true;
$p = preg_replace("/<script.*?>|<\/script>/", '&lt;script&gt;', $value);

var_dump($p);

