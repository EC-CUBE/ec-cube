<?php
// FIXME クラスにする
$include_dir = realpath(dirname( __FILE__));
require_once($include_dir . "/define.php");
if (!defined("CLASS_PATH")) {
    /** クラスパス */
    define("CLASS_PATH", $include_dir . HTML2DATA_DIR . "class/");
}
require_once($include_dir . HTML2DATA_DIR. "conf/conf.php");
require_once($include_dir . HTML2DATA_DIR. "module/gdthumb.php");
require_once($include_dir . HTML2DATA_DIR. "class/util_extends/GC_Utils_Ex.php");


$objThumb = new gdthumb();

$file = NO_IMAGE_DIR;

// NO_IMAGE_DIR以外のファイル名が渡された場合、ファイル名のチェックを行う
if ( isset($_GET['image']) && $_GET['image'] !== NO_IMAGE_DIR) {

    // ファイル名が正しい場合だけ、$fileを設定
    if ( lfCheckFileName() === true ) {
        $file = IMAGE_SAVE_DIR . $_GET['image'];
    } else {
        GC_Utils_Ex::gfPrintLog('invalid access :resize_image.php $_GET["image"]=' . $_GET['image']);
    }
}

if(file_exists($file)){
    $objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
}else{
    $objThumb->Main(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], "", true);
}

// ファイル名の形式をチェック
function lfCheckFileName() {
    //$pattern = '|^[0-9]+_[0-9a-z]+\.[a-z]{3}$|';
    $pattern = '|\./|';
    $file    = trim($_GET["image"]);
    if ( preg_match_all($pattern, $file, $matches) ) {
        return false;
    } else {
        return true;
    }
}

?>
