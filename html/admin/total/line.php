<?php
/*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
require_once("./class/SC_GraphPie.php");
require_once("./class/SC_GraphLine.php");

class LC_Page {
	function LC_Page($arrData) {
		$this->arrData = $arrData;
	}
}


$objGraphPie = new SC_GraphPie();
$objGraphLine = new SC_GraphLine();

$arrData1 = array(
	3250,
	423,
	533,
	1158,
	1120,
	1300,
	2223,
	100,
	250,
	23,
	33,
	58,
	120,
	3300,
	4223,
	5120,
	2300,
	3223,
	100,
	4250,
	100
);

$arrData2 = array(
	3300,
	4223,
	5120,
	2300,
	3223,
	100,
	4250,
	23,
	3250,
	423,
	533,
	1158,
	1120,
	1300,
	2223,
	100,
	250,
	23,
	33,
	58,
	120,
	100
);

$arrData3 = array(
	6223,
	6223,
	6223,
	6300,
	3223,
	2223,
	6300,
	6223,
	5120,
	2300,
	3223,
	6223,
	6223,
	3300,
	4223,
	5120,
	6300,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
	3223,
	2223,
);

$arrXLabel = array(
	'1月',
	'2月',
	'3月',
	'4月',
	'5月',
	'6月',
	'7月',
	'1月',
	'2月',
	'3月',
	'4月',
	'5月',
	'6月',
	'7月',	
	'1月',
	'2月',
	'3月',
	'4月',
	'5月',
	'6月',
	'7月',
	'1月',
	'2月',
	'3月',
	'4月',
	'5月',
	'6月',
	'7月',
	'8月',
	'5月',
	'6月',
	'7月',
	'8月',
	'5月',
	'6月',
	'7月',
	'8月'

);
$arrYLabel = array(
	1000,
	2000,
	3000,
	4000,
	5000,
	6000,
	7000,
	8000,
	9000,
	10000
);

$arrLegend = array(
	'試験1',
	'試験2',
	'試験3',
	'試験4',
	'試験5',
	'試験6',
	'試験7'
);

// グラフ描画
$objGraphLine->setXLabel($arrXLabel);
//$objGraphLine->setYScale($arrYLabel);
$objGraphLine->setXTitle("期間(月)");
$objGraphLine->setYTitle("売上げ(円)");
$objGraphLine->setData($arrData1);
//$objGraphLine->setData($arrData1);
//$objGraphLine->setData($arrData3);

// 凡例をセットする
//$objGraphLine->setLegend($arrLegend);
$objGraphLine->drawGraph();

$objGraphLine->outputGraph(false, "/home/web/os-test.lockon.co.jp/html/upload/graph_image/test.png");

?>