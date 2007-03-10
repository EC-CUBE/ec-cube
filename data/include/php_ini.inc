<?php

ini_set("mbstring.language", "Japanese");
ini_set("mbstring.http_input", CHAR_CODE);
ini_set("mbstring.http_output", CHAR_CODE);
ini_set("auto_detect_line_endings", 1);
ini_set("default_charset", CHAR_CODE);
ini_set("mbstring.internal_encoding", CHAR_CODE);
ini_set("mbstring.detect_order", "auto");
ini_set("mbstring.substitute_character", "none");

// 移行はINI_SETで変更可能かどうかが微妙なので .htaccess で変更する
/*
ini_set("mbstring.language", "Japanese");
ini_set("output_handler", "mb_output_handler");
ini_set("mbstring.encoding_translation", 1);
ini_set("magic_quotes_gpc", 0);
*/

?>