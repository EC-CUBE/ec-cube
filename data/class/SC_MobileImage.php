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
 *
 * 端末の画面解像度にあわせて画像を変換する
 */

define('MOBILE_IMAGE_INC_REALDIR', realpath(dirname( __FILE__)) . '/../include/');
require_once MOBILE_IMAGE_INC_REALDIR . 'image_converter.inc';

/**
 * 画像変換クラス
 */
class SC_MobileImage {
    /**
     * 画像を端末の解像度に合わせて変換する
     * output buffering 用コールバック関数
     *
     * @param string 入力
     * @return string 出力
     */
    static function handler($buffer) {

        // 端末情報を取得する
        $carrier = SC_MobileUserAgent_Ex::getCarrier();
        $model   = SC_MobileUserAgent_Ex::getModel();

        // 携帯電話の場合のみ処理を行う
        if ($carrier !== FALSE) {

            // HTML中のIMGタグを取得する
            $images = array();
            $pattern = '/<img\s+[^<>]*src=[\'"]?([^>"\'\s]+)[\'"]?[^>]*>/i';
            $result = preg_match_all($pattern, $buffer, $images);

            // 端末の情報を取得する
            $fp = fopen(MOBILE_IMAGE_INC_REALDIR . "mobile_image_map_$carrier.csv", 'r');
            // 取得できない場合は, 入力内容をそのまま返す
            if ($fp === false) {
                return $buffer;
            }
            while (($data = fgetcsv($fp, 1000, ',')) !== FALSE) {
                if ($data[1] == $model || $data[1] == '*') {
                    $cacheSize     = $data[2];
                    $imageFileSize = $data[7];
                    $imageType     = $data[6];
                    $imageWidth    = $data[5];
                    $imageHeight   = $data[4];
                    break;
                }
            }
            fclose($fp);

            // docomoとsoftbankの場合は画像ファイル一つに利用可能なサイズの上限を計算する
            // auはHTMLのbyte数上限に画像ファイルサイズが含まれないのでimageFileSizeのまま。
            if ($carrier == 'docomo' or $carrier == 'softbank') {
                if ($result != false && $result > 0) {
                    // 計算式：(利用端末で表示可能なcacheサイズ - HTMLのバイト数 - 変換後の画像名のバイト数(目安値)) / HTML中の画像数
                    $temp_imagefilesize = ($cacheSize - strlen($buffer) - (140 * $result)) / $result;
                } else {
                    // 計算式：(利用端末で表示可能なcacheサイズ - HTMLのバイト数)
                    $temp_imagefilesize = ($cacheSize - strlen($buffer));
                }
                // 計算結果が端末の表示可能ファイルサイズ上限より小さい場合は計算結果の値を有効にする
                if ($temp_imagefilesize < $imageFileSize) {
                    $imageFileSize = $temp_imagefilesize;
                }
            }

            // 画像変換の情報をセットする
            $imageConverter = New ImageConverter();
            $imageConverter->setOutputDir(MOBILE_IMAGE_REALDIR);
            $imageConverter->setImageType($imageType);
            $imageConverter->setImageWidth($imageWidth);
            $imageConverter->setImageHeight($imageHeight);
            $imageConverter->setFileSize($imageFileSize);

            // HTML中のIMGタグを変換後のファイルパスに置換する
            foreach ($images[1] as $key => $path) {
                // resize_image.phpは除外
                if (stripos($path, ROOT_URLPATH . 'resize_image.php') !== FALSE) {
                    break;
                }

                $realpath = html_entity_decode($path, ENT_QUOTES);
                $realpath = preg_replace('|^' . ROOT_URLPATH . '|', HTML_REALDIR, $realpath);
                $converted = $imageConverter->execute($realpath);
                if (isset($converted['outputImageName'])) {
                    $buffer = str_replace($path, MOBILE_IMAGE_URLPATH . $converted['outputImageName'], $buffer);
                } else {
                    $buffer = str_replace($images[0][$key], '<!--No image-->', $buffer);
                }
            }
        }
        return $buffer;
    }
}
