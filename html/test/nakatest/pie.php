<?php
require_once("./class/SC_GraphPie.php");
require_once("./class/SC_GraphLine.php");

$objGraphPie = new SC_GraphPie();
$objGraphLine = new SC_GraphLine();

$arrLegend = array(
	'活赋1',
	'活赋2',
	'活赋3',
	'活赋4',
	'活赋5',
	'活赋6',
	'活赋7',
	'活赋8',
	'活赋5',
	'活赋6',
	'活赋7',
	'活赋8'	
);

$arrData = array(
	250,
	23,
	33,
	58,
	120,
	300,
	223,
	100
);

// デ〖タをセットする
$objGraphPie->setData($arrData);
// 宿毋をセットする
$objGraphPie->setLegend($arrLegend);

// 边グラフ闪茶
$objGraphPie->drawGraph();

// グラフの叫蜗
$objGraphPie->outputGraph();
?>