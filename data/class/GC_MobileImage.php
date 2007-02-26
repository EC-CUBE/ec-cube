<?php
/**
 * ü���β��̲����٤ˤ��碌�Ʋ������Ѵ�����
 */

define("MOBILE_IMAGE_INC_PATH", realpath(dirname( __FILE__)) . "/../include");
require_once(MOBILE_IMAGE_INC_PATH . "/image_converter.inc");

/**
 * �����Ѵ����饹
 */
class GC_MobileImage {
	/**
	 * ������ü���β����٤˹�碌���Ѵ�����
	 * output buffering �ѥ�����Хå��ؿ�
	 *
	 * @param string ����
	 * @return string ����
	 */
	function handler($buffer) {

		// ü��������������
		$carrier = GC_MobileUserAgent::getCarrier();
		$model   = GC_MobileUserAgent::getModel();

		// �������äξ��Τ߽�����Ԥ�
		if ($carrier !== FALSE) {

			// HTML���IMG�������������
			$pattern = '/<img\s+src=[\'"]([^>"]+)[\'"]\s*\/*>/i';
			$result = preg_match_all($pattern, $buffer, $images);

			// ü���ξ�����������
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

			// docomo��softbank�ξ��ϲ����ե������Ĥ����Ѳ�ǽ�ʥ������ξ�¤�׻�����
			// au��HTML��byte����¤˲����ե����륵�������ޤޤ�ʤ��Τ�imageFileSize�Τޤޡ�
			if ($carrier == "docomo" or $carrier == "softbank") {
				// �׻�����(����ü����ɽ����ǽ��cache������ - HTML�ΥХ��ȿ� - �Ѵ���β���̾�ΥХ��ȿ�(�ܰ���) ) / HTML��β�����
				$temp_imagefilesize = ($cacheSize - strlen($buffer) - (140 * $result) ) / $result;
				// �׻���̤�ü����ɽ����ǽ�ե����륵������¤�꾮�������Ϸ׻���̤��ͤ�ͭ���ˤ���
				if ($temp_imagefilesize < $imageFileSize) {
					$imageFileSize = $temp_imagefilesize;
				}
			}

			// �����Ѵ��ξ���򥻥åȤ���
			$imageConverter = New ImageConverter();
			$imageConverter->setOutputDir(MOBILE_IMAGE_DIR);
			$imageConverter->setImageType($imageType);
			$imageConverter->setImageWidth($imageWidth);
			$imageConverter->setFileSize($imageFileSize);

			// HTML���IMG�������Ѵ���Υե�����ѥ����ִ�����
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
