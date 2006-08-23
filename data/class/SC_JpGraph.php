<?php

//����պ���
include_once ("jpgraph/jpgraph.php");

class SC_JpGraph {

	var $rgb_table;		// ����RGB
	var $rgb_name;		// ����̾��
	var $max_cnt;		// ��������Ǥκ����
	var $graph_w;		// ��
	var $graph_h;		// �⤵
	var $margin;		// (array);���ɤ�����Ȥ뤫 ���������塢��

	var $objGraph;		// ����ե��֥�������
	
	function SC_JpGraph($graph_w = 800, $graph_h = 450){
		
		$this->max_cnt = 10;						// �������ǿ�������ʾ���ͤ��Ϥ���Ƥ�̵�뤷�ޤ�

		// ���ơ��֥���ɲä���Ȥ��ϥޥ˥奢��ο����ܤ�ߤƲ�������
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
		$this->margin[0] = 40;			// ;�� �����
		$this->margin[1] = 30;			// ;�� �����
		$this->margin[2] = 30;			// ;��� �����
		$this->margin[3] = 40;			// ;�� �����
	}

	// ����$file_path������С����ξ��إե��������
	function getGraph($file_path = ""){
		
		$this->setMainProperties();
		
		if ( $file_path ){
			// ������ե�����ǽ��Ϥ���
			$this->objGraph->Stroke( $file_path );
		} else {
			//web�ڡ����Ѳ�������
			$this->objGraph->SetFrame('false', array(255,255,255));
			$this->objGraph->Stroke();
		}
	}
	
	function setMainProperties(){
		//�������˼¹Ԥ���ؿ�
		$this->objGraph->legend->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->title->SetFont(FF_GOTHIC , FS_NORMAL,12);
		$this->objGraph->img->SetMargin($this->margin[0],$this->margin[1],$this->margin[2],$this->margin[3]);
	}
	
	function setTitle($title) {
		$this->objGraph->title->Set($title);
	}
	
	function setWidth($graph_w){
		// ���ϲ�������
		$this->graph_w = $graph_w;
	}
	
	function setHeight($graph_h){					
		// ���ϲ����ι⤵
		$this->graph_h = $graph_h;					
	}

	function setMargin($left, $right, $top, $bottom){
		//������ե��ꥢ�ξ岼������;������
		$this->margin[0] = $left;	// ;��
		$this->margin[1] = $right;	// ;��
		$this->margin[2] = $top;	// ;���
		$this->margin[3] = $bottom;	// ;��
	}
}

class SC_JpGraph_Pie extends SC_JpGraph{

	var $graph_size;			// �ߤΥ�����
	var $graph_center;
	var $cnt_min;				// �ͤκǾ�������͡ʤ����꾮�����ȡ֤���¾�פˤ�������
	
	function SC_JpGraph_Pie($graph_w = "", $graph_h = "", $graph_size = 100 ){
		include_once ("jpgraph/jpgraph_pie.php");
		include_once ("jpgraph/jpgraph_pie3d.php");
		parent::SC_JpGraph();
		if ( $graph_w ) $this->setWidth($graph_w);
		if ( $graph_h ) $this->setHeight($graph_h);
		$this->objGraph = new PieGraph( $this->graph_w, $this->graph_h, "auto");
		
		$this->graph_size = $graph_size;
		$this->graph_center = 0.35;
		$this->cnt_min = 1.5;		// �ͤκǾ��������
	}
	
	// setData�����˸ƤӽФ�
	function setPieTitle($title) {
		$this->title = $title;
	}
	
	function setData($data){
		// Ϣ��������Ϥ��ޤ� key=���� val=�͡�ñ�̤�%��������ɽ��������

		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);
		$i = 0;
		$otherVal = 0;
		foreach ( $data as $key=>$val ) {
			if ( $val <= $this->cnt_min ) {
				// �Ǿ�������Ͱʲ��ϡ֤���¾�פˤʤ�
				$otherVal += $val;
			} else {
				$arrayHanrei[] = $key."     ";
				$cnt[] = (double) $val;
			}
			$i++;
			if ($i<$max_cnt) break;
		}
		// �Ǿ�������Ͱʲ�����ä����������ˡ֤���¾�פ�Ĥ���
		if ( $otherVal > 0 ) {
			$arrayHanrei[] = "����¾     ";
			$cnt[] = (double) $otherVal;
		}

		//ɸ������Ǥ�ȿ���ײ��αߥ���դʤΤǡ��ս�ˤ���
		$p1 = new PiePlot3d( array_reverse($cnt) );		// �����Ǥ��ɲ�
		$p1->SetSliceColors( array_reverse( array_slice($this->rgb_table, 0, $this->max_cnt) ) );
		$p1->SetStartAngle(90); 
				
		// ����
		$this->objGraph->legend->SetLayout(LEGEND_VERT);
		$this->objGraph->legend->Pos(0.013,0.05,"right","top");		// ����ΰ���
		
		// �����ʸ����ǥ�٥뤬ʸ���������뤳�Ȥ�����Τ��к�
		if(is_array($arrayHanrei)) {
			foreach($arrayHanrei as $key => $val) {
				// ʸ�������ɤκ��Ѵ�
				$arrData[$key] = mb_convert_encoding($val, "EUC-JP", "EUC-JP");
			}
		}
		
		$p1->SetLegends( array_reverse($arrData) );				// �����ո�����
						
		$this->objGraph->legend->SetReverse();
				
