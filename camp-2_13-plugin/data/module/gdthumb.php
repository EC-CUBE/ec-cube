<?php
/* 

◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆
GD自動サムネイル作成 + 中川修正2006/02/03

Copyright 2002- Akihiro Asai. All rights reserved.

http://aki.adam.ne.jp
aki@mx3.adam.ne.jp

◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆◆

□ 機能概要
・指定されたイメージのサムネイルを表示します。
・出力する大きさを指定する事ができます。
・出力されるイメージのアスペクト比は維持されます。

□ 使用方法
指定は gdthumb.php?path=xxx/xxx.[ jpg | png | gif ]&mw=xx&mh=xx
※ passの部分には画像へのパスを指定
※ mwに表示画像の最大横幅、mhに表示画像の最大横幅を外部より指定可能。
※ 指定しなかった場合はデフォルトの設定値を採用。
★クラスとして使用する場合は、「クラスとして使用する場合には・・・」以降をコメントアウトして下さい。

□ 更新履歴
2002/08/19 最大縦幅の部分を一部手直し
2003/01/31 デフォルトでアスペクト比が固定
2003/04/11 最大横幅と最大縦幅を外部より指定可能
2003/04/25 GD2用に関数変更
2003/06/21 GD1/2をバージョンに応じて変更できるように修正
2003/06/25 imageCopyResampledの部分を修正
2004/01/28 スクリプト全体を書き直し。引数「pass」を「path」に変更。
2005/12/08 関数の自動判別 gif形式に対応 透過gif・透過pngに対応（GD2.0.1以降）  
*/

// クラスとして使用する場合には、以下の6行をコメントアウト
/*
$objg = new gdthumb();
list($Ck, $Msg) = $objg->Main($_GET["path"], $_GET["mw"], $_GET["mh"]);
if(!$Ck) { // エラーの場合
	header("Content-Type: text/html; charset=" . CHAR_CODE);
	print $Msg;
}
*/

class gdthumb {
	
	var $imgMaxWidth;
	var $imgMaxHeight;
	var $gdVer;
	
	/*
	* コンストラクタ
	*/
	function gdthumb() {
		
		// スクリプトのデフォルト設定
		
		// 画像の最大横幅
		$this->imgMaxWidth = 240; // 1以上の値
		
		// 画像の最大縦幅
		$this->imgMaxHeight = 0; // 指定しない場合は0 指定する場合は1以上の値
		
	}
	
