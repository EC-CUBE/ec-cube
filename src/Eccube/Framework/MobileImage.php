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
use Eccube\Framework\ImageConverter;
use Eccube\Framework\MobileUserAgent;

define('MOBILE_IMAGE_INC_REALDIR', realpath(__DIR__ . '/../../mobile_image'));

/**
 * 画像変換クラス
 * 
 * 端末の画面解像度にあわせて画像を変換する
 */
class MobileImage
{
    /**
     * 画像を端末の解像度に合わせて変換する
     * output buffering 用コールバック関数
     *
     * @param string 入力
     * @return string 出力
     */
    public static function handler($buffer)
    {
        // 端末情報を取得する
        $carrier = MobileUserAgent::getCarrier();
        $model   = MobileUserAgent::getModel();

        // 携帯電話の場合のみ処理を行う
        if ($carrier !== FALSE) {
            // HTML中のIMGタグを取得する
            $images = array();
            $pattern = '/<img\s+[^<>]*src=[\'"]?([^>"\'\s]+)[\'"]?[^>]*>/i';
            $result = preg_match_all($pattern, $buffer, $images);

            // 端末の情報を取得する
            $fp = fopen(MOBILE_IMAGE_INC_REALDIR . "/mobile_image_map_$carrier.csv", 'r');
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
            $imageConverter = new ImageConverter();
            $imageConverter->setOutputDir(MOBILE_IMAGE_REALDIR);
            $imageConverter->setImageType($imageType);
            $imageConverter->setImageWidth($imageWidth);
            $imageConverter->setImageHeight($imageHeight);
            $imageConverter->setFileSize($imageFileSize);

            // HTML中のIMGタグを変換後のファイルパスに置換する
            foreach ($images[1] as $key => $path) {
                // resize_image.phpは除外
                if (stripos($path, ROOT_URLPATH . 'resize_image.php') !== FALSE) {
                    continue;
                }

                $realpath = html_entity_decode($path, ENT_QUOTES);
                $realpath = substr_replace($realpath, HTML_REALDIR, 0, strlen(ROOT_URLPATH));
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
