<?php

/**
 * 茵?ず?с??????腟究??絖???臀???????????絖??? (Shift JIS)
 * ???????????????絖?????
 */
define('MOBILE_EMOJI_SUBSTITUTE', '');

/**
 * ?阪遣腴?????亀??絖????宴???????
 */
class GC_MobileEmoji {
	/**
	 * 腟究??絖??帥?違???????ｃ???∝??????絖??潟?若???????????
	 * output buffering ???潟?若?????????∽?
	 *
	 * @param string ?ュ??
	 * @return string ?阪??
	 */
	function handler($buffer) {
		$replace_callback = create_function('$matches', 'return GC_MobileEmoji::indexToCode($matches[1]);');
		return preg_replace_callback('/\[emoji:(e?\d+)\]/', $replace_callback, $buffer);
	}

	/**
	 * 腟究??絖???垩??腟究??絖???茵??? Shift JIS ????絖??????????????
	 *
	 * @param string $index 腟究??絖???
	 * @return string 腟究??絖???茵??? Shift JIS ????絖?????菴?????
	 */
	function indexToCode($index) {
		$carrier = GC_MobileUserAgent::getCarrier();
		if ($carrier === false) {
			return MOBILE_EMOJI_SUBSTITUTE;
		}

		static $arrMap;
		if (!isset($arrMap)) {
			$arrMap = @include_once(dirname(__FILE__) . "/../include/mobile_emoji_map_$carrier.inc");
		}

		return isset($arrMap[$index]) ? $arrMap[$index] : MOBILE_EMOJI_SUBSTITUTE;
	}
}
?>
