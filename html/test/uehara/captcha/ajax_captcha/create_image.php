<?
// ���å���󥹥�����
session_start();
// �������᡼������ 
create_image();
exit(); 

function create_image() 
{ 
    // �������ʸ���������
    $md5_hash = md5(rand(0,999)); 
    // ʸ����򣵷�ˤ��� 
    $security_code = substr($md5_hash, 15, 5); 
    // ���å������������줿�����ɤ���¸
    $_SESSION["security_code"] = $security_code;

    //�����᡼�����������
    $width = 120; 
    $height = 30;  

    $image = ImageCreate($width, $height);  

    // ������� 
    $white = ImageColorAllocate($image, 255, 255, 255); 
    $black = ImageColorAllocate($image, 0, 0, 0); 
    $grey = ImageColorAllocate($image, 204, 204, 204); 

    // �طʿ�
    ImageFill($image, 0, 0, $white); 

    // �������������ɤ�ɽ��
    ImageString($image, 3, 30, 3, $security_code, $black); 
/*
    //Throw in some lines to make it a little bit harder for any bots to break 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
    imageline($image, 0, $height/2, $width, $height/2, $grey); 
    imageline($image, $width/2, 0, $width/2, $height, $grey); 
*/ 
    //Tell the browser what kind of file is come in 
    header("Content-Type: image/jpeg"); 
    // jpag�ǽ��� 
    ImageJpeg($image); 
    ImageDestroy($image); 
} 
?>