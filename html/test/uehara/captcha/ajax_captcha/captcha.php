<?
// ���å���󥹥�����
session_start();

// �����ͤ�������
$input_data = $_POST["input_data"];
$session_data = $_SESSION["security_code"];

// POST�ǡ����Τ߼����դ���
if ($input_data == "") { 
 echo "FORM�������Ϥ��Ʋ�������";
 exit;
}
// ���å������ͤ��������������å�
if (($input_data == $session_data) && ($input_data != "" && $session_data != "")) {
	echo "ǧ����������";
} else {
	echo "ǧ�ڼ��ԡ���";
}
?>