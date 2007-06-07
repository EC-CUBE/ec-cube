<?php
/**
 * Scriptタグをエスケープ
 * 全てのページに適用される
 *
 * @param string $value 入力
 * @return string 出力
 */
function smarty_modifier_script_escape($value) {
    
    if (empty($value)) { return; }
    
    return preg_replace("/<script.*?>|<\/script>/", '&lt;script&gt;', $value);
}
?>
