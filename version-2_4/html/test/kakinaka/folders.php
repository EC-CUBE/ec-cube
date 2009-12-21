<?php

require_once("../../require.php");

//---- ページ表示用クラス
class LC_Page {
	function LC_Page() {
		$this->tpl_mainpage = 'folders.tpl';
	}
}

$i = 1;
$arrFolder = array($i=>array(
						++$i=>array(
							++$i=>array(++$i,++$i,++$i)
							,++$i=>array(++$i)
							,++$i=>array(++$i,++$i,++$i)
							,++$i=>array(++$i,++$i,++$i)
							)
						,++$i=>array(
							++$i=>array(++$i,++$i,++$i)
							,++$i=>array(++$i)
							,++$i=>array(++$i,++$i,++$i)
							,++$i=>array(++$i,++$i,++$i)
							)
						)
					,++$i=>array(++$i,++$i,++$i,++$i,++$i,++$i,++$i)
					);

/*
$data='{
	"通常対応" : {
		"_open" :1,
		"一般" : {
			"_open" :1,
			"郵便振込" :{
				"_open" :1,
				"郵送",
				"宅急便",
				"発送方法不明"
				}
			}
		}
	}
';
*/
//Tree Data(ここへTrreeメニューのデータをJSONで書きます)
// "_open" :1 はメニュートグルを開きます。0なら閉じます。

$data='
{
	"_open":1,
	"通常対応" : {
		"_index" :1,

		"_open" :1,
		"一般" : {
			"_index" :2,
			"_open" :0,
			"郵便振込" :{
				"_index" :3,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			},
			"代金引換" :{
				"_index" :4,
				"_open" :0,
				"コレクト" : ""
			},
			"カード決済" :{
				"_index" :5,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			},
			"口座自動引落" :{
				"_index" :5,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			}
		},
		"_open" :0,
		"定期" : {
			"_index" :6,
			"_open" :0,
			"郵便振込" :{
				"_index" :7,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			},
			"代金引換" :{
				"_index" :8,
				"_open" :0,
				"コレクト" : ""
			},
			"カード決済" :{
				"_index" :9,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			},
			"口座自動引落" :{
				"_index" :10,
				"_open" :0,
				"郵送" : "",
				"宅急便" : "",
				"発送方法不明" : ""
			}
		}
	},
    "個別対応" : {
		"_index" :11,
		"_open" :0,
		"備考欄に記入有り" : "",
		"お友達を紹介" : "",
		"お友達からの紹介" : "",
		"郵送不可、郵送可能商品の同時注文" : "",
		"同一顧客からの複数注文" : "",
		"一般と定期の同時注文" : "",
		"初回注文5000円以上で代金引換以外を指定" : ""
    }
}
';


/*
$arrFolder = array('ID' => 1, 'name' => '通常対応', 'value'=>array(
						'ID' => 2, 'name' => '一般', 'value'=>array(
							'ID' => 3, 'name' => '郵便振込', 'value'=>array(
								'ID' => 4, 'name' => '郵送', 'value'=>"");
*/

$objView = new SC_UserView("./yui/");
$objPage = new LC_Page();
$objQuery = new SC_Query();

$sql = "(SELECT count('a'), create_date FROM (SELECT date(create_date) as create_date FROM dtb_order) as main GROUP BY create_date ORDER BY create_date)";
$arrData = $objQuery->getall($sql);

/*
$treeData = "{ \"" . $arrData[0]["create_date"] . "\":" . $data . ",";
$treeData .= " \"" . $arrData[1]["create_date"] . "\":" . $data . "}";
*/

sfprintr($_POST);
$date = $_POST["now_date"];

$treeData = "{";
foreach($arrData as $val){
	if($val["create_date"] == $date){
		$treeData .= "\"" . $val["create_date"] . "\":" . $data . ",";
	}else{
		$treeData .= "\"" . $val["create_date"] . "\": \"javascript:form_tree.now_date.value='" . $val["create_date"] . "'; form_tree.submit(); \",";
	}
}
$treeData = ereg_replace(",$", "", $treeData);
$treeData .= "}";



/*
$treeData = "{ \"" . $arrData[0]["create_date"] . "\":" . $data;
for($i=1; $i<count($arrData); $i++){
	$treeData .= ",\"" . $arrData[$i]["create_date"] . "\":" . $data;
}
$treeData .= "}";
*/
$objPage->tpl_days = count($arrData);
$objPage->arrFolder = $arrFolder;
$objPage->data = $treeData;

$objView->assignobj($objPage);

$objView->display($objPage->tpl_mainpage);


// ---------------------------------------------------------------------------------------

function lfConversionArray($arrData){
	$ret = "arrTree = new Array();\n";
	
	foreach($arrData as $val){
		$ret = "";
	}
	
} 



?>