<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty t modifier plugin
 *
 * Type:     modifier<br>
 * Name:     t<br>
 * Purpose:  translate variables
 * @author   pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param string
 * @return string
 */
function smarty_modifier_t($string)
{
    // 多言語対応用の関数が無ければ変数をそのまま出力
    if (!function_exists('t')) {
        return $string;
    }

    // パラメーター用の配列
    $params = array();
    // オプション用の配列
    $options = array();

    // 引数を取得
    $args = func_get_args();
    // パラメーターの引数があればセットする
    if (count($args) > 1) {
        array_shift($args); // $string

        // 引数をパラメーターに変換
        $max = floor(count($args)/2);
        for ($i = 0; $i < $max; $i++) {
            $key = $i * 2;
            $params[$args[$key]] = $args[$key+1];
        }

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
    }

    return t($string, $params, $options);
}
