<html lang="ja">
<head>
<meta http-equiv=Content-Type content="text/html; charset=EUC-JP">
</head>

<body>

<table>
<form name="form1" action="test.php" method="POST">
<tr>
	<td>ʸ�������Ϥ��Ƥ�������</td>
	<td><input type="text" name="string"></td>
	<td><input type="submit" value="����"></td>
</tr>
</form>
</table>

<?php
require_once("../../require.php");

    if(isset($_POST['string'])) {
        print("string is " . $_POST['string']);
    }
?>

</body>
</html>