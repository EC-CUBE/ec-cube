<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

/**
 * クラスのオートローディングクラス
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 * @version $Id$
 */
class SC_ClassAutoloader {
    /**
     * クラスのオートローディング本体
     *
     * LC_* には対応していない。
     * @return void
     */
    public static function autoload($class) {
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
        }
        // PEAR用
        // FIXME トリッキー
        else {
            $classpath = '';
            $class = str_replace('_', '/', $class);
        }

        $classpath .= "$class.php";

        // プラグイン向けフックポイント
        // MEMO: プラグインのローダーがDB接続を必要とするため、SC_Queryがロードされた後のみ呼び出される。
        //       プラグイン情報のキャッシュ化が行われれば、全部にフックさせることを可能に？
        $objPlugin = SC_Helper_Plugin_Ex::getSingletonInstance(true);
        if (is_object($objPlugin)) {

            // 元の設定を一時保存
            $plugin_class = $class;
            $plugin_classpath = $classpath;

            $objPlugin->doAction('loadClassFileChange', array(&$plugin_class, &$plugin_classpath));

            // FIXME: トリッキーな処理で _Ex ファイルを無視しないようにする（無視するとユーザーカスタマイズで分かりにくい)
            //        SC_XXXX_Ex がロードされる場合にextendsのchainを
            //        SC_XXXX_Ex -> SC_XXXX から、 SC_XXXX_Ex -> $class (-> SC_XXXX) と変える。
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
        include $classpath;
    }
}
