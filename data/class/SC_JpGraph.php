<?php

//グラフ作成
include_once ("jpgraph/jpgraph.php");

class SC_JpGraph {

	var $rgb_table;		// 色のRGB
	var $rgb_name;		// 色の名前
	var $max_cnt;		// グラフ要素の最大数
	var $graph_w;		// 幅
	var $graph_h;		// 高さ
	var $margin;		// (array)余白をどれだけとるか 左、右、上、下

	var $objGraph;		// グラフオブジェクト
	
	function SC_JpGraph($graph_w = 800, $graph_h = 450){
		
		$this->max_cnt = 10;						// 最大要素数　これ以上の値を渡されても無視します

		// 色テーブル（追加するときはマニュアルの色見本をみて下さい）
		$this->rgb_table = array(   "goldenrod1"
									,"olivedrab2"
									,"steelblue2"
									,"mistyrose"
									,"azure2"
									,"navy"
									,"plum2"
									,"yellow3"
									,"lightcoral"
									,"mediumpurple"
								);

		$this->graph_w = $graph_w;
		$this->graph_h = $graph_h;
		$this->margin[0] = 40;			// 余白左 初期値
		$this->margin[1] = 30;			// 余白右 初期値
		$this->margin[2] = 30;			// 余白上 初期値
		$this->margin[3] = 40;			// 余白下 初期値
	}

	// 引数$file_pathがあれば、その場所へファイル出力
	function getGraph($file_path = ""){
		
		$this->setMainProperties();
		
		if ( $file_path ){
			// 画像をファイルで出力する
			$this->objGraph->Stroke( $file_path );
		} else {
			//webページ用画像出力
			$this->objGraph->SetFrame('false', array(255,255,255));
			$this->objGraph->Stroke();
		}
	}
	
	function setMainProperties(){
		//出力前に実行する関数
		$this->objGraph->legend->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->title->SetFont(FF_GOTHIC , FS_NORMAL,12);
		$this->objGraph->img->SetMargin($this->margin[0],$this->margin[1],$this->margin[2],$this->margin[3]);
	}
	
	function setTitle($title) {
		$this->objGraph->title->Set($title);
	}
	
	function setWidth($graph_w){
		// 出力画像の幅
		$this->graph_w = $graph_w;
	}
	
	function setHeight($graph_h){					
		// 出力画像の高さ
		$this->graph_h = $graph_h;					
	}

	function setMargin($left, $right, $top, $bottom){
		//　グラフエリアの上下左右の余白設定
		$this->margin[0] = $left;	// 余白左
		$this->margin[1] = $right;	// 余白右
		$this->margin[2] = $top;	// 余白上
		$this->margin[3] = $bottom;	// 余白下
	}
}

class SC_JpGraph_Pie extends SC_JpGraph{

	var $graph_size;			// 円のサイズ
	var $graph_center;
	var $cnt_min;				// 値の最小規準数値（これより小さいと「その他」にいれられる）
	
	function SC_JpGraph_Pie($graph_w = "", $graph_h = "", $graph_size = 100 ){
		include_once ("jpgraph/jpgraph_pie.php");
		include_once ("jpgraph/jpgraph_pie3d.php");
		parent::SC_JpGraph();
		if ( $graph_w ) $this->setWidth($graph_w);
		if ( $graph_h ) $this->setHeight($graph_h);
		$this->objGraph = new PieGraph( $this->graph_w, $this->graph_h, "auto");
		
		$this->graph_size = $graph_size;
		$this->graph_center = 0.35;
		$this->cnt_min = 1.5;		// 値の最小規準数値
	}
	
	// setDataの前に呼び出す
	function setPieTitle($title) {
		$this->title = $title;
	}
	
