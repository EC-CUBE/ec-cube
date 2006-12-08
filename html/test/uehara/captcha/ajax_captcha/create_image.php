<?php

// セッションスタート
session_start();
// 画像イメージ生成 
create_image();

exit(); 

//---------------------------------------------------------------------------------------------------

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

    // 背景色
    ImageFill($image, 0, 0, $black); 

    // 生成したコードを表示
    ImageString($image, 3, 30, 3, $security_code, $white); 

    // jpagで出力 
    header("Content-Type: image/jpeg"); 
    ImageJpeg($image); 
    ImageDestroy($image); 
} 
?>