<?php
require_once("./class/SC_GraphPie.php");
require_once("./class/SC_GraphLine.php");

$objGraphPie = new SC_GraphPie();
$objGraphLine = new SC_GraphLine();

$arrLegend = array(
	'�1',
	'�2',
	'�3',
	'�4',
	'�5',
	'�6',
	'�7',
	'�8',
	'�5',
	'�6',
	'�7',
	'�8'	
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

// �ǡ����򥻥åȤ���
$objGraphPie->setData($arrData);
// ����򥻥åȤ���
$objGraphPie->setLegend($arrLegend);

// �ߥ��������
$objGraphPie->drawGraph();

// ����դν���
$objGraphPie->outputGraph();
?>