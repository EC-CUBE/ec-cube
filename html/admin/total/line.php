<?php
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
	'1��',
	'2��',
	'3��',
	'4��',
	'5��',
	'6��',
	'7��',
	'1��',
	'2��',
	'3��',
	'4��',
	'5��',
	'6��',
	'7��',	
	'1��',
	'2��',
	'3��',
	'4��',
	'5��',
	'6��',
	'7��',
	'1��',
	'2��',
	'3��',
	'4��',
	'5��',
	'6��',
	'7��',
	'8��',
	'5��',
	'6��',
	'7��',
	'8��',
	'5��',
	'6��',
	'7��',
	'8��'

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
	'�1',
	'�2',
	'�3',
	'�4',
	'�5',
	'�6',
	'�7'
);

// ���������
$objGraphLine->setXLabel($arrXLabel);
//$objGraphLine->setYScale($arrYLabel);
$objGraphLine->setXTitle("����(��)");
$objGraphLine->setYTitle("��夲(��)");
$objGraphLine->setData($arrData1);
//$objGraphLine->setData($arrData1);
//$objGraphLine->setData($arrData3);

// ����򥻥åȤ���
//$objGraphLine->setLegend($arrLegend);
$objGraphLine->drawGraph();

$objGraphLine->outputGraph(false, "/home/web/os-test.lockon.co.jp/html/upload/graph_image/test.png");

?>