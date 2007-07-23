<?php
/**
 * marqueeタグで囲む。
 *
 * DoCoMoの携帯端末の場合はmarqueeを使用しない。
 *
 * @param string $value 入力
 * @return string 出力
 */
function smarty_block_marquee($params, $content, &$smarty, &$repeat) {
	// {/marquee}の場合のみ出力する。
	if ($repeat || !isset($content)) {
		return null;
	}

	// 末尾の改行などを取り除く。
	$content = rtrim($content);

	// marqueeタグを使用しない場合
	if (defined('MOBILE_SITE') && GC_MobileUserAgent::getCarrier() == 'docomo') {
		return "<div>\n$content\n</div>\n";
	}

	return "<marquee>\n$content\n</marquee>\n";
}
?>
