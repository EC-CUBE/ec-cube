<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */

/*----------------------------------------------------------------------
 * [名称] GC_Pdf
 * [概要] Pdfファイルを表示する。(PDFLib必須)
 *----------------------------------------------------------------------
 */

// グリッドと文字の間隔 
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
		// UTF-8でないとブロック内で改行できない。
		$this->dst_code = "UTF-8";
		// PDF BLOCKのプロパティ
		$this->block_option = "encoding=UniJIS-UCS2-H textformat=utf8 fontname=HeiseiMin-W3 textflow=true";
		// 警告表示
		$this->pdiwarning = "true";	
		// ページサイズ設定
		$this->width = $width;
		$this->height = $height;
		// PDF初期化
		$this->pdf = PDF_new();
		PDF_set_parameter($this->pdf, "license", $this->license_key);
		PDF_set_parameter($this->pdf, "pdiwarning", $this->pdiwarning);
		// ドキュメント開始
		PDF_begin_document($this->pdf, NULL, NULL);
		// ページの状態
		$this->page_open = false;
		// テーブルの色設定
		$this->setTableColor();
		// フォントサイズの設定
		$this->fontsize = $fontsize;
		// グリッド描画の特殊指定
		$this->arrLines = array();
		// テーブルタイトルのスタイル
		$this->arrHeaderColSize = array();
		$this->arrHeaderAlign = array();
		// テーブル補正値
		$this->table_left = 0;
		// タイトル行の出力
		$this->title_enable = true;
		// グリッドの出力
		$this->grid_enable = true;
	}
	
	// タイトルを出力するか否か
	function setTitleEnable($flag) {
		$this->title_enable = $flag;
	}
	
	// グリッドを出力するか否か
	function setGridEnable($flag) {
		$this->grid_enable = $flag;
	}
		
		
	// キー：ブロック名、値：表示テキストのハッシュ配列をセットする。
	function setTextBlock($list) {
		unset($this->arrText);
		$this->arrText[] = $list;
	}
	
	// キー：ブロック名、値：ファイルパスのハッシュ配列をセットする。
	// ※パスはドキュメントルート以下
	function setImageBlock($list) {
		unset($this->arrImage);
		$this->arrImage[] = $list;
	}
	
	// 表示背景となるテンプレートファイルパス
	// ※パスはドキュメントルート以下
	function setTemplate($pdfpath) {
		if(file_exists($pdfpath)) {
			$this->pdfpath = $pdfpath;
		} else {
			print("指定したPDFテンプレートは存在しません：".$pdfpath);
			exit;
		}
	}
	
	// テーブル位置補正値
	function setTableLeft($table_left) {
		$this->table_left = $table_left;
	}
	
	// グリッド描画の特殊指定
	function setGridLines($list) {
		$this->arrLines = $list;
	}
	
	// テーブルタイトルのスタイル設定
	function setTableHeaderStyle($arrColSize, $arrAlign) {
		$this->arrHeaderColSize = $arrColSize;
		$this->arrHeaderAlign = $arrAlign;
	}
	
	// ブロックデータの書き込み(closeすると次回新規ページ)
	function writeBlock() {
		// テンプレートを使用する
		if(!file_exists($this->pdfpath)) {
			return;
		}
		// 既存PDFのドキュメントを取得
		$doc = pdf_open_pdi($this->pdf, $this->pdfpath, NULL, 0 );
		// 既存PDFのドキュメントから指定ページを取得
		$page = pdf_open_pdi_page($this->pdf, $doc, 1, NULL );
		// ページを開く
		$this->openPage();
		
		// 既存PDFのページを割り当てる
		PDF_fit_pdi_page($this->pdf, $page, 0, 0, "adjustpage");
		
		// テキストブロックの書き込み
		$max = count($this->arrText);
		for($i = 0;$i < $max; $i++) {
			foreach($this->arrText[$i] as $key => $val) {
				if($val != "") {
					// 文字コードの変換
					mb_convert_variables($this->dst_code, $this->src_code, $val);
					// 書き込み
					$ret = PDF_fill_textblock($this->pdf, $page, $key, $val, $this->block_option);
				}
			}
		}
		
		// イメージブロックの書き込み
		$max = count($this->arrImage);
		for($i = 0;$i < $max; $i++) {
			foreach($this->arrImage[$i] as $key => $val) {
				if($val != "") {
					$img = PDF_load_image($this->pdf, "auto", $val, NULL );
					$ret = PDF_fill_imageblock($this->pdf, $page, $key, $img, NULL);
				}
			}
		}
		
		// 割り当てたページを閉じる
		PDF_close_pdi_page($this->pdf, $page);
		// 割り当てたドキュメントを閉じる
		PDF_close_pdi($this->pdf, $doc);
	}
	
	// ページを閉じる
	function closePage() {
		if($this->page_open) {
			// ページを閉じる
			PDF_end_page_ext($this->pdf, NULL);
			$this->page_open = false;
		}		
	}
	
	// ページを開く
	function openPage() {
		if(!$this->page_open) {
			// 新しいページを開く	
			PDF_begin_page_ext($this->pdf, $this->width, $this->height, NULL);
			$this->page_open = true;
		}
	}
	
	// 新しいページを開く
	function newPage() {
		PDF_end_page_ext($this->pdf, NULL);
		PDF_begin_page_ext($this->pdf, $this->width, $this->height, NULL);
	}
	
	// アクティブなページのサイズを取得する
	function getSize() {
		$this->openPage();
		$x = PDF_get_value($this->pdf, 'pagewidth', 0);
		$y = PDF_get_value($this->pdf, 'pageheight', 0);
		return array($x, $y);
	}
	
	// 座標を入れ替えて取得する(左下(0,0)を左上(0,0)に変換)
	function posTopDown($x, $y) {
		$width = 0;
		$height = 0;
		list($width, $height) = $this->getSize();
		// x座標は、変更の必要なし
		$pdf_x = $x;
		$pdf_y = $height - $y;
		return array($pdf_x, $pdf_y);
	}
	
	// テーブルカラーの設定
	function setTableColor($frame_color = "000000", $title_color = "F0F0F0", $line_color = "D1DEFE", $last_color = "FDCBFE") {
		$this->frame_color = $frame_color;
		$this->title_color = $title_color;
		$this->line_color = $line_color;
		$this->last_color = $last_color;
	}
	
	// テーブルのグリッドを表示する。
	function writeGrid($x, $y, $arrCol, $line_max, $last_color_flg = true) {
		// テーブル幅
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
		}
		
		if($this->title_enable) { 
			// タイトルグリッド描画
			$this->writeFrameRect($x, $y + GRID_SPACE, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->title_color, $this->frame_color);
		}
		
		// グリッド特殊指定あり
		if(count($this->arrLines) > 0) {
			$count = count($this->arrLines);
			$pos = 0;
			for($i = 0; $i < $count; $i++) {
				if(($i % 2) != 0) {
					// 行の間隔
					$down = ($pos + 1) * $this->fontsize * 1.5;
					// 描画する縦幅を求める
					$height = ($this->fontsize + GRID_SPACE) * $this->arrLines[$i] + ($this->arrLines[$i] - 1);
					// 行グリッド描画
					$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $height, $this->line_color);
				}
				$pos += $this->arrLines[$i];	
			}						
		} else {
			for($i = 1; $i <= $line_max; $i++) {
				if(($i % 2) == 0) {
					// 行の間隔
					$down = $i * $this->fontsize * 1.5;
					// 行グリッド描画
					$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->line_color);
				}
			}
			// 最終行に色をつける場合
			if($last_color_flg) {
				// 行の間隔
				$down = $line_max * $this->fontsize * 1.5;
				// 行グリッド描画
				$this->writeRect($x, $y + GRID_SPACE + $down, $width + GRID_SPACE, $this->fontsize + GRID_SPACE, $this->last_color);
			}
		}
	}
	
	// グリッド用のアンダーラインを引く
	/*
		$x			:テーブル開始位置X軸
		$y			:テーブル開始位置Y軸
		$arrCol		:カラムサイズの配列
		$line		:アンダーラインを引く行
		$start_col	:アンダーライン開始カラム(0:開始カラム)
	 */
	function writeUnderLine($x, $y, $arrCol, $line, $start_col = 0) {
		// テーブル幅
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
		}
		
		$start_x = 0;
		for($i = 0; $i < $start_col; $i++) {
			$start_x += $arrCol[$i];
		}
		
		// アンダーラインのY座標を求める
		$down = ($line + 1) * $this->fontsize * 1.5;
		// 行グリッド描画
		$sx = $x + $start_x + GRID_SPACE + $this->table_left;
		$sy = $y + GRID_SPACE + $down - 1;
		$ex = $x + $width + GRID_SPACE;
		$ey = $sy;
				
		$this->writeLine($sx, $sy, $ex, $ey);		
	}
	
	// 真ん中横位置を求める
	function getXCenter($width) {
		$page_width = 0;
		$page_height = 0;
		list($page_width, $page_height) = $this->getSize();
		$x = ($page_width - $width) / 2;
		return $x;
	}
	
	// 自動中央よせ
	function writeTableCenter($table, $y, $arrCol, $arrAlign, $line_max = 256, $start_no = 1, $last_color_flg = false) {
		// テーブルサイズ取得
		$width = 0;
		foreach($arrCol as $val) {
			$width += $val;
		}
		// 中央よせ位置取得
		$x = $this->getXCenter($width) + $this->table_left;
		list($ret_x, $ret_y) = $this->writeTable($table, $x, $y, $arrCol, $arrAlign, $line_max, $start_no, $last_color_flg);
		// X軸の座標を返す
		return array($ret_x, $ret_y);
	}
	
	// データの書き込み(closeすると次回新規ページ)
	// $start_no:1行目(タイトル)を0とする。
	// $line_max:タイトルを含まない行数
	function writeTable($table, $x, $y, $arrCol, $arrAlign, $line_max = 256, $start_no = 1, $last_color_flg = false) {
		$this->openPage();
		
		$table = ereg_replace("\n$", "", $table);
				
		$arrRet = split("\n", $table);
								
		if($line_max > (count($arrRet) - $start_no)) {
			$line_max = count($arrRet) - $start_no;
		}
		
		// タイトル有効
		if($this->grid_enable) {
			// グリッドの描画
			$this->writeGrid($x, $y, $arrCol, $line_max, $last_color_flg);
		}
		
		// UnicodeエンコーディングとしてUTF-8を設定
		PDF_set_parameter($this->pdf, "textformat", "utf8");
		
		// タイトル有効
		if($this->title_enable) {
			if(count($this->arrHeaderColSize) > 0 && count($this->arrHeaderAlign) > 0 ) {
				list($linecol, $aligncol, $width) = $this->getTableOption($this->arrHeaderColSize, $this->arrHeaderAlign);
			} else {
				list($linecol, $aligncol, $width) = $this->getTableOption($arrCol, $arrAlign);
			}	
						
			// タイトル行の書き込み
			$option = "ruler {" . $linecol . "} ";
			$option.= "tabalignment {" . $aligncol . "} ";
			$fontsize =  $this->fontsize;
			$option.= "hortabmethod ruler leading=150% fontname=HeiseiKakuGo-W5 fontsize=$fontsize encoding=UniJIS-UCS2-H";
			
			$this->writeTableData($table, $x, $y, $width, 0, 0, $option);
		}
		
		list($linecol, $aligncol, $width) = $this->getTableOption($arrCol, $arrAlign);
		
		// データ行の書き込み
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
		// カラムサイズ
		$max = count($arrCol);
		$width = 0;
		for($i = 0; $i < $max; $i++) {
			$width += $arrCol[$i];
			$linecol.= $width . " ";
		}
		
		// カラム位置
		$max = count($arrAlign);
		for($i = 0; $i < $max; $i++) {
			$aligncol.= $arrAlign[$i] . " ";
		}
		
		return array($linecol, $aligncol, $width);
	}
	
	// テーブルデータの書き込み
	function writeTableData($table, $x, $y, $table_width, $start_no, $end_no, $option) {
		$arrLine = split("\n", $table);
		for($i = $start_no; $i <= $end_no; $i++) {
			$line.=$arrLine[$i] . "\n";
		}
				
		// テーブル位置を求める
		list($pdf_x, $pdf_y) = $this->posTopDown($x, $y);
						
		// テーブル高さを求める
		$table_height = $this->fontsize * 1.5 * ($end_no - $start_no + 1);
		// テーブル右下のy座標を求める
		$end_y = $pdf_y - $table_height;
		if($end_y < 0) {
			$end_y = 0;
		}
		$enc_table = mb_convert_encoding($line, "utf-8", CHAR_CODE);
				
		$tf = PDF_create_textflow($this->pdf, $enc_table, $option);

		PDF_fit_textflow($this->pdf, $tf, $pdf_x, $pdf_y, $pdf_x + $table_width, $end_y, NULL);
		PDF_delete_textflow($this->pdf, $tf);
		
		// テーブル左下座標を返す
		return array($x, $y + $table_height);		
	}
		
	// 色の設定
	function setColor($rgb) {
		if($rgb != "") {
			list($r, $g, $b) = sfGetPdfRgb($rgb);
			PDF_setcolor($this->pdf, "fillstroke", "rgb", $r, $g, $b, 0);	
		}
	}
	
	// 短形を描画
	function writeRect($x, $y, $width, $height, $rgb = "") {
		$this->openPage();
		list($pdf_x, $pdf_y) = $this->posTopDown($x, $y);
		$this->setColor($rgb);
		PDF_rect($this->pdf, $pdf_x,$pdf_y,$width,-$height);
		PDF_fill($this->pdf);
	}
	
	// 枠付の短形を描画
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
	
	// 直線を描画
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
		
	// ファイルのダウンロード
	function output($filekey = "") {
		if(isset($this->pdf)) {
			// ページを閉じる
			$this->closePage();
			// PDFの終了
			PDF_end_document($this->pdf, NULL);
			// 出力用データの取得 
			$buf = PDF_get_buffer($this->pdf);
			$filename = $filekey . date("ymdHis").".pdf";
						
			header("Content-disposition: attachment; filename=$filename");
			header("Content-type: application/octet-stream; name=$filename");
					
			/*
			 * session_start()を事前に呼び出している場合に出力される以下のヘッダは、
			 * URL直接呼び出し時にエラーを発生させるので空にしておく。
			 *
			 * Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
			 * Progma: no-cache
			 *
			 */
			header("Cache-Control: ");
			header("Pragma: ");
			print $buf;
			
			// PDF解放
			PDF_delete($this->pdf);
		} else {
			print("PDFが生成されていません。");
		}
		exit;		
	}
	
	// ファイルの表示
	function display() {
		if(isset($this->pdf)) {
			// ページを閉じる
			$this->closePage();
			// PDFの終了
			PDF_end_document($this->pdf, NULL);
			
			// 出力用データの取得 
			$buf = PDF_get_buffer($this->pdf);
			$len = strlen($buf);
			header("Content-type: application/pdf");
			header("Content-Length: $len");
			header("Content-Disposition: inline; filename=". date("YmdHis").".pdf");
								
			/*
			 * session_start()を事前に呼び出している場合に出力される以下のヘッダは、
			 * URL直接呼び出し時にエラーを発生させるので空にしておく。
			 *
			 * Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
			 * Progma: no-cache
			 *
			 */
			header("Cache-Control: ");
			header("Pragma: ");
			print $buf;
			
			// PDF解放
			PDF_delete($this->pdf);
		} else {
			print("PDFが生成されていません。");
		}
		exit;
	}
}

?>