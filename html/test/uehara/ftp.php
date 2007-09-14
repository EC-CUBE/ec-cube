<?php


$TEST_DIR = realpath(dirname( __FILE__));
require_once($TEST_DIR . "/../../require.php");
require_once($TEST_DIR . "/../../../data/include/ftp.php");

// FTP������¸���Хѥ�
define("FTP_IMAGE_SAVE_DIR", "./html" . URL_DIR . "upload/save_image/");
// FTP���������¸���Хѥ�
define("FTP_IMAGE_TEMP_DIR", "./html" . URL_DIR . "upload/temp_image/");
define ("IMAGE_TEMP_DIR", HTML_PATH . "upload/temp_image/");                // ���������¸
define ("IMAGE_SAVE_DIR", HTML_PATH . "upload/save_image/");                // ������¸��


ftpMoveTempFile(FTP_IMAGE_TEMP_DIR);

//----------------------------------------------------------------------------

// �ե�����ι�����¾�����Фˤ�ȿ�Ǥ��롣
function ftpMoveTempFile($ftp_image_save_dir) {
    global $arrWEB_SERVERS;
    
    $arrFiles = array();
    $arrFiles[] = '06081024_4668afcc10571.jpg';
    
    // ���ʬ�����Ƥ������ƤΥ����Ф˥ե�����򥳥ԡ�����
    foreach($arrFiles as $files) {
        foreach($arrWEB_SERVERS as $array) {
               $dst_path = $ftp_image_save_dir . $files;
               $src_path = IMAGE_TEMP_DIR . $files;
               
echo $dst_path;
               sfFtpDelete($array['host'], $array['user'], $array['pass'], $dst_path);
//               sfFtpCopy($array['host'], $array['user'], $array['pass'], $dst_path, $src_path);            
        }
    }
}

?>