	/*
	* サムネイル画像の作成
	* string $path
	* integer $width
	* integer $height
	*/
	function Main($path, $width, $height, $dst_file, $header = false) {
		
		if(!isset($path)) {
			return array(0, "イメージのパスが設定されていません。");
		}
		
		if(!file_exists($path)) {
			return array(0, "指定されたパスにファイルが見つかりません。");
		}
		
		// 画像の大きさをセット
		if($width) $this->imgMaxWidth = $width;
		if($height) $this->imgMaxHeight = $height;
		
		$size = @GetimageSize($path);
		$re_size = $size;
		
		//アスペクト比固定処理
		if($this->imgMaxWidth != 0) {
			$tmp_w = $size[0] / $this->imgMaxWidth;
		}
		
		if($this->imgMaxHeight != 0) {
			$tmp_h = $size[1] / $this->imgMaxHeight;
		}
		
		if($tmp_w > 1 || $tmp_h > 1) {
			if($this->imgMaxHeight == 0) {
				if($tmp_w > 1) {
					$re_size[0] = $this->imgMaxWidth;
					$re_size[1] = $size[1] * $this->imgMaxWidth / $size[0];
				}
			} else {
				if($tmp_w > $tmp_h) {
					$re_size[0] = $this->imgMaxWidth;
					$re_size[1] = $size[1] * $this->imgMaxWidth / $size[0];
				} else {
					$re_size[1] = $this->imgMaxHeight;
					$re_size[0] = $size[0] * $this->imgMaxHeight / $size[1];
				}
			}
		}
		
		$imagecreate = function_exists("imagecreatetruecolor") ? "imagecreatetruecolor" : "imagecreate";
		$imageresize = function_exists("imagecopyresampled") ? "imagecopyresampled" : "imagecopyresized";

		switch($size[2]) {
			
			// gif形式
			case "1":
				if(function_exists("imagecreatefromgif")) {
					$src_im = imagecreatefromgif($path);
					$dst_im = $imagecreate($re_size[0], $re_size[1]);
					
					$transparent = imagecolortransparent($src_im);
					$colorstotal = imagecolorstotal ($src_im);
					
					$dst_im = imagecreate($re_size[0], $re_size[1]);
					if (0 <= $transparent && $transparent < $colorstotal) {
						imagepalettecopy ($dst_im, $src_im);
						imagefill ($dst_im, 0, 0, $transparent);
						imagecolortransparent ($dst_im, $transparent);
					}
          			$imageresize($dst_im, $src_im, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

					if(function_exists("imagegif")) {						
						// 画像出力
						if($header){
							header("Content-Type: image/gif");
							imagegif($dst_im);
							return "";
						}else{
                            $dst_file = $dst_file . ".gif";
		                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
		                        // サイズが同じ場合には、そのままコピーする。(画質劣化を防ぐ）           
		                        copy($path, $dst_file);
		                    } else {
		                        imagegif($dst_im, $dst_file);
		                    }
						}						
						imagedestroy($src_im);
						imagedestroy($dst_im);
					} else {
						// 画像出力
						if($header){
							header("Content-Type: image/png");
							imagepng($dst_im);
							return "";
						}else{
							$dst_file = $dst_file . ".png";
		                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
		                        // サイズが同じ場合には、そのままコピーする。(画質劣化を防ぐ）           
		                        copy($path, $dst_file);
		                    } else {
		                        imagepng($dst_im, $dst_file);
		                    }
						}
						imagedestroy($src_im);
						imagedestroy($dst_im);
					}
				} else {
					// サムネイル作成不可の場合（旧バージョン対策）
					$dst_im = imageCreate($re_size[0], $re_size[1]);
					imageColorAllocate($dst_im, 255, 255, 214); //背景色
					
					// 枠線と文字色の設定
					$black = imageColorAllocate($dst_im, 0, 0, 0);
					$red = imageColorAllocate($dst_im, 255, 0, 0);
					
					imagestring($dst_im, 5, 10, 10, "GIF $size[0]x$size[1]", $red);
					imageRectangle ($dst_im, 0, 0, ($re_size[0]-1), ($re_size[1]-1), $black);
					
					// 画像出力
					if($header){
						header("Content-Type: image/png");
						imagepng($dst_im);
						return "";
					}else{
						$dst_file = $dst_file . ".png";
						imagepng($dst_im, $dst_file);
					}
					imagedestroy($src_im);
					imagedestroy($dst_im);
				}
				break;
				
			// jpg形式
			case "2": 
			
				$src_im = imageCreateFromJpeg($path);
				$dst_im = $imagecreate($re_size[0], $re_size[1]);
                
                $imageresize( $dst_im, $src_im, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

				// 画像出力
				if($header){
					header("Content-Type: image/jpeg");
					imageJpeg($dst_im);
					return "";
				}else{
					$dst_file = $dst_file . ".jpg";

                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
                        // サイズが同じ場合には、そのままコピーする。(画質劣化を防ぐ）       
                        copy($path, $dst_file);
                    } else {
                        imageJpeg($dst_im, $dst_file);
                    }
				}
				
				imagedestroy($src_im);
				imagedestroy($dst_im);
      			
				break;
    
			// png形式    
			case "3": 

				$src_im = imageCreateFromPNG($path);
				
				$colortransparent = imagecolortransparent($src_im);
				if ($colortransparent > -1) {
					$dst_im = $imagecreate($re_size[0], $re_size[1]);
					imagepalettecopy($dst_im, $src_im);
					imagefill($dst_im, 0, 0, $colortransparent);
					imagecolortransparent($dst_im, $colortransparent);
					imagecopyresized($dst_im,$src_im, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
				} else {				
					$dst_im = $imagecreate($re_size[0], $re_size[1]);
					imagecopyresized($dst_im,$src_im, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);
					
					(imagecolorstotal($src_im) == 0) ? $colortotal = 65536 : $colortotal = imagecolorstotal($src_im);
					
					imagetruecolortopalette($dst_im, true, $colortotal);
				}
				
				// 画像出力
				if($header){
					header("Content-Type: image/png");
					imagepng($dst_im);
					return "";
				}else{
					$dst_file = $dst_file . ".png";
                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
                        // サイズが同じ場合には、そのままコピーする。(画質劣化を防ぐ）           
                        copy($path, $dst_file);
                    } else {
                        imagepng($dst_im, $dst_file);
                    }
				}
				imagedestroy($src_im);
				imagedestroy($dst_im);
				
				break;
				
			default:
				return array(0, "イメージの形式が不明です。");
		}

		return array(1, $dst_file);
	}
}
?>
