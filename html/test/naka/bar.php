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
	'�1',
	'�2',
	'�3',
	'�4',
	'�5',
	'�6',
	'�7',
	'�8',
	'�9',
	'�10',
	'�11',
);

$arrLegend = array(
	'�1',
	'�2',
	'�3',
	'�4'
);

// ���������
$objGraphBar->setXLabel($arrXLabel);
$objGraphBar->setXTitle("����(��)");
$objGraphBar->setYTitle("��夲(��)");
$objGraphBar->setData($arrData1);
$objGraphBar->setData($arrData2);
$objGraphBar->setData($arrData3);
$objGraphBar->setLegend($arrLegend);
$objGraphBar->drawGraph();

$objGraphBar->outputGraph();

?>