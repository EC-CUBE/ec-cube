<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*----------------------------------------------------------------------
 * [̾��] GC_Pdf
 * [����] Pdf�ե������ɽ�����롣(PDFLibɬ��)
 *----------------------------------------------------------------------
 */

// ����åɤ�ʸ���δֳ� 
define("GRID_SPACE", 4);

class SC_Pdf {
	var $arrText;
	var $arrImage;
	var $license_key;
	var $block_option;
	var $src_code;
	var $dst_code;
	var $pdiwarning;
	var $pdfpath;
	var $page_close;
			
	function SC_Pdf($width = 595, $height = 842, $fontsize = 10) {
		$this->license_key = "B600602-010400-714251-5851C1";
		$this->src_code = CHAR_CODE;
		// UTF-8�Ǥʤ��ȥ֥�å���ǲ��ԤǤ��ʤ���
		$this->dst_code = "UTF-8";
		// PDF BLOCK�Υץ�ѥƥ�
		$this->block_option = "encoding=UniJIS-UCS2-H textformat=utf8 fontname=HeiseiMin-W3 textflow=true";
		// �ٹ�ɽ��
		$this->pdiwarning = "true";	
		// �ڡ�������������
		$this->width = $width;
		$this->height = $height;
		// PDF�����
		$this->pdf = PDF_new();
		PDF_set_parameter($this->pdf, "license", $this->license_key);
		PDF_set_parameter($this->pdf, "pdiwarning", $this->pdiwarning);
		// �ɥ�����ȳ���
		PDF_begin_document($this->pdf, NULL, NULL);
		// �ڡ����ξ���
		$this->page_open = false;
		// �ơ��֥�ο�����
		$this->setTableColor();
		// �ե���ȥ�����������
		$this->fontsize = $fontsize;
		// ����å�������ü����
		$this->arrLines = array();
		// �ơ��֥륿���ȥ�Υ�������
		$this->arrHeaderColSize = array();
		$this->arrHeaderAlign = array();
		// �ơ��֥�������
		$this->table_left = 0;
		// �����ȥ�Ԥν���
		$this->title_enable = true;
		// ����åɤν���
		$this->grid_enable = true;
	}
	
	// �����ȥ����Ϥ��뤫�ݤ�
	function setTitleEnable($flag) {
		$this->title_enable = $flag;
	}
	
	// ����åɤ���Ϥ��뤫�ݤ�
	function setGridEnable($flag) {
		$this->grid_enable = $flag;
	}
		
		
	// �������֥�å�̾���͡�ɽ���ƥ����ȤΥϥå�������򥻥åȤ��롣
	function setTextBlock($list) {
		unset($this->arrText);
		$this->arrText[] = $list;
	}
	
	// �������֥�å�̾���͡��ե�����ѥ��Υϥå�������򥻥åȤ��롣
	// ���ѥ��ϥɥ�����ȥ롼�Ȱʲ�
	function setImageBlock($list) {
		unset($this->arrImage);
		$this->arrImage[] = $list;
	}
	
	// ɽ���طʤȤʤ�ƥ�ץ졼�ȥե�����ѥ�
	// ���ѥ��ϥɥ�����ȥ롼�Ȱʲ�
	function setTemplate($pdfpath) {
		if(file_exists($pdfpath)) {
			$this->pdfpath = $pdfpath;
		} else {
			print("���ꤷ��PDF�ƥ�ץ졼�Ȥ�¸�ߤ��ޤ���".$pdfpath);
			exit;
		}
	}
	
	// �ơ��֥����������
	function setTableLeft($table_left) {
		$this->table_left = $table_left;
	}
	
	// ����å�������ü����
	function setGridLines($list) {
		$this->arrLines = $list;
	}
	
	// �ơ��֥륿���ȥ�Υ�����������
	function setTableHeaderStyle($arrColSize, $arrAlign) {
		$this->arrHeaderColSize = $arrColSize;
		$this->arrHeaderAlign = $arrAlign;
	}
	
