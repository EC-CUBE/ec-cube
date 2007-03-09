<?php

require_once("../../require.php");

$objView = new SC_UserView("./templates/");
$objImage = new SC_Image(IMAGE_TEMP_DIR);

//---- ページ表示用クラス
class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'customer/release_set.tpl';
		$this->tpl_mainno = 'release_set';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'release_set';
		
		global $arrRelease;
		$this->arrRelease = $arrRelease;
	}
}

exit();

//---- ページ表示用クラス
class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'customer/release_set.tpl';
		$this->tpl_mainno = 'release_set';
		$this->tpl_subnavi = 'customer/subnavi.tpl';
		$this->tpl_subno = 'release_set';
		
		global $arrRelease;
		$this->arrRelease = $arrRelease;
	}
}
?>