	function setData($data){
		// 連想配列を渡します key=凡例 val=値（単位は%を整数で表した数）

		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);
		$i = 0;
		$otherVal = 0;
		foreach ( $data as $key=>$val ) {
			if ( $val <= $this->cnt_min ) {
				// 最小規準数値以下は「その他」になる
				$otherVal += $val;
			} else {
				$arrayHanrei[] = $key."     ";
				$cnt[] = (double) $val;
			}
			$i++;
			if ($i<$max_cnt) break;
		}
		// 最小規準数値以下が合った場合は末尾に「その他」をつくる
		if ( $otherVal > 0 ) {
			$arrayHanrei[] = "その他     ";
			$cnt[] = (double) $otherVal;
		}

		//標準設定では反時計回りの円グラフなので、逆順にする
		$p1 = new PiePlot3d( array_reverse($cnt) );		// 円要素を追加
		$p1->SetSliceColors( array_reverse( array_slice($this->rgb_table, 0, $this->max_cnt) ) );
		$p1->SetStartAngle(90); 
				
		// 凡例
		$this->objGraph->legend->SetLayout(LEGEND_VERT);
		$this->objGraph->legend->Pos(0.013,0.05,"right","top");		// 凡例の位置
		
		// 特定の文字列でラベルが文字化けすることがあるので対策
		if(is_array($arrayHanrei)) {
			foreach($arrayHanrei as $key => $val) {
				// 文字コードの再変換
				$arrData[$key] = mb_convert_encoding($val, "EUC-JP", "EUC-JP");
			}
		}
		
		$p1->SetLegends( array_reverse($arrData) );				// 凡例も逆向きに
						
		$this->objGraph->legend->SetReverse();
				
		// グラフのその他属性
		$p1->SetEdge("navy");					// エッジの色。
		$p1->value->HideZero();
		$p1->SetLabelMargin(5);					// ラベル位置。円のエッジからの距離。マイナスは中心側に。
		$p1->SetCenter($this->graph_center);
		$p1->SetSize($this->graph_size);
		if($this->title != "") {
			$p1->title->SetFont(FF_GOTHIC, FS_NORMAL,8);
			$p1->title->SetColor("gray4");
			$p1->title->Set($this->title);
			$p1->title->SetMargin(30);	
		}
		// 値をセット
		$this->objGraph->Add($p1);
	}
}

class SC_JpGraph_Bar extends SC_JpGraph{

	function SC_JpGraph_Bar ($graph_w = "", $graph_h = ""){
		include_once ("jpgraph/jpgraph_bar.php");
		parent::SC_JpGraph();
		if ( $graph_w ) $this->setWidth($graph_w);
		if ( $graph_h ) $this->setHeight($graph_h);	
		$this->objGraph = new Graph( $this->graph_w, $this->graph_h, "auto");
		$this->objGraph->SetScale("textlin");
		$this->objGraph->SetMarginColor('white');
		$this->objGraph->yaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);		// 縦軸ラベルのフォント
		$this->objGraph->xaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);		// 横軸ラベルのフォント
		$this->objGraph->xaxis->SetFont(FF_GOTHIC);
		$this->objGraph->yaxis->SetTitleMargin(35);
		$this->objGraph->legend->Pos(0.013,0.05,"right","top");				// 凡例の位置
	}
	
	// 横軸のタイトル
	function setXTitle($title) {
		$this->objGraph->xaxis->SetTitleMargin(10);
		$this->objGraph->xaxis->title->Set($title);
		$this->objGraph->xaxis->title->SetColor("gray4");
	}
	
	// 縦軸のタイトル
	function setYTitle($title) {
		$this->objGraph->subtitle->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->subtitle->SetColor("gray4");
		$this->objGraph->subtitle->Set($title);
		$this->objGraph->subtitle->SetAlign('left');
	}
	
	function setData($data){
		
		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);	// 最大要素数の設定
		$cnt = 0;
		$otherVal = 0;
		
		foreach ( $data as $key=>$val ) {
			$arrLabel[] = $key;
			
			// グラフ毎に色換えを行うために配列を重ねて描画させる。
			for($i = 0; $i < $this->max_cnt; $i++) {
				if($cnt == $i) {
					$arrVal[$i] = (double)$val;
				} else {
					$arrVal[$i] = 0;
				}
			}
					
			$bar[$cnt] = new BarPlot($arrVal);						
			
			//$bar[$i]->SetLegend( $key."     " );		// 説明表示
					
			$bar[$cnt]->SetFillColor( $this->rgb_table[$cnt] );	// 色設定
			$bar[$cnt]->value->Show();
			$bar[$cnt]->value->SetFont(FF_GOTHIC, FS_NORMAL,8);
			$bar[$cnt]->value->SetFormat('%01.0f');
			
			$bar[$cnt]->SetShadow("black", 1, 1); //影色,影サイズ(h),影サイズ(y)
			$bar[$cnt]->SetAlign("center");
			
			$bar[$cnt]->SetWidth(0.75); 
			
			$cnt++;
			if ($cnt > $this->max_cnt) break;
		}
		
		
		for($i = 0; $i < $this->max_cnt; $i++) {
			$this->objGraph->Add($bar[$i]);			
		}
			
		$this->objGraph->xaxis->SetTickLabels($arrLabel);	
	}	
}