		// ����դΤ���¾°��
		$p1->SetEdge("navy");					// ���å��ο���
		$p1->value->HideZero();
		$p1->SetLabelMargin(5);					// ��٥���֡��ߤΥ��å�����ε�Υ���ޥ��ʥ����濴¦�ˡ�
		$p1->SetCenter($this->graph_center);
		$p1->SetSize($this->graph_size);
		if($this->title != "") {
			$p1->title->SetFont(FF_GOTHIC, FS_NORMAL,8);
			$p1->title->SetColor("gray4");
			$p1->title->Set($this->title);
			$p1->title->SetMargin(30);	
		}
		// �ͤ򥻥å�
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
		$this->objGraph->yaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);		// �ļ���٥�Υե����
		$this->objGraph->xaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);		// ������٥�Υե����
		$this->objGraph->xaxis->SetFont(FF_GOTHIC);
		$this->objGraph->yaxis->SetTitleMargin(35);
		$this->objGraph->legend->Pos(0.013,0.05,"right","top");				// ����ΰ���
	}
	
	// �����Υ����ȥ�
	function setXTitle($title) {
		$this->objGraph->xaxis->SetTitleMargin(10);
		$this->objGraph->xaxis->title->Set($title);
		$this->objGraph->xaxis->title->SetColor("gray4");
	}
	
	// �ļ��Υ����ȥ�
	function setYTitle($title) {
		$this->objGraph->subtitle->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->subtitle->SetColor("gray4");
		$this->objGraph->subtitle->Set($title);
		$this->objGraph->subtitle->SetAlign('left');
	}
	
	function setData($data){
		
		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);	// �������ǿ�������
		$cnt = 0;
		$otherVal = 0;
		
		foreach ( $data as $key=>$val ) {
			$arrLabel[] = $key;
			
			// �������˿�������Ԥ�����������Ťͤ����褵���롣
			for($i = 0; $i < $this->max_cnt; $i++) {
				if($cnt == $i) {
					$arrVal[$i] = (double)$val;
				} else {
					$arrVal[$i] = 0;
				}
			}
					
			$bar[$cnt] = new BarPlot($arrVal);						
			
			//$bar[$i]->SetLegend( $key."     " );		// ����ɽ��
					
			$bar[$cnt]->SetFillColor( $this->rgb_table[$cnt] );	// ������
			$bar[$cnt]->value->Show();
			$bar[$cnt]->value->SetFont(FF_GOTHIC, FS_NORMAL,8);
			$bar[$cnt]->value->SetFormat('%01.0f');
			
			$bar[$cnt]->SetShadow("black", 1, 1); //�ƿ�,�ƥ�����(h),�ƥ�����(y)
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
		$this->objGraph->yaxis->SetTitleMargin(35);									// �ļ� ���Ф��ޤǤ�;��
		$this->objGraph->yaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);				// �ļ� ���Ф��Υե����
		$this->objGraph->xaxis->title->SetFont(FF_GOTHIC, FS_NORMAL,8);				// ���� ���Ф��Υե����
		$this->objGraph->xaxis->SetFont(FF_GOTHIC); 								// ���� �ͤΥե����
	}
	
	// ������٥�򲿸Ĥ˰��ɽ�������뤫
	function setXLabelInterval($interval) {
		$this->objGraph->xaxis->SetTextLabelInterval($interval);					
	}
	
	// ������٥��ɽ�����١����ܸ��Բġ�
 	function setXLabelAngle($angle) {
		$this->objGraph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
		$this->objGraph->xaxis->SetLabelAngle($angle);
 	}
	
	// �����Υ����ȥ�
	function setXTitle($title) {
		$this->objGraph->xaxis->SetTitleMargin(25);
		$this->objGraph->xaxis->title->Set($title);
		$this->objGraph->xaxis->title->SetColor("gray4");
	}
	
	// �ļ��Υ����ȥ�
	function setYTitle($title) {
		$this->objGraph->subtitle->SetFont(FF_GOTHIC, FS_NORMAL,8);
		$this->objGraph->subtitle->SetColor("gray4");
		$this->objGraph->subtitle->Set($title);
		$this->objGraph->subtitle->SetAlign('left');
	}
	
	// ������٥��ɽ�������ס����ܸ��Բġ�
 	function setXLabelBold($ttf, $type) {
		$this->objGraph->xaxis->SetFont($ttf, $type, 8);
 	}
	
	function setData($data){

		if ( count($data) < $this->max_cnt ) $this->max_cnt = count($data);	//�������ǿ�������
		
		$i =0;
		foreach ( $data as $key=>$val ) {
			$arrayHanrei[] = $key;
			$cnt[] = (double) $val;
			$i++;
			if ($i < $max_cnt) break;
		}
		$bar = new linePlot( $cnt );
		$bar->value->Show();
		$bar->value->SetFont(FF_GOTHIC, FS_NORMAL,8);			// �ͤΥե����
		$bar->value->SetFormat('%01.0f');
		$bar->SetStyle('solid'); 
		$bar->SetColor("navy");	 								// ���ο�
		$bar->SetCenter();
		$bar->mark->SetType(MARK_FILLEDCIRCLE);					// �ݥ���Ȥη�
		$bar->mark->SetFillColor("red");						// �ݥ���Ȥο�
		$bar->mark->SetWidth(2);								// �ݥ���ȤΥ�����
		
		$this->objGraph->Add($bar);		
		$this->objGraph->xaxis->SetTickLabels($arrayHanrei);
	}
	
}
?>