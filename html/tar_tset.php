<?php
require_once("./require.php");

$objtar = new SC_Tar();


//�����̤ǰ����оݤΥե����뤬���ꤵ��Ƥ��뤫
//count�ؿ����Ѥ��ƥ����å��������̵꤬�����Ͻ�λ����
if (count($HTTP_POST_VARS["compck"]) == 0)
{
	print("�����оݤΥե����뤬����ޤ���");
 	print("<META HTTP-EQUIV=refresh CONTENT=\"1; URL=http://localhost/mytest12.php" ."\">");
	exit;
}

//file�ե�����˰�ư����
chdir("file");

//�ѿ�$key����������
$key = "";

//�ѿ�$key�������̤ǻ��ꤵ�줿�ե�����̾��Ϣ�뤷�Ƴ�Ǽ����
//�ե�����̾�ȥե�����̾�δ֤�Ⱦ�ѥ��ڡ����������
for ($i=0;$i<count($HTTP_POST_VARS["compck"]);$i++){
	print $HTTP_POST_VARS["compck"][$i]."<br>";
	$key = $key . " " .$HTTP_POST_VARS["compck"][$i];

}

//�ե�����̾���������
//���ߤ������ǯ�β�2��Ƭ�����դ� + ���Ƭ�����դ� + ����Ƭ�����դ� + 
//"-" + ���֤�Ƭ�����դ� + ʬ��Ƭ�����դ� + �ä�Ƭ�����դ� + "tar.gz"
$fname = "bu".date(ymd)."-".date(his).".tar.gz";

//���֥������Ȥ��������
//new Archive_Tar(�ե�����̾,���̥ե饰);
//���̥ե饰TRUE��gzip���̤򤪤��ʤ�
$tar = new Archive_Tar($fname, TRUE);

//���̤򤪤��ʤ�
$tar->create($key);

//���̴�λ�Υ�å�������ɽ������
print("<br>�򰵽̤��ޤ�����<br>");
print("�ե�����̾�� " . $fname . "�Ǥ���<br><br>");

//HTMLʸ����ϡ�javascript����Ѥ���ľ���Υڡ����������
print ("<br><a href=javascript:history.back();>���</a><br>");


///////////////////////////////////////////////////////////////////////////////////////////////


//PEAR��Tar���ɤ߹���
require_once("Tar.php");

//�����̤ǲ����оݤΥե����뤬���ꤵ��Ƥ��뤫
//count�ؿ����Ѥ��ƥ����å��������̵꤬�����Ͻ�λ����
if (count($HTTP_POST_VARS["compck"]) == 0)
{
	print("�����оݤΥե����뤬����ޤ���");
 	print("<META HTTP-EQUIV=refresh CONTENT=\"1; URL=http://localhost/mytest13.php" ."\">");
	exit;
}

//file�ե�����˰�ư����
chdir("file");

//�ѿ�$key�������̤ǻ��ꤵ�줿�ե�����̾���Ǽ����
$key = $HTTP_POST_VARS["compck"];

//������Υե����̾��$fname�˳�Ǽ����
//�ե����̾�ϡ֥ե�����̾.tar.tz�פ���.tar.tz�פ������
//substr(ʸ����,���ϰ���,ʸ�����ˤ�ʸ����γ��ϰ��֤���ʸ����ʬ����Ф��ؿ�
//strlen(ʸ����ˤ�ʸ�����Ĺ�����֤��ؿ�
$fname = substr($key,0,strlen($key) - 7);

//���֥������Ȥ��������
//new Archive_Tar(�ե�����̾,���̥ե饰);
//���̥ե饰TRUE��gzip����򤪤��ʤ�
$tar = new Archive_Tar($key, TRUE);

//���ꤵ�줿�ե������˲��ह��
$tar->extract("./" . $fname);

//���രλ�Υ�å�������ɽ������
print($key . "��<br>" .$fname . "�ե�����˲��त�����ޤ�����<br>");

//HTMLʸ����ϡ�javascript����Ѥ���ľ���Υڡ����������
print ("<br><a href=javascript:history.back();>���</a><br>");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
<?php

//�������������ɽ��������������λ���
header("Content-Type: application/octet-stream");

//�������������ɽ��������������λ��ꡡ�ե�����̾�� bu+����.csv
//�Ȼ���
header("Content-Disposition: attachment; filename=bu".date(ymd).".csv");

//DB����³���� �����С�̾--localhost �桼����̾--root �ѥ����--root
$dbHandle = mysql_connect("localhost","root","root");

//DB����³�˼��Ԥ������ϥ��顼ɽ���򤪤��ʤ���������
if ($dbHandle == False) {
	print ("can not connect db\n");	
	exit;
}

//db̾  test
$db = "test";

//SQLʸ tab1ɽ����number����ͤξ���˥����Ȥ������Ԥ���Ф�
$sql = "select * from tab1 order by number";

//SQLʸ��¹Ԥ���
$rs = mysql_db_query($db,$sql);

//mysql_num_fields���ؿ�����Ѥ�������������
$fields = mysql_num_fields($rs);

//mysql_num_rows���ؿ�����Ѥ��Կ����������
$rows = mysql_num_rows($rs);

//���Ф����Կ�ʬ�����֤�
for($i=0;$i<$rows;$i++){

//���ʬ�����֤�
	for($j=0;$j < $fields;$j++){
	
//������ƽ��Ϥ���
		print(mysql_result($rs,$i,$j));
		
//�ǽ���Ǥʤ����� ����� ����Ϥ���
		if ($j < $fields - 1)
			print(",");
	}

//���ԥ����ɤ���Ϥ���
	print("\n");
}

?>








?>