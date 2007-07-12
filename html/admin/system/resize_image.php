<?php

require_once("../../require.php");

$file = NO_IMAGE_DIR;

// NO_IMAGE_DIR�ʳ��Υե�����̾���Ϥ��줿��硢�ե�����̾�Υ����å���Ԥ�
if ( isset($_GET['image']) && $_GET['image'] !== NO_IMAGE_DIR) {
    
    // �ե�����̾����������������$file������
    if ( lfCheckFileName() === true ) {
        $file = MODULE_PATH . $_GET['image'];
    } else {
        gfPrintLog('invalid access :resize_image.php $_GET["image"]=' . $_GET['image']);
    }
}

$objThumb = new gdthumb();

if(file_exists($file)){
    $objThumb->Main($file, $_GET["width"], $_GET["height"], "", true);
}else{
    $objThumb->Main(NO_IMAGE_DIR, $_GET["width"], $_GET["height"], "", true);
}

// �ե�����̾�η���������å�
function lfCheckFileName() {
    $pattern = '|^mdl_icons/icon_[a-zA-Z0-9_]+?\.[a-z]{3}$|';
    $file    = trim($_GET["image"]);
    if ( preg_match($pattern, $file) ) {
        return true;
    } else {
        return false;
    }
}

?>
