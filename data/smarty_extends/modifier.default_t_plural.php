<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty default_t_plural modifier plugin
 *
 * Type:     modifier<br>
 * Name:     default_t_plural<br>
 * Purpose:  designate translatable default value for empty variables
 * @author   pineray 松田光貴 <matsudaterutaka at gmail dot com>
 * @param string
 * @param integer
 * @param string
 * @param string
 * @return string
 */
function smarty_modifier_default_t_plural($variable, $counter = 1, $single = '', $plural = '')
{
    // 多言語対応用の関数が無い、あるいはエイリアスが無ければ変数をそのまま出力
    if (!function_exists('t_plural') || empty($single) || empty($plural)) {
        return $variable;
    }

    if (!isset($variable) || $variable === '') {
        // パラメーター用の配列
        $params = array();
        // オプション用の配列
        $options = array();

        // 引数を取得
        $args = func_get_args();
        // パラメーターの引数があればセットする
        if (count($args) > 4) {
            array_shift($args); // $variable
            array_shift($args); // $counter
            array_shift($args); // $sigle
            array_shift($args); // $plural

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

        return t_plural($counter, $single, $plural, $params, $options);
    } else {
        return $variable;
    }
}
