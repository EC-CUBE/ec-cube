<?php
# MakeThumb(���ϸ������ѥ��ʥե�����̾�ޤǡ�, ������ե������/home/hoge/ �ʤɡ� , ���粣�� , ������� , ���ե�����̾��
function MakeThumb($FromImgPath , $ToImgPath , $tmpMW , $tmpMH, $newFileName = ''){

# ���������ǥե�����ͤ����ꡡ������
# ɬ�פ˱������ѹ����Ʋ�������

# �����κ��粣����ñ�̡��ԥ������
$ThmMaxWidth = 500;

# �����κ��������ñ�̡��ԥ������
$ThmMaxHeight = 500;

# ����ͥ����������Ƭʸ��
$PreWord = $head;

# �����������ꤳ���ޤǡ�������

	//��ĥ�Ҽ���
	if (!$ext) {
		$array_ext = explode(".", $FromImgPath);
		$ext = $array_ext[count($array_ext) - 1];
	}
	
	$MW = $ThmMaxWidth;
	if($tmpMW) $MW = $tmpMW; # $MW�˺��粣�����å�	
	
	$MH = $ThmMaxHeight;
	if($tmpMH) $MH = $tmpMH; # $MH�˺���������å�
	
	if(empty($FromImgPath) || empty($ToImgPath)){ # ���顼����
		return array(0,"���ϸ������ѥ����ޤ��Ͻ�����ե���������ꤵ��Ƥ��ޤ���");
	}
	
	if(!file_exists($FromImgPath)){ # ���顼����
		return array(0,"���ϸ����������Ĥ���ޤ���");
	}
	
	$size = @GetImageSize($FromImgPath);
	$re_size = $size;
	
	if(!$size[2] || $size[2] > 3){ # �����μ��ब���� or swf
		return array(0,"�������������ݡ��Ȥ���Ƥ��ޤ���");
	}

	//�����ڥ�����������
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
	
	# ����ͥ�������ե�����̾��������
	$tmp = array_pop(explode("/",$FromImgPath)); # /�ΰ��ֺǸ���ڤ�Ф�
	$FromFileName = array_shift(explode(".",$tmp)); # .�Ƕ��ڤ�줿��ʬ���ڤ�Ф�
	$ToFile = $PreWord.$FromFileName; # ��ĥ�Ұʳ�����ʬ�ޤǤ����
	
	$ImgNew = imagecreatetruecolor($re_size[0],$re_size[1]);
	
	switch($size[2]) {
	 	case "1": //gif����
			if($tmp_w <= 1 && $tmp_h <= 1){
				if ( $newFileName ) {
					$ToFile = $newFileName;
				} elseif  ($ext) {
					$ToFile .= "." . $ext;
				} else {
					$ToFile .= ".gif";
				}
				if(!@copy($FromImgPath , $ToImgPath.$ToFile)) { # ���顼����
					return array(0,"�ե�����Υ��ԡ��˼��Ԥ��ޤ�����");
				}
				ImageDestroy($ImgNew);
				return array(1,$ToFile);
			}
					
			ImageColorAllocate($ImgNew,255,235,214); //�طʿ�
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
			if(!@file_exists($TmpPath)){ # ��������������Ƥ��ʤ����
				return array(0,"�����ν��Ϥ˼��Ԥ��ޤ�����");
			}
			ImageDestroy($ImgNew);
			return array(1,$ToFile);
			
	 	case "2": //jpg����
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
			if(!@file_exists($TmpPath)){ # ��������������Ƥ��ʤ����
				return array(0,"�����ν��Ϥ˼��Ԥ��ޤ�����<br>${ImgNew}<br>${TmpPath}");
			}
			$RetVal = $ToFile;
	 		break;
	 		
	 	case "3": //png����
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
			if(!@file_exists($TmpPath)){ # ��������������Ƥ��ʤ����
				return array(0,"�����ν��Ϥ˼��Ԥ��ޤ�����");
			}
			$RetVal = $ToFile;
			break;
	}
	
	ImageDestroy($ImgDefault);
	ImageDestroy($ImgNew);
	
	return array(1,$RetVal);
}
?>