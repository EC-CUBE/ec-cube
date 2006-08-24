#!/usr/bin/perl

#┌─────────────────────────────────
#│ PostMail v1.91 (2002/08/14)
#│ Copyright(C) Kent Web 2002
#│ webmaster@kent-web.com
#│ http://www.kent-web.com/
#└─────────────────────────────────
$ver = 'PostMail v1.91';
#┌─────────────────────────────────
#│ [注意事項]
#│ 1. このスクリプトはフリーソフトです。このスクリプトを使用した
#│    いかなる損害に対して作者は一切の責任を負いません。
#│ 2. 勝手ながら、BlatJを使った送信に関する質問はサポート対象外
#│    とさせていただきます。
#│ 3. 送信フォームのHTMLページの作成に関しては、HTML文法の範疇
#│    となるため、サポートの対象外となります。
#│ 4. それ以外の設置に関する質問はサポート掲示板にお願いいたし
#│    ます。直接メールによる質問はお受けいたしておりません。
#└─────────────────────────────────
#
# [ 送信フォーム (HTML) の記述例 ]
#
# ・タグの記述例 (1)
#   おなまえ <input type=text name="name" size=25>
#   → このフォームに「山田太郎」と入力して送信すると、
#      「name = 山田太郎」という形式で受信します
#
# ・タグの記述例 (2)
#   お好きな色 <input type=radio name="color" value="青">
#   → このラジオボックスにチェックして送信すると、
#      「color = 青」という形式で受信します
#
# ・タグの記述例 (3)
#   E-mail <input type=text name="email" size=25>
#   → name値に「email」という文字を使うとこれはメールアドレス
#      と認識し、アドレスの書式を簡易チェックします
#   → (○) abc@xxx.co.jp
#   → (×) abc.xxx.co.jp → 入力エラーとなります
#
# ・タグの記述例 (4)
#   E-mail <input type=text name="_email" size=25>
#   → name値の先頭に「アンダーバー 」を付けると、その入力値は
#     「入力必須」となります。
#      上記の例では、「メールアドレスは入力必須」となります。
#
# ・name値への「全角文字」の使用は可能です（ただし一部のブラウザでは
#   ごくまれに文字化けする可能性もあり、できれば「半角英数字」を使う方
#   が無難ではあります）
#  (例) <input type=radio name="年齢" value="20歳代">
#  → 上記のラジオボックスにチェックを入れて送信すると、
#     「年齢 = 20歳代」という書式で受け取ることができます。
#
# ・mimew.pl使用時、name値を「name」とするとこれを「送信者名」と認識
#   して送信元のメールアドレスを「送信者 <メールアドレス>」という
#   フォーマットに自動変換します。
#  (フォーム記述例)  <input type=text name="name">
#  (送信元アドレス)  太郎 <taro@email.xx.jp>
#
#  [ 簡易チェック ]
#   http://~~/postmail.cgi?mode=check
#
#  [ 設置例 ]
#
#  public_html / index.html (トップページ等）
#       |
#       +-- postmail / postmail.cgi [755]
#                      jcode.pl     [644]
#                      mimew.pl     [644] ... 任意
#                      postmail.html

#------------#
#  基本設定  #
#------------#

# 文字コード変換ライブラリ
$jcode = '../jcode.pl';

# MIMEエンコードライブラリを使う場合（推奨）
#  → メールヘッダの全角文字をBASE64変換する機能
#  → mimew.plを指定
#$mimer = './mimew.pl';

# メールソフトまでのパス
#  → sendmailの例 ：/usr/lib/sendmail
#  → BlatJの例    ：c:\blatj\blatj.exe
$mailprog = '/usr/sbin/sendmail';

# 送信先メールアドレス
$mailto = 'naka@lockon.co.jp';

# 送信前確認
#   0 : no
#   1 : yes
$preview = 1;

# メールタイトル
$subject = 'お問い合わせ';

# スクリプト名
$script = './postmail.cgi';

# 送信後の戻り先
$back = 'http://www.foulee.co.jp/index.html';

# 送信は method=POST 限定 (0=no 1=yes)
# → セキュリティ対策
$postonly = 1;

# bodyタグ
$body = '<body bgcolor="#F0F0F0" text="#000000" link="#000FF" vlink="#800080">';

# プレビュー画面の枠の色
$tbl_col1 = "#003399";

# プレビュー画面の下地の色
$tbl_col2 = "#FFFFFF";

#------------#
#  設定完了  #
#------------#

