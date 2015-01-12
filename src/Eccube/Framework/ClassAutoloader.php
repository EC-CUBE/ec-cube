<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

use Eccube\Application;
use Eccube\Framework\Helper\PluginHelper;

/**
 * クラスのオートローディングクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ClassAutoloader
{
    /**
     * クラスのオートローディング本体
     *
     * LC_* には対応していない。
     * @return void
     */
    public static function autoload($class)
    {
        $arrClassNamePart = explode('_', $class);
        $is_ex = end($arrClassNamePart) === 'Ex';
        $count = count($arrClassNamePart);
        $classpath = $is_ex ? CLASS_EX_REALDIR : CLASS_REALDIR;

        if (($arrClassNamePart[0] === 'GC' || $arrClassNamePart[0] === 'SC') && $arrClassNamePart[1] === 'Utils') {
            $classpath .= $is_ex ? 'util_extends/' : 'util/';
        } elseif ($arrClassNamePart[0] === 'SC' && $is_ex === true && $count >= 4) {
            $arrClassNamePartTemp = $arrClassNamePart;
            // FIXME クラスファイルのディレクトリ命名が変。変な現状に合わせて強引な処理をしてる。
            $arrClassNamePartTemp[1] = $arrClassNamePartTemp[1] . '_extends';
            $classpath .= strtolower(implode('/', array_slice($arrClassNamePartTemp, 1, -2))) . '/';
        } elseif ($arrClassNamePart[0] === 'SC' && $is_ex === false && $count >= 3) {
            $classpath .= strtolower(implode('/', array_slice($arrClassNamePart, 1, -1))) . '/';
        } elseif ($arrClassNamePart[0] === 'SC') {
            // 処理なし
        } else {
            // PEAR用
            // FIXME トリッキー
            $classpath = '';
            $class = str_replace('_', '/', $class);
        }

        $classpath .= "$class.php";

        // プラグイン向けフックポイント
        // MEMO: プラグインのローダーがDB接続を必要とするため、Queryがロードされた後のみ呼び出される。
        //       プラグイン情報のキャッシュ化が行われれば、全部にフックさせることを可能に？
        $objPlugin = PluginHelper::getSingletonInstance(true);
        if (is_object($objPlugin)) {
            // 元の設定を一時保存
            $plugin_class = $class;
            $plugin_classpath = $classpath;

            $objPlugin->doAction('loadClassFileChange', array(&$plugin_class, &$plugin_classpath));

            // FIXME: トリッキーな処理で _Ex ファイルを無視しないようにする（無視するとユーザーカスタマイズで分かりにくい)
            //        XXXX_Ex がロードされる場合にextendsのchainを
            //        XXXX_Ex -> XXXX から、 XXXX_Ex -> $class (-> XXXX) と変える。
            //        そうでない場合は、直接置き換えと想定して帰ってきたクラスをロードする
            if (is_array($plugin_class) && count($plugin_class) > 0) {
                $arrPluginClassName = $plugin_class;
                $arrPluginClassPath = $plugin_classpath;

                foreach ($arrPluginClassName as $key => $plugin_class) {
                    $plugin_classpath = $arrPluginClassPath[$key];

                    if ($is_ex) {
                        // Ex ファイルへのフックの場合のみチェイン変更する。

                        if ($parent_classname) {
                            $exp = "/(class[ ]+{$plugin_class}[ ]+extends +)[a-zA-Z_\-]+( *{?)/";
                            $replace = '$1' . $parent_classname . '$2';

                            $base_class_str = file_get_contents($plugin_classpath);
                            $base_class_str = str_replace(array('<?php', '?>'), '', $base_class_str);
                            $base_class_str = preg_replace($exp, $replace, $base_class_str, 1);
                            eval($base_class_str);
                        } else {
                            include $plugin_classpath;
                        }

                        $parent_classname = $plugin_class;
                    } else {
                        include $plugin_classpath;
                    }
                }

                if ($is_ex) {
                    $exp = "/(class[ ]+{$class}[ ]+extends +)[a-zA-Z_\-]+( *{?)/";
                    $replace = '$1' . $parent_classname . '$2';
                    $base_class_str = file_get_contents($classpath);
                    $base_class_str = str_replace(array('<?php', '?>'), '', $base_class_str);
                    $base_class_str = preg_replace($exp, $replace, $base_class_str, 1);
                    eval($base_class_str);

                    return;
                }
            }
        }
        if (file_exists($classpath)) {
            include $classpath;
        } else {
            $arrPath = explode(PATH_SEPARATOR, get_include_path());
            foreach ($arrPath as $path) {
                if (file_exists($path . '/' .$classpath)) {
                    include $classpath;
                    break;
                }
            }
        }
    }
}
