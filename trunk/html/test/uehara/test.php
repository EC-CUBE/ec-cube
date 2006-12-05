<?php
$test= 'aaa\\ ';
if(EregI("[\\]", $test)) {
	echo "true!!";
}