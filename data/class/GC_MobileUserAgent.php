<?php
require_once(dirname(__FILE__) . '/../module/Net/UserAgent/Mobile.php');

/**
 * ����ü���ξ���򰷤����饹
 *
 * �оݤȤ������ü���� $_SERVER ������ꤹ�롣
 * ���٤ƤΥ᥽�åɤϥ��饹�᥽�åɡ�
 */
class GC_MobileUserAgent {
	/**
	 * ����ü���Υ���ꥢ��ɽ��ʸ�����������롣
	 *
	 * ʸ����� docomo, ezweb, softbank �Τ����줫��
	 *
	 * @return string|false ����ü���Υ���ꥢ��ɽ��ʸ������֤���
	 *                      ����ü���ǤϤʤ����� false ���֤���
	 */
	function getCarrier() {
		$objAgent =& Net_UserAgent_Mobile::singleton();
		if (Net_UserAgent_Mobile::isError($objAgent)) {
			return false;
		}

		switch ($objAgent->getCarrierShortName()) {
		case 'I':
			return 'docomo';
		case 'E':
			return 'ezweb';
		case 'V':
			return 'softbank';
		default:
			return false;
		}
	}

	/**
	 * ���ꥵ���Ȥ����Ѳ�ǽ�ʷ���ü��/���ѼԤ�ID��������롣
	 *
	 * �ƥ���ꥢ�ǻ��Ѥ���ID�μ���:
	 * + docomo   ... UTN
	 * + ezweb    ... EZ�ֹ�
	 * + softbank ... ü�����ꥢ���ֹ�
	 *
	 * @return string|false ��������ID���֤��������Ǥ��ʤ��ä����� false ���֤���
	 */
	function getId() {
		$objAgent =& Net_UserAgent_Mobile::singleton();
		if (Net_UserAgent_Mobile::isError($objAgent)) {
			return false;
		} elseif ($objAgent->isDoCoMo() || $objAgent->isVodafone()) {
			$id = $objAgent->getSerialNumber();
		} elseif ($objAgent->isEZweb()) {
			$id = @$_SERVER['HTTP_X_UP_SUBNO'];
		}
		return isset($id) ? $id : false;
	}

	/**
	 * ����ü���ε����ɽ��ʸ�����������롣
	 * ����ü���ǤϤʤ����ϥ桼��������������Ȥ�̾����������롣(��: "Mozilla")
	 *
	 * @return string ����ü���Υ�ǥ��ɽ��ʸ������֤���
	 */
	function getModel() {
		$objAgent =& Net_UserAgent_Mobile::singleton();
		if (Net_UserAgent_Mobile::isError($objAgent)) {
			return 'Unknown';
		} elseif ($objAgent->isNonMobile()) {
			return $objAgent->getName();
		} else {
			return $objAgent->getModel();
		}
	}

	/**
	 * EC-CUBE �����ݡ��Ȥ�����ӥ���ꥢ���ɤ�����Ƚ�̤��롣
	 *
	 * @return boolean ���ݡ��Ȥ��Ƥ������ true������ʳ��ξ��� false ���֤���
	 */
	function isMobile() {
		$objAgent =& Net_UserAgent_Mobile::singleton();
		if (Net_UserAgent_Mobile::isError($objAgent)) {
			return false;
		} else {
			return $objAgent->isDoCoMo() || $objAgent->isEZweb() || $objAgent->isVodafone();
		}
	}

	/**
	 * EC-CUBE �����ݡ��Ȥ�����ӥ���ꥢ���ɤ�����Ƚ�̤��롣
	 *
	 * @return boolean ����ü���ǤϤʤ����� true������ʳ��ξ��� false ���֤���
	 */
	function isNonMobile() {
		return !GC_MobileUserAgent::isMobile();
	}

	/**
	 * EC-CUBE �����ݡ��Ȥ������ü�����ɤ�����Ƚ�̤��롣
	 *
	 * @return boolean ���ݡ��Ȥ��Ƥ������ true������ʳ��ξ��� false ���֤���
	 */
	function isSupported() {
		$objAgent =& Net_UserAgent_Mobile::singleton();

		// ����ü������ǧ�����줿����User-Agent �η�����̤�Τξ��
		if (Net_UserAgent_Mobile::isError($objAgent)) {
			gfPrintLog($objAgent->toString());
			return false;
		}

		if ($objAgent->isDoCoMo()) {
			$arrUnsupportedSeries = array('501i', '502i', '209i', '210i');
			$arrUnsupportedModels = array('SH821i', 'N821i', 'P821i ', 'P651ps', 'R691i', 'F671i', 'SH251i', 'SH251iS');
			return !in_array($objAgent->getSeries(), $arrUnsupportedSeries) && !in_array($objAgent->getModel(), $arrUnsupportedModels);
		} elseif ($objAgent->isEZweb()) {
			return $objAgent->isWAP2();
		} elseif ($objAgent->isVodafone()) {
			return $objAgent->isPacketCompliant();
		} else {
			// ����ü���ǤϤʤ����ϥ��ݡ��Ȥ��Ƥ��뤳�Ȥˤ��롣
			return true;
		}
	}
}
?>
