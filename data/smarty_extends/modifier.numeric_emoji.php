<?php
/**
 * ���ͤ������ʸ�����Ѵ����롣
 *
 * ���Ϥ�0��9�ǤϤʤ���硢�ޤ��ϡ�����ü������Υ��������ǤϤʤ����ϡ�
 * ���Ϥ� [ �� ] �ǰϤ��ʸ������֤���
 *
 * @param string $value ����
 * @return string ����
 */
function smarty_modifier_numeric_emoji($value) {
	// ������ʸ�� (0��9) �γ�ʸ���ֹ�
	static $numeric_emoji_index = array('134', '125', '126', '127', '128', '129', '130', '131', '132', '133');

	if (GC_MobileUserAgent::isMobile() && isset($numeric_emoji_index[$value])) {
		return '[emoji:' . $numeric_emoji_index[$value] . ']';
	} else {
		return '[' . $value . ']';
	}
}
?>
