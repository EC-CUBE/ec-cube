<?php

$str = "ああああああああああああああああああああああああああああああああああああああああああああああああああああああああinjyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△△injyuあああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああああ";

$Message_tmp = wordwrap($str,256,"<br>", 1);

print($Message_tmp);


?>