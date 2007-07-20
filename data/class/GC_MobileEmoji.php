<?php
/**
 * 表示できない絵文字を置き換える文字列 (Shift JIS)
 * デフォルトは空文字列。
 */
define('MOBILE_EMOJI_SUBSTITUTE', '');

/**
 * 携帯端末の絵文字を扱うクラス
 */
class GC_MobileEmoji {
	/**
	 * 絵文字タグを各キャリア用の文字コードに変換する
	 * output buffering 用コールバック関数
	 *
	 * @param string 入力
	 * @return string 出力
	 */
	function handler($buffer) {
		$replace_callback = create_function('$matches', 'return GC_MobileEmoji::indexToCode($matches[1]);');
		return preg_replace_callback('/\[emoji:(e?\d+)\]/', $replace_callback, $buffer);
	}

	/**
	 * 絵文字番号を絵文字を表す Shift JIS の文字列に変換する。
	 *
	 * @param string $index 絵文字番号
	 * @return string 絵文字を表す Shift JIS の文字列を返す。
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
