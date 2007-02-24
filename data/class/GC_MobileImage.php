<?php
/**
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
			preg_match_all($pattern, $buffer, $images);

			// 端末の情報を取得する
			$fp = fopen(MOBILE_IMAGE_INC_PATH . "/mobile_image_map_$carrier.csv", "r");
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
				if ($data[1] == $model) {
					$imageType  = $data[6];
					$imageWidth = $data[5];
				}
			}
			fclose($fp);

			// 画像変換の情報をセットする
			$imageConverter = New ImageConverter();
			$imageConverter->setOutputDir(MOBILE_IMAGE_DIR);
			$imageConverter->setImageType($imageType);
			$imageConverter->setImageWidth($imageWidth);

			// HTML中のIMGタグを変換後のファイルパスに置換する
			foreach ($images[1] as $key => $value) {
				$converted = $imageConverter->execute(preg_replace('|^' . PC_URL_DIR . '|', PC_HTML_PATH, $value));
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
