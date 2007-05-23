<?php

function gfIsMobileMailAddress($address) {
	$arrMobileMailDomains = array('docomo.ne.jp', 'ezweb.ne.jp', 'softbank.ne.jp', 'vodafone.ne.jp', 'pdx.ne.jp');

	if (defined('MOBILE_ADDITIONAL_MAIL_DOMAINS')) {
		$arrMobileMailDomains = array_merge($arrMobileMailDomains, split('[ ,]+', MOBILE_ADDITIONAL_MAIL_DOMAINS));
	}

	foreach ($arrMobileMailDomains as $domain) {
		$domain = str_replace('.', '\\.', $domain);
		if (preg_match("/@([^@]+\\.)?$domain\$/", $address)) {
			return true;
		}
	}

	return false;
}


print(gfIsMobileMailAddress('tatsuyadake-aisitel@dk.pdx.ne.jp'));

?>