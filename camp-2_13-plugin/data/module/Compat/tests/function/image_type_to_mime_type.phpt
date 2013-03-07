--TEST--
Function -- image_type_to_mime_type
--SKIPIF--
<?php if (function_exists('image_type_to_mime_type')) { echo 'skip'; } ?>
--FILE--
<?php
require_once 'PHP/Compat.php';
PHP_Compat::loadFunction('image_type_to_mime_type');

$types = array (
	IMAGETYPE_GIF,
	IMAGETYPE_JPEG,
	IMAGETYPE_PNG,
	IMAGETYPE_SWF,
	IMAGETYPE_PSD,
	IMAGETYPE_BMP,
	IMAGETYPE_TIFF_II,
	IMAGETYPE_TIFF_MM,
	IMAGETYPE_JPC,
	IMAGETYPE_JP2,
	IMAGETYPE_JPX,
	IMAGETYPE_JB2,
	IMAGETYPE_SWC,
	IMAGETYPE_IFF,
	IMAGETYPE_WBMP,
	IMAGETYPE_XBM
);

foreach ($types as $type) {
	echo image_type_to_mime_type($type), "\n";
}
?>
--EXPECT--
image/gif
image/jpeg
image/png
application/x-shockwave-flash
image/psd
image/bmp
image/tiff
image/tiff
application/octet-stream
image/jp2
application/octet-stream
application/octet-stream
application/x-shockwave-flash
image/iff
image/vnd.wap.wbmp
image/xbm