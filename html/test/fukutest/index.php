<?php
require_once("../require.php");
$fuku = array(	"��Ƭ"=>300,
				"hoge"=>200,
				"uhyo"=>130,
				"muho"=>100,
				"oge" =>10,
				"ahe" =>30,
				"���Ҥ�" =>20,
				"����" =>15
			);

/*
// �������		
$hoge = new SC_JpGraph_Bar(600, 300);
$hoge->setData($fuku);
$hoge->getGraph();
*/

// �ߥ����
$hoge = new SC_JpGraph_Pie(500, 300);
$hoge->setData($fuku);
$hoge->getGraph();

/*
// �ޤ��������		
$hoge = new SC_JpGraph_Line(600, 300, 2);
$hoge->setData($fuku);
$hoge->getGraph();
*/

?>