<?php
/* 

����������������������������������������������������
GD��ư����ͥ������ + �����2006/02/03

Copyright 2002- Akihiro Asai. All rights reserved.

http://aki.adam.ne.jp
aki@mx3.adam.ne.jp

����������������������������������������������������

�� ��ǽ����
�����ꤵ�줿���᡼���Υ���ͥ����ɽ�����ޤ���
�����Ϥ����礭������ꤹ������Ǥ��ޤ���
�����Ϥ���륤�᡼���Υ����ڥ�����ϰݻ�����ޤ���

�� ������ˡ
����� gdthumb.php?path=xxx/xxx.[ jpg | png | gif ]&mw=xx&mh=xx
�� pass����ʬ�ˤϲ����ؤΥѥ������
�� mw��ɽ�������κ��粣����mh��ɽ�������κ��粣�������������ǽ��
�� ���ꤷ�ʤ��ä����ϥǥե���Ȥ������ͤ���ѡ�
�����饹�Ȥ��ƻ��Ѥ�����ϡ��֥��饹�Ȥ��ƻ��Ѥ�����ˤϡ������װʹߤ򥳥��ȥ����Ȥ��Ʋ�������

�� ��������
2002/08/19 �����������ʬ�������ľ��
2003/01/31 �ǥե���Ȥǥ����ڥ����椬����
2003/04/11 ���粣���Ⱥ�����������������ǽ
2003/04/25 GD2�Ѥ˴ؿ��ѹ�
2003/06/21 GD1/2��С������˱������ѹ��Ǥ���褦�˽���
2003/06/25 imageCopyResampled����ʬ����
2004/01/28 ������ץ����Τ��ľ����������pass�פ��path�פ��ѹ���
2005/12/08 �ؿ��μ�ưȽ�� gif�������б� Ʃ��gif��Ʃ��png���б���GD2.0.1�ʹߡ�  
*/

// ���饹�Ȥ��ƻ��Ѥ�����ˤϡ��ʲ���6�Ԥ򥳥��ȥ�����
/*
$objg = new gdthumb();
list($Ck, $Msg) = $objg->Main($_GET["path"], $_GET["mw"], $_GET["mh"]);
if(!$Ck) { // ���顼�ξ��
	header("Content-Type: text/html; charset=" . CHAR_CODE);
	print $Msg;
}
*/

class gdthumb {
	
	var $imgMaxWidth;
	var $imgMaxHeight;
	var $gdVer;
	
	/*
	* ���󥹥ȥ饯��
	*/
	function gdthumb() {
		
		// ������ץȤΥǥե��������
		
		// �����κ��粣��
		$this->imgMaxWidth = 240; // 1�ʾ����
		
		// �����κ������
		$this->imgMaxHeight = 0; // ���ꤷ�ʤ�����0 ���ꤹ�����1�ʾ����
		
	}
	
	/*
	* ����ͥ�������κ���
	* string $path
	* integer $width
	* integer $height
	*/
	function Main($path, $width, $height, $dst_file, $header = false) {
		
		if(!isset($path)) {
			return array(0, "���᡼���Υѥ������ꤵ��Ƥ��ޤ���");
		}
		
		if(!file_exists($path)) {
			return array(0, "���ꤵ�줿�ѥ��˥ե����뤬���Ĥ���ޤ���");
		}
		
		// �������礭���򥻥å�
		if($width) $this->imgMaxWidth = $width;
		if($height) $this->imgMaxHeight = $height;
		
		$size = @GetimageSize($path);
		$re_size = $size;
		
		//�����ڥ�����������
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
			
			// gif����
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
						// ��������
						if($header){
							header("Content-Type: image/gif");
							imagegif($dst_im);
							return "";
						}else{
                            $dst_file = $dst_file . ".gif";
		                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
		                        // ��������Ʊ�����ˤϡ����Τޤޥ��ԡ����롣(����������ɤ���           
		                        copy($path, $dst_file);
		                    } else {
		                        imagegif($dst_im, $dst_file);
		                    }
						}						
						imagedestroy($src_im);
						imagedestroy($dst_im);
					} else {
						// ��������
						if($header){
							header("Content-Type: image/png");
							imagepng($dst_im);
							return "";
						}else{
							$dst_file = $dst_file . ".png";
		                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
		                        // ��������Ʊ�����ˤϡ����Τޤޥ��ԡ����롣(����������ɤ���           
		                        copy($path, $dst_file);
		                    } else {
		                        imagepng($dst_im, $dst_file);
		                    }
						}
						imagedestroy($src_im);
						imagedestroy($dst_im);
					}
				} else {
					// ����ͥ�������ԲĤξ��ʵ�С�������к���
					$dst_im = imageCreate($re_size[0], $re_size[1]);
					imageColorAllocate($dst_im, 255, 255, 214); //�طʿ�
					
					// ������ʸ����������
					$black = imageColorAllocate($dst_im, 0, 0, 0);
					$red = imageColorAllocate($dst_im, 255, 0, 0);
					
					imagestring($dst_im, 5, 10, 10, "GIF $size[0]x$size[1]", $red);
					imageRectangle ($dst_im, 0, 0, ($re_size[0]-1), ($re_size[1]-1), $black);
					
					// ��������
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
				
			// jpg����
			case "2": 
			
				$src_im = imageCreateFromJpeg($path);
				$dst_im = $imagecreate($re_size[0], $re_size[1]);
                
                $imageresize( $dst_im, $src_im, 0, 0, 0, 0, $re_size[0], $re_size[1], $size[0], $size[1]);

				// ��������
				if($header){
					header("Content-Type: image/jpeg");
					imageJpeg($dst_im);
					return "";
				}else{
					$dst_file = $dst_file . ".jpg";
                    
                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
                        // ��������Ʊ�����ˤϡ����Τޤޥ��ԡ����롣(����������ɤ���       
                        copy($path, $dst_file);
                    } else {
                        imageJpeg($dst_im, $dst_file);
                    }
				}
				
				imagedestroy($src_im);
				imagedestroy($dst_im);
      			
				break;
    
			// png����    
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
				
				// ��������
				if($header){
					header("Content-Type: image/png");
					imagepng($dst_im);
					return "";
				}else{
					$dst_file = $dst_file . ".png";
                    if($re_size[0] == $size[0] && $re_size[1] == $size[1]) {
                        // ��������Ʊ�����ˤϡ����Τޤޥ��ԡ����롣(����������ɤ���           
                        copy($path, $dst_file);
                    } else {
                        imagepng($dst_im, $dst_file);
                    }
				}
				imagedestroy($src_im);
				imagedestroy($dst_im);
				
				break;
				
			default:
				return array(0, "���᡼���η����������Ǥ���");
		}

		return array(1, $dst_file);
	}
}
?>