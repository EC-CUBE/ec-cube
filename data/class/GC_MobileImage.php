<?php
/**
 * ü���β��̲����٤ˤ��碌�Ʋ������Ѵ�����
 *
 */

define("INC_PATH", realpath(dirname( __FILE__)) . "/../include");
require_once(INC_PATH . "/image_converter.inc");

/**
 * ����ü���Υ��饹
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
			preg_match_all($pattern, $buffer, $images);

            // ü���ξ�����������
			$fp = @fopen(INC_PATH . "/mobile_image_map_$carrier.inc", "r");
			flock($fp, LOCK_SH);
			while (($data = fgetcsv($fp, 1000, ",")) !== FALSE) {
				if ($data[1] == $model) {
					$imageType  = $data[6];
					$imageWidth = $data[5];
				}
			}
			flock($fp, LOCK_UN);
			fclose($fp);

            // �����Ѵ��ξ���򥻥åȤ���
			$imageConverter = New ImageConverter();
			$imageConverter->setOutputDir(MOBILE_IMAGE_DIR);
			$imageConverter->setImageType($imageType);
			$imageConverter->setImageWidth($imageWidth);

            // HTML���IMG�������Ѵ���Υե�����ѥ����ִ�����
			foreach ($images[1] as $key => $value) {
				$converted = $imageConverter->execute(str_replace(PC_URL_DIR, PC_HTML_PATH, $value));
			    $buffer = str_replace($value, MOBILE_IMAGE_URL . '/' . $converted['outputImageName'], $buffer);
			}
		}
		return $buffer;
	}
}
?>
