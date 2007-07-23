<?php
/**
 * 
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 * 
 *
 *
 * 端末の画面解像度にあわせて画像を変換する
 */

define("MOBILE_IMAGE_INC_PATH", realpath(dirname( __FILE__)) . "/../include");
require_once(MOBILE_IMAGE_INC_PATH . "/image_converter.inc");

/**
 * 画像変換クラス
 */
class GC_MobileImage {
	/**
	 * 画像を端末の解像度に合わせて変換する
	 * output buffering 用コールバック関数
	 *
	 * @param string 入力
	 * @return string 出力
	 */
	function handler($buffer) {

		// 端末情報を取得する
		$carrier = GC_MobileUserAgent::getCarrier();
		$model   = GC_MobileUserAgent::getModel();

		// 携帯電話の場合のみ処理を行う
		if ($carrier !== FALSE) {

			// HTML中のIMGタグを取得する
			$pattern = '/<img\s+src=[\'"]([^>"]+)[\'"]\s*\/*>/i';
			$result = preg_match_all($pattern, $buffer, $images);

			// 端末の情報を取得する
			$fp = fopen(MOBILE_IMAGE_INC_PATH . "/mobile_image_map_$carrier.csv", "r");
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
				if ($data[1] == $model || $data[1] == '*') {
					$cacheSize     = $data[2];
					$imageFileSize = $data[7];
					$imageType     = $data[6];
					$imageWidth    = $data[5];
					break;
				}
			}
			fclose($fp);

			// docomoとsoftbankの場合は画像ファイル一つに利用可能なサイズの上限を計算する
			// auはHTMLのbyte数上限に画像ファイルサイズが含まれないのでimageFileSizeのまま。
			if ($carrier == "docomo" or $carrier == "softbank") {
				// 計算式：(利用端末で表示可能なcacheサイズ - HTMLのバイト数 - 変換後の画像名のバイト数(目安値) ) / HTML中の画像数
				$temp_imagefilesize = ($cacheSize - strlen($buffer) - (140 * $result) ) / $result;
				// 計算結果が端末の表示可能ファイルサイズ上限より小さい場合は計算結果の値を有効にする
				if ($temp_imagefilesize < $imageFileSize) {
					$imageFileSize = $temp_imagefilesize;
				}
			}

			// 画像変換の情報をセットする
			$imageConverter = New ImageConverter();
			$imageConverter->setOutputDir(MOBILE_IMAGE_DIR);
			$imageConverter->setImageType($imageType);
			$imageConverter->setImageWidth($imageWidth);
			$imageConverter->setFileSize($imageFileSize);

			// HTML中のIMGタグを変換後のファイルパスに置換する
			foreach ($images[1] as $key => $value) {
				$converted = $imageConverter->execute(preg_replace('|^' . URL_DIR . '|', HTML_PATH, $value));
				if (isset($converted['outputImageName'])) {
					$buffer = str_replace($value, MOBILE_IMAGE_URL . '/' . $converted['outputImageName'], $buffer);
				} else {
					$buffer = str_replace($images[0][$key], '<!--No image-->', $buffer);
				}
			}
		}
		return $buffer;
	}
}
?>
