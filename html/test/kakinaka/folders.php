<?php

require_once("../../require.php");

//---- �ڡ���ɽ���ѥ��饹
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
	"�̾��б�" : {
		"_open" :1,
		"����" : {
			"_open" :1,
			"͹�ؿ���" :{
				"_open" :1,
				"͹��",
				"�����",
				"ȯ����ˡ����"
				}
			}
		}
	}
';
*/
//Tree Data(������Trree��˥塼�Υǡ�����JSON�ǽ񤭤ޤ�)
// "_open" :1 �ϥ�˥塼�ȥ���򳫤��ޤ���0�ʤ��Ĥ��ޤ���

$data='
{
	"_open":1,
	"�̾��б�" : {
		"_index" :1,

		"_open" :1,
		"����" : {
			"_index" :2,
			"_open" :0,
			"͹�ؿ���" :{
				"_index" :3,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			},
			"������" :{
				"_index" :4,
				"_open" :0,
				"���쥯��" : ""
			},
			"�����ɷ��" :{
				"_index" :5,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			},
			"���¼�ư����" :{
				"_index" :5,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			}
		},
		"_open" :0,
		"���" : {
			"_index" :6,
			"_open" :0,
			"͹�ؿ���" :{
				"_index" :7,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			},
			"������" :{
				"_index" :8,
				"_open" :0,
				"���쥯��" : ""
			},
			"�����ɷ��" :{
				"_index" :9,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			},
			"���¼�ư����" :{
				"_index" :10,
				"_open" :0,
				"͹��" : "",
				"�����" : "",
				"ȯ����ˡ����" : ""
			}
		}
	},
    "�����б�" : {
		"_index" :11,
		"_open" :0,
		"������˵���ͭ��" : "",
		"��ͧã��Ҳ�" : "",
		"��ͧã����ξҲ�" : "",
		"͹���Բġ�͹����ǽ���ʤ�Ʊ����ʸ" : "",
		"Ʊ��ܵҤ����ʣ����ʸ" : "",
		"���̤������Ʊ����ʸ" : "",
		"�����ʸ5000�߰ʾ���������ʳ������" : ""
    }
}
';


/*
$arrFolder = array('ID' => 1, 'name' => '�̾��б�', 'value'=>array(
						'ID' => 2, 'name' => '����', 'value'=>array(
							'ID' => 3, 'name' => '͹�ؿ���', 'value'=>array(
								'ID' => 4, 'name' => '͹��', 'value'=>"");
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