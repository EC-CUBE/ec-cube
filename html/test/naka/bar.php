<?php
require_once("./class/SC_GraphBar.php");

$objGraphBar = new SC_GraphBar();

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
);

$arrData2 = array(
	533,
	1158,
	3250,
	423,
	1120,
	100,
	250,
	1300,
	2223,
);

$arrData3 = array(
	1120,
	1300,
	2223,
	100,
	250,
	3250,
	423,
	533,
	1158,
);

$arrXLabel = array(
	'活赋1',
	'活赋2',
	'活赋3',
	'活赋4',
	'活赋5',
	'活赋6',
	'活赋7',
	'活赋8',
	'活赋9',
	'活赋10',
	'活赋11',
);

$arrLegend = array(
	'活赋1',
	'活赋2',
	'活赋3',
	'活赋4'
);

// グラフ闪茶
$objGraphBar->setXLabel($arrXLabel);
$objGraphBar->setXTitle("袋粗(奉)");
$objGraphBar->setYTitle("卿惧げ(边)");
$objGraphBar->setData($arrData1);
$objGraphBar->setData($arrData2);
$objGraphBar->setData($arrData3);
$objGraphBar->setLegend($arrLegend);
$objGraphBar->drawGraph();

$objGraphBar->outputGraph();

?>