class SC_JpGraph_Line extends SC_JpGraph{
	
	function SC_JpGraph_Line ($graph_w = "", $graph_h = ""){
		include ("jpgraph/jpgraph_line.php");
		parent::SC_JpGraph();
		if ( $graph_w ) $this->setWidth($graph_w);
		if ( $graph_h ) $this->setHeight($graph_h);	
		$this->objGraph = new Graph( $this->graph_w, $this->graph_h, "auto" );
		$this->objGraph->SetMarginColor('white');
		$this->objGraph->SetScale("textlin");
		$this->objGraph->yaxis->SetTitleMargin(35);									// 縦軸 見出しまでの余白
		$this->objGraph->yaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);				// 縦軸 見出しのフォント
		$this->objGraph->xaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);				// 横軸 見出しのフォント
		$this->objGraph->xaxis->SetFont(FF_GOTHIC); 								// 横軸 値のフォント
	}
	
	// 横軸ラベルを何個に一回表示させるか
	function setXLabelInterval($interval) {
		$this->objGraph->xaxis->SetTextLabelInterval($interval);					
	}
	
	// 横軸ラベルの表示角度（日本語不可）
 	function setXLabelAngle($angle) {
		$this->objGraph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
		$this->objGraph->xaxis->SetLabelAngle($angle);
 	}
	
	// 横軸のタイトル
	function setXTitle($title) {
		$this->objGraph->xaxis->SetTitleMargin(25);
		$this->objGraph->xaxis->title->Set($title);
		$this->objGraph->xaxis->title->SetColor("gray4");
	}
	
	// 縦軸のタイトル
	function setYTitle($title) {
		$this->objGraph->subtitle->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->subtitle->SetColor("gray4");
		$this->objGraph->subtitle->Set($title);
		$this->objGraph->subtitle->SetAlign('left');
	}
	
	// 横軸ラベルの表示タイプ（日本語不可）
 	function setXLabelBold($ttf, $type) {
		$this->objGraph->xaxis->SetFont($ttf, $type, 8);
 	}
	
	function setData($data){

		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);	//最大要素数の設定
		
		$i =0;
		foreach ( $data as $key=>$val ) {
			$arrayHanrei[] = $key;
			$cnt[] = (double) $val;
			$i++;
			if ($i < $max_cnt) break;
		}
		$bar = new linePlot( $cnt );
		$bar->value->Show();
		$bar->value->SetFont(FF_GOTHIC, FS_NORMAL,8);			// 値のフォント
		$bar->value->SetFormat('%01.0f');
		$bar->SetStyle('solid'); 
		$bar->SetColor("navy");	 								// 線の色
		$bar->SetCenter();
		$bar->mark->SetType(MARK_FILLEDCIRCLE);					// ポイントの形
		$bar->mark->SetFillColor("red");						// ポイントの色
		$bar->mark->SetWidth(2);								// ポイントのサイズ
		
		$this->objGraph->Add($bar);		
		$this->objGraph->xaxis->SetTickLabels($arrayHanrei);
	}
	
}
?>