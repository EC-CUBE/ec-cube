<?php
/**
 * marquee�����ǰϤࡣ
 *
 * DoCoMo�η���ü���ξ���marquee����Ѥ��ʤ���
 *
 * @param string $value ����
 * @return string ����
 */
function smarty_block_marquee($params, $content, &$smarty, &$repeat) {
	// {/marquee}�ξ��Τ߽��Ϥ��롣
	if ($repeat || !isset($content)) {
		return null;
	}

	// �����β��Ԥʤɤ��������
	$content = rtrim($content);

	// marquee��������Ѥ��ʤ����
	if (defined('MOBILE_SITE') && GC_MobileUserAgent::getCarrier() == 'docomo') {
		return "<div>\n$content\n</div>\n";
	}

	return "<marquee>\n$content\n</marquee>\n";
}
?>
