<?php
/**
 * Scriptタグをエスケープする
 *
 * @param  string $value 入力
 * @return string $value マッチした場合は変換後の文字列、しない場合は入力された文字列をそのまま返す。
 */
function smarty_modifier_script_escape($value) {
    
    if (is_array($value)) {return $value;}
    
    $pattern = "/<script.*?>|<\/script>/";
    $convert = "#####";
    
    if ( preg_match_all($pattern, $value, $matches) ) {
        return preg_replace($pattern, $convert, $value);
    } else {
        return $value;
    }
}
?>
