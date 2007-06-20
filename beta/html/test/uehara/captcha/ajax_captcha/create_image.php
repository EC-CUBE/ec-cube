<?php

// セッションスタート
session_start();

// 画像イメージ生成 
// ランダムな文字列を生成
$md5_hash = md5(rand(0,999)); 
// 文字列を５桁にする 
$code = substr($md5_hash, 15, 5); 
// セッションに生成されたコードを保存
$_SESSION["code"] = $code;

$image = ImageCreate(120, 20);  

// 色の定義 
$white = ImageColorAllocate($image, 255, 255, 255); 
$grey = ImageColorAllocate($image, 204, 204, 204); 

// 背景色
ImageFill($image, 0, 0, $grey); 

// 生成したコードを表示
ImageString($image, 5, 30, 3, $code, $white); 

// jpagで出力 
header("Content-Type: image/jpeg"); 
ImageJpeg($image); 
ImageDestroy($image); 

exit; 
?>