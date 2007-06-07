<?php


$TEST_DIR = realpath(dirname( __FILE__));
require_once($TEST_DIR . "/../../require.php");
require_once($TEST_DIR . "/../../../data/include/ftp.php");

// FTP画像保存相対パス
define("FTP_IMAGE_SAVE_DIR", "./html" . URL_DIR . "upload/save_image/");
// FTP画像一時保存相対パス
define("FTP_IMAGE_TEMP_DIR", "./html" . URL_DIR . "upload/temp_image/");
define ("IMAGE_TEMP_DIR", HTML_PATH . "upload/temp_image/");                // 画像一時保存
define ("IMAGE_SAVE_DIR", HTML_PATH . "upload/save_image/");                // 画像保存先


ftpMoveTempFile(FTP_IMAGE_SAVE_DIR);

//----------------------------------------------------------------------------

// ファイルの更新を他サーバにも反映する。
function ftpMoveTempFile($ftp_image_save_dir) {
    global $arrWEB_SERVERS;
    
    $arrFiles = array();
    $arrFiles[] = test.jpg;
    
    // 負荷分散している全てのサーバにファイルをコピーする
    foreach($arrFiles as $files) {
        foreach($arrWEB_SERVERS as $array) {
               $dst_path = $ftp_image_save_dir . $files;
               $src_path = IMAGE_SAVE_DIR . $files;
               sfFtpCopy($array['host'], $array['user'], $array['pass'], $dst_path, $src_path);            
        }
    }
}

?>