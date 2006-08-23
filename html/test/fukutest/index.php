<?php
require_once("../require.php");
$fuku = array(	"先頭"=>300,
				"hoge"=>200,
				"uhyo"=>130,
				"muho"=>100,
				"oge" =>10,
				"ahe" =>30,
				"うひょ" =>20,
				"も一個" =>15
			);

/*
// 棒グラフ		
$hoge = new SC_JpGraph_Bar(600, 300);
$hoge->setData($fuku);
$hoge->getGraph();
*/

// 円グラフ
$hoge = new SC_JpGraph_Pie(500, 300);
$hoge->setData($fuku);
$hoge->getGraph();

/*
// 折れ線グラフ		
$hoge = new SC_JpGraph_Line(600, 300, 2);
$hoge->setData($fuku);
$hoge->getGraph();
*/

?>