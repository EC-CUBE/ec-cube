<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {t} plugin
 *
 * Type:     function<br>
 * Name:     t<br>
 * Purpose:  replace message alias to appropriate strings
 * @author pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_t($params, &$smarty) {
    // エイリアスが無ければエラーを出力
    if (empty($params['string'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'string' cannot be empty");
        return;
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

    return SC_Utils_Ex::t($string, $params, $options);
}
