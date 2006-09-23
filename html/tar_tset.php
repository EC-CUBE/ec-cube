<?php
require_once("./require.php");

$objtar = new SC_Tar();


//前画面で圧縮対象のファイルが指定されているか
//count関数を用いてチェックし、指定が無い場合は終了する
if (count($HTTP_POST_VARS["compck"]) == 0)
{
	print("圧縮対象のファイルがありません");
 	print("<META HTTP-EQUIV=refresh CONTENT=\"1; URL=http://localhost/mytest12.php" ."\">");
	exit;
}

//fileフォルダに移動する
chdir("file");

//変数$keyを初期化する
$key = "";

//変数$keyに前画面で指定されたファイル名を連結して格納する
//ファイル名とファイル名の間は半角スペースを入れる
for ($i=0;$i<count($HTTP_POST_VARS["compck"]);$i++){
	print $HTTP_POST_VARS["compck"][$i]."<br>";
	$key = $key . " " .$HTTP_POST_VARS["compck"][$i];

}

//ファイル名を作成する
//現在の西暦の年の下2桁頭ゼロ付き + 月の頭ゼロ付き + 日の頭ゼロ付き + 
//"-" + 時間の頭ゼロ付き + 分の頭ゼロ付き + 秒の頭ゼロ付き + "tar.gz"
$fname = "bu".date(ymd)."-".date(his).".tar.gz";

//オブジェクトを作成する
//new Archive_Tar(ファイル名,圧縮フラグ);
//圧縮フラグTRUEはgzip圧縮をおこなう
$tar = new Archive_Tar($fname, TRUE);

//圧縮をおこなう
$tar->create($key);

//圧縮完了のメッセージを表示する
print("<br>を圧縮しました。<br>");
print("ファイル名は " . $fname . "です。<br><br>");

//HTML文を出力　javascriptを使用して直前のページに戻るリンク
print ("<br><a href=javascript:history.back();>戻る</a><br>");


///////////////////////////////////////////////////////////////////////////////////////////////


//PEARのTarを読み込む
require_once("Tar.php");

//前画面で解凍対象のファイルが指定されているか
//count関数を用いてチェックし、指定が無い場合は終了する
if (count($HTTP_POST_VARS["compck"]) == 0)
{
	print("解凍対象のファイルがありません");
 	print("<META HTTP-EQUIV=refresh CONTENT=\"1; URL=http://localhost/mytest13.php" ."\">");
	exit;
}

//fileフォルダに移動する
chdir("file");

//変数$keyに前画面で指定されたファイル名を格納する
$key = $HTTP_POST_VARS["compck"];

//解凍先のフォルダ名を$fnameに格納する
//フォルダ名は「ファイル名.tar.tz」より「.tar.tz」を取り除く
//substr(文字列,開始位置,文字数）は文字列の開始位置から文字数分を取り出す関数
//strlen(文字列）は文字列の長さを返す関数
$fname = substr($key,0,strlen($key) - 7);

//オブジェクトを作成する
//new Archive_Tar(ファイル名,圧縮フラグ);
//圧縮フラグTRUEはgzip解凍をおこなう
$tar = new Archive_Tar($key, TRUE);

//指定されたフォルダ内に解凍する
$tar->extract("./" . $fname);

//解凍完了のメッセージを表示する
print($key . "を<br>" .$fname . "フォルダに解凍いたしました。<br>");

//HTML文を出力　javascriptを使用して直前のページに戻るリンク
print ("<br><a href=javascript:history.back();>戻る</a><br>");

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
<?php

//ダウンロード前に表示するダイアログの指定
header("Content-Type: application/octet-stream");

//ダウンロード前に表示するダイアログの指定　ファイル名を bu+日付.csv
//と指定
header("Content-Disposition: attachment; filename=bu".date(ymd).".csv");

//DBへ接続開始 サーバー名--localhost ユーザー名--root パスワード--root
$dbHandle = mysql_connect("localhost","root","root");

//DBの接続に失敗した場合はエラー表示をおこない処理中断
if ($dbHandle == False) {
	print ("can not connect db\n");	
	exit;
}

//db名  test
$db = "test";

//SQL文 tab1表からnumber列の値の昇順にソートした全行を取り出す
$sql = "select * from tab1 order by number";

//SQL文を実行する
$rs = mysql_db_query($db,$sql);

//mysql_num_fields　関数を使用し列数を取得する
$fields = mysql_num_fields($rs);

//mysql_num_rows　関数を使用し行数を取得する
$rows = mysql_num_rows($rs);

//取り出した行数分繰り返す
for($i=0;$i<$rows;$i++){

//列数分繰り返す
	for($j=0;$j < $fields;$j++){
	
//列の内容出力する
		print(mysql_result($rs,$i,$j));
		
//最終列でない場合は カンマ を出力する
		if ($j < $fields - 1)
			print(",");
	}

//改行コードを出力する
	print("\n");
}

?>








?>