	// �֥�å��ǡ����ν񤭹���(close����ȼ��󿷵��ڡ���)
	function writeBlock() {
		// �ƥ�ץ졼�Ȥ���Ѥ���
		if(!file_exists($this->pdfpath)) {
			return;
		}
		// ��¸PDF�Υɥ�����Ȥ����
		$doc = pdf_open_pdi($this->pdf, $this->pdfpath, NULL, 0 );
		// ��¸PDF�Υɥ�����Ȥ������ڡ��������
		$page = pdf_open_pdi_page($this->pdf, $doc, 1, NULL );
		// �ڡ����򳫤�
		$this->openPage();
		
		// ��¸PDF�Υڡ����������Ƥ�
		PDF_fit_pdi_page($this->pdf, $page, 0, 0, "adjustpage");
		
		// �ƥ����ȥ֥�å��ν񤭹���
		$max = count($this->arrText);
		for($i = 0;$i < $max; $i++) {
			foreach($this->arrText[$i] as $key => $val) {
				if($val != "") {
					// ʸ�������ɤ��Ѵ�
					mb_convert_variables($this->dst_code, $this->src_code, $val);
					// �񤭹���
					$ret = PDF_fill_textblock($this->pdf, $page, $key, $val, $this->block_option);
				}
			}
		}
		
		// ���᡼���֥�å��ν񤭹���
		$max = count($this->arrImage);
		for($i = 0;$i < $max; $i++) {
			foreach($this->arrImage[$i] as $key => $val) {
				if($val != "") {
					$img = PDF_load_image($this->pdf, "auto", $val, NULL );
					$ret = PDF_fill_imageblock($this->pdf, $page, $key, $img, NULL);
				}
			}
		}
		
		// ������Ƥ��ڡ������Ĥ���
		PDF_close_pdi_page($this->pdf, $page);
		// ������Ƥ��ɥ�����Ȥ��Ĥ���
		PDF_close_pdi($this->pdf, $doc);
	}
	
	// �ڡ������Ĥ���
	function closePage() {
		if($this->page_open) {
			// �ڡ������Ĥ���
			PDF_end_page_ext($this->pdf, NULL);
			$this->page_open = false;
		}		
	}
	
	// �ڡ����򳫤�
	function openPage() {
		if(!$this->page_open) {
			// �������ڡ����򳫤�	
			PDF_begin_page_ext($this->pdf, $this->width, $this->height, NULL);
			$this->page_open = true;
		}
	}
	
	// �������ڡ����򳫤�
	function newPage() {
		PDF_end_page_ext($this->pdf, NULL);
		PDF_begin_page_ext($this->pdf, $this->width, $this->height, NULL);
	}
	
	// �����ƥ��֤ʥڡ����Υ��������������
	function getSize() {
		$this->openPage();
		$x = PDF_get_value($this->pdf, 'pagewidth', 0);
		$y = PDF_get_value($this->pdf, 'pageheight', 0);
		return array($x, $y);
	}
	
	// ��ɸ�������ؤ��Ƽ�������(����(0,0)�򺸾�(0,0)���Ѵ�)
	function posTopDown($x, $y) {
		$width = 0;
		$height = 0;
		list($width, $height) = $this->getSize();
		// x��ɸ�ϡ��ѹ���ɬ�פʤ�
		$pdf_x = $x;
		$pdf_y = $height - $y;
		return array($pdf_x, $pdf_y);
	}
	
	// �ơ��֥륫�顼������
	function setTableColor($frame_color = "000000", $title_color = "F0F0F0", $line_color = "D1DEFE", $last_color = "FDCBFE") {
		$this->frame_color = $frame_color;
		$this->title_color = $title_color;
		$this->line_color = $line_color;
		$this->last_color = $last_color;
	}
	
