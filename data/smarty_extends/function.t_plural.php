<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {t_plural} plugin
 *
 * Type:     function<br>
 * Name:     t_plural<br>
 * Purpose:  replace message alias to appropriate strings
 * @author pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param array
 * @param Smarty
 * @return string
 */
function smarty_function_t_plural($params, &$smarty) {
    // 多言語対応用の関数が定義されていなければエラーを出力
    if (!function_exists('t_plural')) {
        $smarty->_trigger_fatal_error("[plugin] function 't_plural' is not defined");
        return;
    }

    // 書式判定用の数値が無ければエラーを出力
    if (empty($params['counter'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'counter' cannot be empty");
        return;
    }
    $counter = $params['counter'];
    unset($params['counter']);
    // 単数形のエイリアスが無ければエラーを出力
    if (empty($params['single'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'single' cannot be empty");
        return;
    }
    $single = $params['single'];
    unset($params['single']);
    // 複数形のエイリアスが無ければエラーを出力
    if (empty($params['plural'])) {
        $smarty->_trigger_fatal_error("[plugin] parameter 'plural' cannot be empty");
        return;
    }
    $plural = $params['plural'];
    unset($params['plural']);

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

    return t_plural($counter, $single, $plural, $params, $options);
}
