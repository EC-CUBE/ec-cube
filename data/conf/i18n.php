<?php

// ‘Û‰»—p
define('LOCALE_DIR', DATA_PATH . 'locale/');

$accept_language = '';
if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
    $accept_language = array_shift(split(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
}
switch ($accept_language) {
case 'ja':
    $language = 'ja';
    break;
case 'ko':
case 'ko-kr':
    $language = 'ko';
    break;
case 'en':
case 'en-us':
    $language = 'en';
    break;
default:
    $language = 'C';
    break;
}
$text = file_get_contents(LOCALE_DIR . $language . '.html');
$preVar = explode('<!--end-->', $text);
foreach ($preVar AS $preVar_1) {
	$preVar_2 = explode('-->', $preVar_1);
	$preVar_3 = explode('<!--', $preVar_2[0]);
	$newVar = str_replace('', '_', $preVar_3[1]);
	#$_GLOBALS['_lang'][$newVar] = $preVar_2[1];
	define('_lang_' . $newVar, $preVar_2[1]);
}
#var_dump($accept_language);
#var_dump($language);

?>