# デコード処理
require $jcode;
&decode;

# チェックモード
if ($in{'mode'} eq "check") { &check; }

# POSTチェック
if ($postonly && !$postflag) { &error("不正なアクセスです"); }

# メールプログラムのパスチェック／種類チェック
unless (-e $mailprog) { &error("メールプログラムのパスが不正です"); }
if ($mailprog =~ /blat/i) { $prog_type=2; } else { $prog_type=1; }

# 入力モレエラー
if ($flag) {
	$key2 =~ s/^\_//;
	&error("$key2の入力は必須です");
}

# E-Mail書式チェック
if ($in{'email'} =~ /\,/) { &error("メールアドレスにコンマ「\,」が含まれています"); }
if ($in{'email'} ne "" && $in{'email'} !~ /[\w\.\-]+\@[\w\.\-]+\.[a-zA-Z]{2,5}$/) {
	&error("メールアドレスの書式が不正です");
}

# プレビュー
if ($preview && $in{'mode'} ne "send") {

	&header;
	print "<br><div align=center>\n",
	"<h3>- 以下の内容でよろしければ送信ボタンを押して下さい -</h3>\n",
	"<form action=\"$script\" method=\"POST\">\n",
	"<input type=hidden name=mode value=send>\n",
	"<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 width='80%' BORDER=0><TR>",
	"<TD BGCOLOR=\"$tbl_col1\">",
	"<table border=0 cellspacing=1 cellpadding=5 width='100%'>",
	"<tr bgcolor=\"$tbl_col1\"><th bgcolor=\"$tbl_col2\" width='30%'>項目</th>",
	"<th bgcolor=\"$tbl_col2\" width='70%'>内容</th></tr>\n";

	foreach (@key) {
		next if ($bef eq $_);
#		$in{$_} =~ s/</&lt;/g;
#		$in{$_} =~ s/>/&gt;/g;
#		$in{$_} =~ s/"/&quot;/g;
#		$in{$_} =~ s/&/&amp;/g;
		$in{$_} =~ s/\0/ /g;
		$in{$_} =~ s/\r\n/<br>/g;
		$in{$_} =~ s/\r/<br>/g;
		$in{$_} =~ s/\n/<br>/g;
		if ($in{$_} =~ /<br>$/) {
			while ($in{$_} =~ /<br>$/) { $in{$_} =~ s/<br>$//g; }
		}
		print "<tr><td bgcolor=\"$tbl_col2\"><b>$_</b></td>";
		print "<td bgcolor=\"$tbl_col2\">$in{$_}\n";
		print "<input type=hidden name=\"$_\" value=\"$in{$_}\"></td>\n";
		$bef = $_;
	}

	print "</tr></table></TD></TR></TABLE>\n<P><table><tr><td>\n";
	print "<input type=submit value='この内容で送信する'></form></td>\n";
	print "<td><form><input type=button value='前画面に戻る' onClick='history.back()'></form></td></tr></table></div>\n";
	print "<!-- $ver -->\n</body>\n</html>\n";
	exit;
}

# 時間・ホストを取得
&get_time;
&get_host;

# blatj送信
if ($prog_type == 2) {

	# 一時ファイルを書き出し
	$tempfile = "./$$\.tmp";
	open(TMP,">$tempfile") || &error("Write Error : $tempfile");
	print TMP "「$subject」よりメールの発信がありました.\n\n";
	print TMP "送信日時：$date\n";
	print TMP "ブラウザ：$ENV{'HTTP_USER_AGENT'}\n";
	print TMP "ホスト名：$host\n\n";
	foreach (@key) {
		next if ($_ eq "mode");
		next if ($bef eq $_);
#		$in{$_} =~ s/&lt;/</g;
#		$in{$_} =~ s/&gt;/>/g;
#		$in{$_} =~ s/&quot;/"/g;
#		$in{$_} =~ s/&amp;/&/g;
		$in{$_} =~ s/\0/ /g;
		$in{$_} =~ s/&lt;br&gt;/\n/g;
		$in{$_} =~ s/\.\n/\. \n/g;
		if ($in{$_} =~ /\n/) { print TMP "$_ = \n\n$in{$_}\n"; }
		else { print TMP "$_ = $in{$_}\n"; }
		$bef = $_;
	}
	close(TMP);

	# 送信処理
	open(MAIL,"| $mailprog $tempfile -t $mailto -s \"$subject\" -q")
					|| &error("メール送信に失敗しました");
	close(MAIL);

	# 一時ファイル削除
	unlink($tempfile);
}
# sendmail送信
else {
	$mail_body .= "「$subject」よりメールの発信がありました.\n\n";
	$mail_body .= "送信日時：$date\n";
	$mail_body .= "ブラウザ：$ENV{'HTTP_USER_AGENT'}\n";
	$mail_body .= "ホスト名：$host\n\n";
	foreach (@key) {
		next if ($_ eq "mode");
		next if ($bef eq $_);
#		$in{$_} =~ s/&lt;/</g;
#		$in{$_} =~ s/&gt;/>/g;
#		$in{$_} =~ s/&quot;/\"/g;
#		$in{$_} =~ s/&amp;/&/g;
		$in{$_} =~ s/\0/ /g;
		$in{$_} =~ s/&lt;br&gt;/\n/g;
		$in{$_} =~ s/\.\n/\. \n/g;
		if ($in{$_} =~ /\n/) {
			$mail_body .= "$_ = \n\n$in{$_}\n";
		} else {
			$mail_body .= "$_ = $in{$_}\n";
		}
		$bef = $_;
	}

	# JISコード変換
    	&jcode'convert(*mail_body,'jis');

	# メールアドレスがない場合は送信先に置き換え
	if ($in{'email'} eq "") { $email = $mailto; }
	else { $email = $in{'email'}; }

	# MIMEエンコード
	if (-e $mimer) {
		require $mimer;
		$subject2 = &mimeencode($subject);
		if ($in{'name'}) {
			$email = "\"$in{'name'}\" <$email>";
			$email = &mimeencode($email);
		}
	} else {
		$subject2 = $subject;
		&jcode'convert(*subject2,'jis');
	}
	open(MAIL,"| $mailprog -t") || &error("メール送信に失敗しました");
	print MAIL "To: $mailto\n";
	print MAIL "From: $email\n";
	print MAIL "Subject: $subject2\n";
	print MAIL "MIME-Version: 1.0\n";
	print MAIL "Content-type: text/plain; charset=ISO-2022-JP\n";
	print MAIL "Content-Transfer-Encoding: 7bit\n";
	print MAIL "X-Mailer: $ver\n\n";
	print MAIL "$mail_body\n";
	close(MAIL);
}

# 完了メッセージ
&header2;
print <<"EOM";
<br>
<div align=center>
<hr width=400>
<P><big><b>ありがとうございました.</b>
<P><b>送信は正常に完了しました.</b></big>
<P><hr width=400>
<form><input type=button value="トップに戻る" onClick=window.open("$back","_top")></form>
</div><br><br><p align=right>
<!-- 著作権表\示：削除改変不可 ($ver) -->
<span style="font-size:11px;font-family:verdana">
Copyright (C)
<a href="http://www.kent-web.com/" target="_top" style="text-decoration:none">Kent Web</a>
2002<br><b>$ver</b></span>
<!-- Google Code for Purchase Conversion Page -->
<script language="JavaScript" type="text/javascript">
<!--
var google_conversion_id = 1067004636;
var google_conversion_language = "ja";
var google_conversion_format = "1";
var google_conversion_color = "FFFFFF";
if (1) {
  var google_conversion_value = 1;
}
var google_conversion_label = "Purchase";
//-->
</script>
<script language="JavaScript" src="http://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<img height=1 width=1 border=0 src="http://www.googleadservices.com/pagead/conversion/1067004636/?value=1&label=Purchase&script=0">
</noscript>
</body>
</html>
EOM
exit;

#----------------#
#  デコード処理  #
#----------------#
sub decode {
	local($key, $val, $buf, @in);
	if ($ENV{'REQUEST_METHOD'} eq "POST") {
		$postflag=1;
		read(STDIN, $buf, $ENV{'CONTENT_LENGTH'});
	} else {
		$postflag=0;
		$buf = $ENV{'QUERY_STRING'};
	}
	@in = split(/&/, $buf);
	$flag=0; @key=();
	foreach (@in) {
		($key, $val) = split(/=/);
		$key =~ tr/+/ /;
		$key =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
		$val =~ tr/+/ /;
		$val =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;

		&jcode'convert(*key, 'sjis');
		&jcode'convert(*val, 'sjis');

		# タグ排除
		$key =~ s/&/&amp;/g;
		$key =~ s/"/&quot;/g;
		$key =~ s/</&lt;/g;
		$key =~ s/>/&gt;/g;
		$val =~ s/&/&amp;/g;
		$val =~ s/"/&quot;/g;
		$val =~ s/</&lt;/g;
		$val =~ s/>/&gt;/g;

		# 必須項目
		if ($key =~ /^\_/) {
			if ($val eq "") { $flag=1; $key2=$key; last; }
		}
		$key =~ s/^\_//;

		$in{$key} .= "\0" if (defined($in{$key}));
		$in{$key} .= $val;

		push(@key,$key);
	}
}

#--------------#
#  HTMLヘッダ  #
#--------------#
sub header {
	$headflag=1;
	print "Content-type: text/html\n\n";
	print <<"EOM";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
<title>$subject</title></head>
$body
EOM
}

#--------------#
# HTMLヘッダ2  #
#--------------#
sub header2 {
	$headflag=1;
	print "Content-type: text/html\n\n";
	print <<"EOM";
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="ja">
<head>
<META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">
<title>$subject</title>
<SCRIPT LANGUAGE="JavaScript">
<!-- Overture Services Inc. 07/15/2003
var cc_tagVersion = "1.0";
var cc_accountID = "1145090945";
var cc_marketID =  "4";
var cc_protocol="http";
var cc_subdomain = "convctr";
if(location.protocol == "https:")
{
    cc_protocol="https";
     cc_subdomain="convctrs";
}
var cc_queryStr = "?" + "ver=" + cc_tagVersion + "&aID=" + cc_accountID + "&mkt=" + cc_marketID +"&ref=" + escape(document.referrer);
var cc_imageUrl = cc_protocol + "://" + cc_subdomain + ".overture.com/images/cc/cc.gif" + cc_queryStr;
var cc_imageObject = new Image();
cc_imageObject.src = cc_imageUrl;
// -->
</SCRIPT>
</head>
$body
EOM
}

#--------------#
#  エラー処理  #
#--------------#
sub error {
	unlink($tempfile) if (-e $tempfile);

	&header if (!$headflag);
	print <<"EOM";
<div align="center"><h3>ERROR !</h3>
<font color="red">$_[0]</font>
<form><input type=button value="前画面にもどる" onClick="history.back()">
</form></div>
</body>
</html>
EOM
	exit;
}

sub error {
	unlink($tempfile) if (-e $tempfile);

	&header2 if (!$headflag);
	print <<"EOM";
<div align="center"><h3>ERROR !</h3>
<font color="red">$_[0]</font>
<form><input type=button value="前画面にもどる" onClick="history.back()">
</form></div>
</body>
</html>
EOM
	exit;
}

#--------------#
#  時間を取得  #
#--------------#
sub get_time {
	$ENV{'TZ'} = "JST-9";
	local($min,$hour,$mday,$mon,$year,$wday) = (localtime(time))[1..6];
	local(@week) = ('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

	# 日時のフォーマット
	$date = sprintf("%04d/%02d/%02d(%s) %02d:%02d",
			$year+1900,$mon+1,$mday,$week[$wday],$hour,$min);
}

#----------------#
#  ホスト名取得  #
#----------------#
sub get_host {
	$host = $ENV{'REMOTE_HOST'};
	$addr = $ENV{'REMOTE_ADDR'};

	if ($host eq "" || $host eq $addr) {
		$host = gethostbyaddr(pack("C4", split(/\./, $addr)), 2) || $addr;
	}
}

#----------------#
#  ホスト名取得  #
#----------------#
sub check {
	&header;
	print "<h3>Check Mode</h3>\n<UL>\n";

	# 設定チェック
	if ($postonly && POST ne "POST") {
		print "<LI>$postonly=1;の場合はPOST=\"POST\"とすること";
	}

	# メールソフトチェック
	print "<LI>メールソ\フトパス：";
	if (-e $mailprog) { print "OK\n"; }
	else { print "NG → $mailprog\n"; }

	# jcode.pl バージョンチェック
	print "<LI>jcode.plバージョンチェック：";
	$flag=0;
	open(IN, $jcode);
	while (<IN>) {
		if ($_ =~ /jcode\.pl\,v (\d)\.(\d+)/) {
			$v1=$1; $v2=$2; $flag=1; last;
		}
	}
	close(IN);
	if ($v1 < 2 || $v2 < 13) {
		print "バージョンが低いようです。→ $v1.$v2\n";
	} else {
		print "バージョンOK (v $v1.$v2)\n";
	}

	print "</UL>\n<body></html>\n";
	exit;
}

__END__

