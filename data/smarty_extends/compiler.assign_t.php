<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Smarty {assign_t} compiler function plugin
 *
 * Type:     compiler function<br>
 * Name:     assign_t<br>
 * Purpose:  assign a translated value to a template variable
 * @author   pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param string containing var-attribute and value-attribute
 * @param Smarty_Compiler
 */
function smarty_compiler_assign_t($tag_attrs, &$compiler)
{
    // 多言語対応用の関数が定義されていなければエラーを出力
    if (!function_exists('t')) {
        $compiler->_syntax_error("[compiler] function 't' is not defined", E_USER_WARNING);
        return;
    }

    $params = $compiler->_parse_attrs($tag_attrs);

    // セット対象が無ければエラーを出力
    if (!isset($params['var'])) {
        $compiler->_syntax_error("assign: missing 'var' parameter", E_USER_WARNING);
        return;
    }
    $var = $params['var'];
    unset($params['var']);

    // エイリアスが無ければエラーを出力
    if (empty($params['string'])) {
        $compiler->_syntax_error("[compiler] parameter 'string' cannot be empty", E_USER_WARNING);
        return;
    }

    foreach ($params as $key => $param) {
        eval("\$params[{$key}] = {$param};");
    }

    $string = $params['string'];
    unset($params['string']);

    // オプション用の配列
    $options = array();
    // 言語コードの指定があればオプションにセット
    if (!empty($params['lang_code'])) {
        $options['lang_code'] = $params['lang_code'];
        unset($params['lang_code']);
    }
    // 機種の指定があればオプションにセット
    if (!empty($params['device_type_id'])) {
        $options['device_type_id'] = $params['device_type_id'];
        unset($params['device_type_id']);
    }

    $translated = '"' . t($string, $params, $options) . '"';

    return "\$this->assign({$var}, {$translated});";
}
