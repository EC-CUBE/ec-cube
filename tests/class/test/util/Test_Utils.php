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
 * テストケースで使う一般的なユーティリティを持つクラス.
 *
 * @author Hiroko Tamagawa
 * @version $Id$
 */
class Test_Utils {

  /**
   * 連想配列から指定されたキーだけを抜き出したものを返します.
   * 入力の連想配列には変更を加えません.
   *
   * @static
   * @param input_array 入力の連想配列
   * @param map_keys 出力結果に入れたいキーを配列で指定します
   * @return 指定したキーのみを持つ連想配列
   */
  public static function mapArray($input_array, $map_keys) {
    $output_array = array();
    foreach ($map_keys as $index => $map_key) {
      $output_array[$map_key] = $input_array[$map_key];
    }

    return $output_array;
  }

  /**
   * 配列の各要素（連想配列）から特定のキーだけを抜き出した配列を返します.
   * 入力の連想配列には変更を加えません.
   * 
   * @static
   * @param input_array 入力の配列
   * @param key 抽出対象のキー
   * @return 指定のキーだけを抜き出した配列
   */
  public static function mapCols($input_array, $key) {
    $output_array = array();
    foreach ($input_array as $data) {
      $output_array[] = $data[$key];
    }
    
    return $output_array;
  }

  /**
   * 配列に別の配列をappendします。
   * $orig_arrayが直接変更されます。
   * 
   * @static
   * @param orig_array 追加先の配列
   * @param new_array 追加要素を持つ配列
   */
  public static function array_append(&$orig_array, $new_array) {
    foreach ($new_array as $element) {
      $orig_array[] = $element;
    }
  }
}

