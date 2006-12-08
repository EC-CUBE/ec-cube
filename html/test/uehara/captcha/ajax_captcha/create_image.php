<?
// セッションスタート
session_start();
// 画像イメージ生成 
create_image();
exit(); 

function create_image() 
{ 
    // ランダムな文字列を生成
    $md5_hash = md5(rand(0,999)); 
    // 文字列を５桁にする 
    $security_code = substr($md5_hash, 15, 5); 
    // セッションに生成されたコードを保存
    $_SESSION["security_code"] = $security_code;

    //　イメージサイズ定義
    $width = 120; 
    $height = 30;  

    $image = ImageCreate($width, $height);  

    // 色の定義 
    $white = ImageColorAllocate($image, 255, 255, 255); 
    $black = ImageColorAllocate($image, 0, 0, 0); 
    $grey = ImageColorAllocate($image, 204, 204, 204); 

    // 背景色
    ImageFill($image, 0, 0, $white); 

    // 生成したコードを表示
    ImageString($image, 3, 30, 3, $security_code, $black); 
/*
    //Throw in some lines to make it a little bit harder for any bots to break 
    ImageRectangle($image,0,0,$width-1,$height-1,$grey); 
    imageline($image, 0, $height/2, $width, $height/2, $grey); 
    imageline($image, $width/2, 0, $width/2, $height, $grey); 
*/ 
    //Tell the browser what kind of file is come in 
    header("Content-Type: image/jpeg"); 
    // jpagで出力 
    ImageJpeg($image); 
    ImageDestroy($image); 
} 
?>