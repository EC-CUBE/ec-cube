<?php

/**
 * ���᡼������դ����ɥ쥹�Υ��ޥ��̾�ȥȡ�����δ֤ζ��ڤ�ʸ��
 */
define('MOBILE_KARA_MAIL_EXTENSION_DELIMITER', '_');

/**
 * ��Х��륵���Ȥζ��᡼��򰷤����饹
 */
class GC_MobileKaraMail {
	/**
	 * �Ķ��ѿ����� MTA ��Ƚ�̤����б����� GC_MobileKaraMail �ޤ��Ϥ��Υ��֥��饹
	 * �Υ��󥹥��󥹤�������롣
	 *
	 * @return object GC_MobileKaraMail �ޤ��Ϥ��Υ��֥��饹�Υ��󥹥��󥹤��֤���
	 */
	function &factory() {
		if (isset($_ENV['EXTENSION'])) {
			$objInstance = new GC_MobileKaraMail_Postfix;
		} elseif (isset($_ENV['DEFAULT'])) {
			$objInstance = new GC_MobileKaraMail_Qmail;
		} else {
			$objInstance = new GC_MobileKaraMail;
		}

		return $objInstance;
	}

	/**
	 * ɸ�����Ϥ���᡼����ɤ߹��ߡ�ɬ�פʾ����������롣
	 *
	 * @return void
	 */
	function parse() {
		if (@$this->parsed) {
			return;
		}

		require_once DATA_PATH . '/module/Mail/mimeDecode.php';

		$fp = fopen('php://stdin', 'r');

		// From �Ԥ���Ϥ��롣
		$from_line = rtrim(fgets($fp));
		if (preg_match('/^From\\s+"?([^\\s"@]+)"?@([^\\s@]+)/', $from_line, $matches)) {
			$this->sender = $matches[1] . '@' . $matches[2];
		} else {
			trigger_error("Invalid from line: $from_line");
			$this->sender = null;
		}

		// �Ĥ�Υإå�������Ϥ��롣
		$data = '';
		while (!feof($fp)) {
			$data .= fgets($fp);
			if (rtrim($data, "\r\n") == '') {
				break;
			}
		}
		$structure = Mail_mimeDecode::decode(array('input' => $data));
		$this->recipient = @$structure->headers['to'];

		// ���襢�ɥ쥹�����ĥ��ʬ��������롣
		$pos = strpos($this->recipient, MOBILE_KARA_MAIL_ADDRESS_DELIMITER);
		if ($pos !== false) {
			$extension_and_domain = substr($this->recipient, $pos + 1);
			$pos = strpos($extension_and_domain, '@');
			if ($pos !== false) {
				$this->extension = substr($extension_and_domain, 0, $pos);
			} else {
				$this->extension = $extension_and_domain;
			}
		} else {
			trigger_error("Invalid recipient: {$this->recipient}");
			$this->extension = null;
		}

		$this->parsed = true;
	}

	/**
	 * �ۿ�����λ�������Ȥ򼨤���λ���ơ������ǥץ�����λ���롣
	 *
	 * @return void
	 */
	function success() {
		exit(0);
	}

	/**
	 * ���Ū�ʥ��顼�򼨤���λ���ơ������ǥץ�����λ���롣
	 *
	 * @return void
	 */
	function temporaryFailure() {
		exit(75);
	}

	/**
	 * �����ԤΥ᡼�륢�ɥ쥹��������롣
	 *
	 * parse() �¹Ը�˻��Ѥ��뤳�ȡ�
	 *
	 * @return string|false �����ԤΥ᡼�륢�ɥ쥹���֤��������Ǥ��ʤ��ä����� false ���֤���
	 */
	function getSender() {
		return isset($this->sender) ? $this->sender : false;
	}

	/**
	 * ����Υ᡼�륢�ɥ쥹�γ�ĥ��ʬ���饳�ޥ��̾��������롣
	 *
	 * parse() �¹Ը�˻��Ѥ��뤳�ȡ�
	 *
	 * @return string|false ���ޥ��̾���֤��������Ǥ��ʤ��ä����� false ���֤���
	 */
	function getCommand() {
		if (!isset($this->extension)) {
			return false;
		}

		$pos = strpos($this->extension, MOBILE_KARA_MAIL_EXTENSION_DELIMITER);
		if ($pos === false) {
			return false;
		}

		return substr($this->extension, 0, $pos);
	}

	/**
	 * ����Υ᡼�륢�ɥ쥹�γ�ĥ��ʬ����ȡ������������롣
	 *
	 * parse() �¹Ը�˻��Ѥ��뤳�ȡ�
	 *
	 * @return string|false �ȡ�������֤��������Ǥ��ʤ��ä����� false ���֤���
	 */
	function getToken() {
		if (!isset($this->extension)) {
			return false;
		}

		$pos = strpos($this->extension, MOBILE_KARA_MAIL_EXTENSION_DELIMITER);
		if ($pos === false) {
			return false;
		}

		return substr($this->extension, $pos + 1);
	}
}

/**
 * ��Х��륵���Ȥζ��᡼��򰷤����饹 (Postfix��)
 */
class GC_MobileKaraMail_Postfix extends GC_MobileKaraMail {
	/**
	 * @see GC_MobileKaraMail::parse()
	 */
	function parse() {
		if (@$this->parsed) {
			return;
		}

		$this->sender = $_ENV['SENDER'];
		$this->recipient = $_ENV['RECIPIENT'];
		$this->extension = $_ENV['EXTENSION'];

		$this->parsed = true;
	}
}

/**
 * ��Х��륵���Ȥζ��᡼��򰷤����饹 (qmail��)
 */
class GC_MobileKaraMail_Qmail extends GC_MobileKaraMail {
	/**
	 * @see GC_MobileKaraMail::parse()
	 */
	function parse() {
		if (@$this->parsed) {
			return;
		}

		$this->sender = $_ENV['SENDER'];
		$this->recipient = $_ENV['RECIPIENT'];
		$this->extension = $_ENV['DEFAULT'];

		$this->parsed = true;
	}

	/**
	 * ���Ū�ʥ��顼�򼨤���λ���ơ������ǥץ�����λ���롣
	 *
	 * @return void
	 */
	function temporaryFailure() {
		exit(111);
	}
}
?>
