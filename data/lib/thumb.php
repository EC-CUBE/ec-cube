<?php
# MakeThumb(出力元画像パス（ファイル名まで）, 出力先フォルダ（/home/hoge/ など） , 最大横幅 , 最大縦幅 , 新ファイル名）
function MakeThumb($FromImgPath , $ToImgPath , $tmpMW , $tmpMH, $newFileName = ''){

# ◆◇◆　デフォルト値の設定　◆◇◆
# 必要に応じて変更して下さい。

# 画像の最大横幅（単位：ピクセル）
$ThmMaxWidth = 500;

# 画像の最大縦幅（単位：ピクセル）
$ThmMaxHeight = 500;

# サムネイル画像の接頭文字
$PreWord = $head;

# ◆◇◆　設定ここまで　◆◇◆

	//拡張子取得
	if (!$ext) {
		$array_ext = explode(".", $FromImgPath);
		$ext = $array_ext[count($array_ext) - 1];
	}
	
	$MW = $ThmMaxWidth;
	if($tmpMW) $MW = $tmpMW; # $MWに最大横幅セット	
	
	$MH = $ThmMaxHeight;
	if($tmpMH) $MH = $tmpMH; # $MHに最大縦幅セット
	
	if(empty($FromImgPath) || empty($ToImgPath)){ # エラー処理
		return array(0,"出力元画像パス、または出力先フォルダが指定されていません。");
	}
	
	if(!file_exists($FromImgPath)){ # エラー処理
		return array(0,"出力元画像が見つかりません。");
	}
	
	$size = @GetImageSize($FromImgPath);
	$re_size = $size;
	
	if(!$size[2] || $size[2] > 3){ # 画像の種類が不明 or swf
		return array(0,"画像形式がサポートされていません。");
	}

	//アスペクト比固定処理
	$tmp_w = $size[0] / $MW;
	
	if($MH != 0){
		$tmp_h = $size[1] / $MH;
	}
	
	if($tmp_w > 1 || $tmp_h > 1){
		if($MH == 0){
			if($tmp_w > 1){
				$re_size[0] = $MW;
				$re_size[1] = $size[1] * $MW / $size[0];
			}
		} else {
			if($tmp_w > $tmp_h){
				$re_size[0] = $MW;
				$re_size[1] = $size[1] * $MW / $size[0];
			} else {
				$re_size[1] = $MH;
				$re_size[0] = $size[0] * $MH / $size[1];
			}
		}
	}	
	
	# サムネイル画像ファイル名作成処理
	$tmp = array_pop(explode("/",$FromImgPath)); # /の一番最後を切り出し
	$FromFileName = array_shift(explode(".",$tmp)); # .で区切られた部分を切り出し
	$ToFile = $PreWord.$FromFileName; # 拡張子以外の部分までを作成
	
	$ImgNew = imagecreatetruecolor($re_size[0],$re_size[1]);
	
	switch($size[2]) {
	 	case "1": //gif形式
			if($tmp_w <= 1 && $tmp_h <= 1){
				if ( $newFileName ) {
					$ToFile = $newFileName;
				} elseif  ($ext) {
					$ToFile .= "." . $ext;
				} else {
					$ToFile .= ".gif";
				}
				if(!@copy($FromImgPath , $ToImgPath.$ToFile)) { # エラー処理
					return array(0,"ファイルのコピーに失敗しました。");
				}
				ImageDestroy($ImgNew);
				return array(1,$ToFile);
			}
					
			ImageColorAllocate($ImgNew,255,235,214); //背景色
			$black = ImageColorAllocate($ImgNew,0,0,0);
			$red = ImageColorAllocate($ImgNew,255,0,0);
			Imagestring($ImgNew,4,5,5,"GIF $size[0]x$size[1]", $red);
			ImageRectangle ($ImgNew,0,0,($re_size[0]-1),($re_size[1]-1),	$black);
			
			if ( $newFileName ) {
				$ToFile = $newFileName;
			} elseif($ext) {
				$ToFile .= "." . $ext;
			} else {
				$ToFile .= ".png";
			}
			$TmpPath = $ToImgPath.$ToFile;
			@Imagepng($ImgNew,$TmpPath);
			if(!@file_exists($TmpPath)){ # 画像が作成されていない場合
				return array(0,"画像の出力に失敗しました。");
			}
			ImageDestroy($ImgNew);
			return array(1,$ToFile);
			
	 	case "2": //jpg形式
			$ImgDefault = ImageCreateFromJpeg($FromImgPath);
			//ImageCopyResized( $ImgNew,$ImgDefault, 0, 0, 0, 0,$re_size[0], $re_size[1],$size[0], $size[1]);
			
            if($re_size[0] != $size[0] || $re_size[0] != $size[0]) {
                ImageCopyResampled( $ImgNew,$ImgDefault, 0, 0, 0, 0,$re_size[0], $re_size[1],$size[0], $size[1]);
            }
            
            gfDebugLog($size);
            gfDebugLog($re_size);
                        
            
            
			if ( $newFileName ) {
				$ToFile = $newFileName;
			} elseif($ext) {
				$ToFile .= "." . $ext;
			} else {
				$ToFile .= ".jpg";
			}
			$TmpPath = $ToImgPath.$ToFile;
			@ImageJpeg($ImgNew,$TmpPath);
			if(!@file_exists($TmpPath)){ # 画像が作成されていない場合
				return array(0,"画像の出力に失敗しました。<br>${ImgNew}<br>${TmpPath}");
			}
			$RetVal = $ToFile;
	 		break;
	 		
	 	case "3": //png形式
			$ImgDefault = ImageCreateFromPNG($FromImgPath);
			//ImageCopyResized($ImgNew, $ImgDefault, 0, 0, 0, 0,$re_size[0], $re_size[1],$size[0], $size[1]);
			ImageCopyResampled($ImgNew, $ImgDefault, 0, 0, 0, 0,$re_size[0], $re_size[1],$size[0], $size[1]);
			
			if ( $newFileName ) {
				$ToFile = $newFileName;
			} elseif ($ext) {
				$ToFile .= "." . $ext;
			} else {
				$ToFile .= ".png";
			}
			$TmpPath = $ToImgPath.$ToFile;
			@ImagePNG($ImgNew,$TmpPath );
			if(!@file_exists($TmpPath)){ # 画像が作成されていない場合
				return array(0,"画像の出力に失敗しました。");
			}
			$RetVal = $ToFile;
			break;
	}
	
	ImageDestroy($ImgDefault);
	ImageDestroy($ImgNew);
	
	return array(1,$RetVal);
}
?>