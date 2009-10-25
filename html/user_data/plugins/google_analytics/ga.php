<?php
// {{{ requires
require_once(PLUGIN_PATH . "google_analytics/classes/pages/LC_Page_FrontParts_Bloc_GoogleAnalytics.php");

// }}}
// {{{ generate page

$objPage = new LC_Page_FrontParts_Bloc_GoogleAnalytics();
register_shutdown_function(array($objPage, "destroy"));
$objPage->init();
$objPage->process();
?>