	// �ơ��֥�Υ���åɤ�ɽ�����롣
	function writeGrid($x, $y, $arrCol, $line_max, $last_color_flg = true) {
		// �ơ��֥���
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
		}
		
		if($this->title_enable) { 
			// �����ȥ륰��å�����
			$this->writeFrameRect($x, $y + GRID_SPACE, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->title_color, $this->frame_color);
		}
		
		// ����å��ü���ꤢ��
		if(count($this->arrLines) > 0) {
			$count = count($this->arrLines);
			$pos = 0;
			for($i = 0; $i < $count; $i++) {
				if(($i % 2) != 0) {
					// �Ԥδֳ�
					$down = ($pos + 1) * $this->fontsize * 1.5;
					// ���褹����������
					$height = ($this->fontsize + GRID_SPACE) * $this->arrLines[$i] + ($this->arrLines[$i] - 1);
					// �ԥ���å�����
					$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $height, $this->line_color);
				}
				$pos += $this->arrLines[$i];	
			}						
		} else {
			for($i = 1; $i <= $line_max; $i++) {
				if(($i % 2) == 0) {
					// �Ԥδֳ�
					$down = $i * $this->fontsize * 1.5;
					// �ԥ���å�����
					$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->line_color);
				}
			}
			// �ǽ��Ԥ˿���Ĥ�����
			if($last_color_flg) {
				// �Ԥδֳ�
				$down = $line_max * $this->fontsize * 1.5;
				// �ԥ���å�����
				$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->last_color);
			}
		}
	}
	
	// ����å��ѤΥ�������饤������
	/*
		$x			:�ơ��֥볫�ϰ���X��
		$y			:�ơ��֥볫�ϰ���Y��
		$arrCol		:����ॵ����������
		$line		:��������饤��������
		$start_col	:��������饤�󳫻ϥ����(0:���ϥ����)
	 */
	function writeUnderLine($x, $y, $arrCol, $line, $start_col = 0) {
		// �ơ��֥���
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
		}
		
		$start_x = 0;
		for($i = 0; $i < $start_col; $i++) {
			$start_x += $arrCol[$i];
		}
		
		// ��������饤���Y��ɸ�����
		$down = ($line + 1) * $this->fontsize * 1.5;
		// �ԥ���å�����
		$sx = $x + $start_x + GRID_SPACE + $this->table_left;
		$sy = $y + GRID_SPACE + $down - 1;
		$ex = $x + $width + GRID_SPACE;
		$ey = $sy;
				
		$this->writeLine($sx, $sy, $ex, $ey);		
	}
	
	// �����沣���֤����
	function getXCenter($width) {
		$page_width = 0;
		$page_height = 0;
		list($page_width, $page_height) = $this->getSize();
		$x = ($page_width - $width) / 2;
		return $x;
	}
	
	// ��ư����褻
	function writeTableCenter($table, $y, $arrCol, $arrAlign, $line_max = 256, $start_no = 1, $last_color_flg = false) {
		// �ơ��֥륵��������
		$width = 0;
		foreach($arrCol as $val) {
			$width += $val;
		}
		// ����褻���ּ���
		$x = $this->getXCenter($width) + $this->table_left;
		list($ret_x, $ret_y) = $this->writeTable($table, $x, $y, $arrCol, $arrAlign, $line_max, $start_no, $last_color_flg);
		// X���κ�ɸ���֤�
		return array($ret_x, $ret_y);
	}
	
	// �ǡ����ν񤭹���(close����ȼ��󿷵��ڡ���)
	// $start_no:1����(�����ȥ�)��0�Ȥ��롣
	// $line_max:�����ȥ��ޤޤʤ��Կ�
	function writeTable($table, $x, $y, $arrCol, $arrAlign, $line_max = 256, $start_no = 1, $last_color_flg = false) {
		$this->openPage();
		
		$table = ereg_replace("\n$", "", $table);
				
		$arrRet = split("\n", $table);
								
		if($line_max > (count($arrRet) - $start_no)) {
			$line_max = count($arrRet) - $start_no;
		}
		
		// �����ȥ�ͭ��
		if($this->grid_enable) {
			// ����åɤ�����
			$this->writeGrid($x, $y, $arrCol, $line_max, $last_color_flg);
		}
		
		// Unicode���󥳡��ǥ��󥰤Ȥ���UTF-8������
		PDF_set_parameter($this->pdf, "textformat", "utf8");
		
		// �����ȥ�ͭ��
		if($this->title_enable) {
			if(count($this->arrHeaderColSize) > 0 && count($this->arrHeaderAlign) > 0 ) {
				list($linecol, $aligncol, $width) = $this->getTableOption($this->arrHeaderColSize, $this->arrHeaderAlign);
			} else {
				list($linecol, $aligncol, $width) = $this->getTableOption($arrCol, $arrAlign);
			}	
						
			// �����ȥ�Ԥν񤭹���
			$option = "ruler {" . $linecol . "} ";
			$option.= "tabalignment {" . $aligncol . "} ";
			$fontsize =  $this->fontsize;
			$option.= "hortabmethod ruler leading=150% fontname=HeiseiKakuGo-W5 fontsize=$fontsize encoding=UniJIS-UCS2-H";
			
			$this->writeTableData($table, $x, $y, $width, 0, 0, $option);
		}
		
		list($linecol, $aligncol, $width) = $this->getTableOption($arrCol, $arrAlign);
		
		// �ǡ����Ԥν񤭹���
		$option = "ruler {" . $linecol . "} ";
		$option.= "tabalignment {" . $aligncol . "} ";
		$option.= "hortabmethod ruler leading=150% fontname=HeiseiMin-W3 fontsize=$this->fontsize encoding=UniJIS-UCS2-H";
		
		if($start_no <= 0) {
			$start_no = 1;
			$end_no = $line_max;
		} else {
			$end_no = $start_no + $line_max - 1;
		}
		
		$y += $this->fontsize * 1.5;
		
		list($ret_x, $ret_y) = $this->writeTableData($table, $x, $y, $width, $start_no, $end_no, $option);
		
		return array($ret_x, $ret_y);
	}
	
	function getTableOption($arrCol, $arrAlign) {
		// ����ॵ����
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
			$linecol.= $width . " ";
		}
		
		// ��������
		$max = count($arrAlign);
		for($i = 0; $i < $max; $i++) {
			$aligncol.= $arrAlign[$i] . " ";
		}
		
		return array($linecol, $aligncol, $width);
	}
	
	// �ơ��֥�ǡ����ν񤭹���
	function writeTableData($table, $x, $y, $table_width, $start_no, $end_no, $option) {
		$arrLine = split("\n", $table);
		for($i = $start_no; $i <= $end_no; $i++) {
			$line.=$arrLine[$i] . "\n";
		}
				
		// �ơ��֥���֤����
		list($pdf_x, $pdf_y) = $this->posTopDown($x, $y);
						
		// �ơ��֥�⤵�����
		$table_height = $this->fontsize * 1.5 * ($end_no - $start_no + 1);
		// �ơ��֥뱦����y��ɸ�����
		$end_y = $pdf_y - $table_height;
		if($end_y < 0) {
			$end_y = 0;
		}
		$enc_table = mb_convert_encoding($line, "utf-8", CHAR_CODE);
				
		$tf = PDF_create_textflow($this->pdf, $enc_table, $option);

		PDF_fit_textflow($this->pdf, $tf, $pdf_x, $pdf_y, $pdf_x + $table_width, $end_y, NULL);
		PDF_delete_textflow($this->pdf, $tf);
		
		// �ơ��֥뺸����ɸ���֤�
		return array($x, $y + $table_height);		
	}
		
	// ��������
	function setColor($rgb) {
		if($rgb != "") {
			list($r, $g, $b) = sfGetPdfRgb($rgb);
			PDF_setcolor($this->pdf, "fillstroke", "rgb", $r, $g, $b, 0);	
		}
	}
	
	// û��������
	function writeRect($x, $y, $width, $height, $rgb = "") {
		$this->openPage();
		list($pdf_x, $pdf_y) = $this->posTopDown($x, $y);
		$this->setColor($rgb);
		PDF_rect($this->pdf, $pdf_x,$pdf_y,$width,-$height);
		PDF_fill($this->pdf);
	}
	
	// ���դ�û��������
	function writeFrameRect($x, $y, $width, $height, $rgb, $frgb) {
		$this->openPage();
		list($pdf_x, $pdf_y) = $this->posTopDown($x, $y);
		$this->setColor($frgb);
		PDF_rect($this->pdf, $pdf_x,$pdf_y,$width,-$height);
		PDF_fill($this->pdf);
		
		$this->setColor($rgb);
		PDF_rect($this->pdf, $pdf_x+1,$pdf_y-1,$width-2,-$height+2);
		PDF_fill($this->pdf);		
	}
	
	// ľ��������
	function writeLine($sx, $sy, $ex, $ey, $rgb = "000000") {
		$this->openPage();
		list($pdf_sx, $pdf_sy) = $this->posTopDown($sx, $sy);
		list($pdf_ex, $pdf_ey) = $this->posTopDown($ex, $ey);
		$this->setColor($rgb);
		PDF_setlinewidth($this->pdf, 1.0);
		PDF_moveto($this->pdf, $pdf_sx, $pdf_sy);
		PDF_lineto($this->pdf, $pdf_ex, $pdf_ey);
		PDF_stroke($this->pdf);
	}
		
	// �ե�����Υ��������
	function output($filekey = "") {
		if(isset($this->pdf)) {
			// �ڡ������Ĥ���
			$this->closePage();
			// PDF�ν�λ
			PDF_end_document($this->pdf, NULL);
			// �����ѥǡ����μ��� 
			$buf = PDF_get_buffer($this->pdf);
			$filename = $filekey . date("ymdHis").".pdf";
						
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: application/octet-stream; name=$filename");
					
			/*
			 * session_start()������˸ƤӽФ��Ƥ�����˽��Ϥ����ʲ��Υإå��ϡ�
			 * URLľ�ܸƤӽФ����˥��顼��ȯ��������ΤǶ��ˤ��Ƥ�����
			 *
			 * Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
			 * Progma: no-cache
			 *
			 */
			header("Cache-Control: ");
			header("Pragma: ");
			print $buf;
			
			// PDF����
			PDF_delete($this->pdf);
		} else {
			print("PDF����������Ƥ��ޤ���");
		}
		exit;		
	}
	
	// �ե������ɽ��
	function display() {
		if(isset($this->pdf)) {
			// �ڡ������Ĥ���
			$this->closePage();
			// PDF�ν�λ
			PDF_end_document($this->pdf, NULL);
			
			// �����ѥǡ����μ��� 
			$buf = PDF_get_buffer($this->pdf);
			$len = strlen($buf);
			header("Content-type: application/pdf");
			header("Content-Length: $len");
			header("Content-Disposition: inline; filename=". date("YmdHis").".pdf");
								
			/*
			 * session_start()������˸ƤӽФ��Ƥ�����˽��Ϥ����ʲ��Υإå��ϡ�
			 * URLľ�ܸƤӽФ����˥��顼��ȯ��������ΤǶ��ˤ��Ƥ�����
			 *
			 * Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
			 * Progma: no-cache
			 *
			 */
			header("Cache-Control: ");
			header("Pragma: ");
			print $buf;
			
			// PDF����
			PDF_delete($this->pdf);
		} else {
			print("PDF����������Ƥ��ޤ���");
		}
		exit;
	}
}

?>