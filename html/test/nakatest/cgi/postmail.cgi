#!/usr/bin/perl

#��������������������������������������������������������������������
#�� PostMail v1.91 (2002/08/14)
#�� Copyright(C) Kent Web 2002
#�� webmaster@kent-web.com
#�� http://www.kent-web.com/
#��������������������������������������������������������������������
$ver = 'PostMail v1.91';
#��������������������������������������������������������������������
#�� [��ջ���]
#�� 1. ���Υ�����ץȤϥե꡼���եȤǤ������Υ�����ץȤ���Ѥ���
#��    �����ʤ�»�����Ф��ƺ�Ԥϰ��ڤ���Ǥ���餤�ޤ���
#�� 2. ����ʤ��顢BlatJ��Ȥä������˴ؤ������ϥ��ݡ����оݳ�
#��    �Ȥ����Ƥ��������ޤ���
#�� 3. �����ե������HTML�ڡ����κ����˴ؤ��Ƥϡ�HTMLʸˡ������
#��    �Ȥʤ뤿�ᡢ���ݡ��Ȥ��оݳ��Ȥʤ�ޤ���
#�� 4. ����ʳ������֤˴ؤ������ϥ��ݡ��ȷǼ��Ĥˤ��ꤤ������
#��    �ޤ���ľ�ܥ᡼��ˤ�����Ϥ������������Ƥ���ޤ���
#��������������������������������������������������������������������
#
# [ �����ե����� (HTML) �ε����� ]
#
# �������ε����� (1)
#   ���ʤޤ� <input type=text name="name" size=25>
#   �� ���Υե�����ˡֻ�����Ϻ�פ����Ϥ�����������ȡ�
#      ��name = ������Ϻ�פȤ��������Ǽ������ޤ�
#
# �������ε����� (2)
#   �������ʿ� <input type=radio name="color" value="��">
#   �� ���Υ饸���ܥå����˥����å�������������ȡ�
#      ��color = �ġפȤ��������Ǽ������ޤ�
#
# �������ε����� (3)
#   E-mail <input type=text name="email" size=25>
#   �� name�ͤˡ�email�פȤ���ʸ����Ȥ��Ȥ���ϥ᡼�륢�ɥ쥹
#      ��ǧ���������ɥ쥹�ν񼰤�ʰץ����å����ޤ�
#   �� (��) abc@xxx.co.jp
#   �� (��) abc.xxx.co.jp �� ���ϥ��顼�Ȥʤ�ޤ�
#
# �������ε����� (4)
#   E-mail <input type=text name="_email" size=25>
#   �� name�ͤ���Ƭ�ˡ֥�������С� �פ��դ���ȡ����������ͤ�
#     ������ɬ�ܡפȤʤ�ޤ���
#      �嵭����Ǥϡ��֥᡼�륢�ɥ쥹������ɬ�ܡפȤʤ�ޤ���
#
# ��name�ͤؤΡ�����ʸ���פλ��Ѥϲ�ǽ�Ǥ��ʤ����������Υ֥饦���Ǥ�
#   �����ޤ��ʸ�����������ǽ���⤢�ꡢ�Ǥ���С�Ⱦ�ѱѿ����פ�Ȥ���
#   ��̵��ǤϤ���ޤ���
#  (��) <input type=radio name="ǯ��" value="20����">
#  �� �嵭�Υ饸���ܥå����˥����å����������������ȡ�
#     ��ǯ�� = 20����פȤ����񼰤Ǽ�����뤳�Ȥ��Ǥ��ޤ���
#
# ��mimew.pl���ѻ���name�ͤ��name�פȤ���Ȥ�����������̾�פ�ǧ��
#   �����������Υ᡼�륢�ɥ쥹��������� <�᡼�륢�ɥ쥹>�פȤ���
#   �ե����ޥåȤ˼�ư�Ѵ����ޤ���
#  (�ե����൭����)  <input type=text name="name">
#  (���������ɥ쥹)  ��Ϻ <taro@email.xx.jp>
#
#  [ �ʰץ����å� ]
#   http://������/postmail.cgi?mode=check
#
#  [ ������ ]
#
#  public_html / index.html (�ȥåץڡ�������
#       |
#       +-- postmail / postmail.cgi [755]
#                      jcode.pl     [644]
#                      mimew.pl     [644] ... Ǥ��
#                      postmail.html

#------------#
#  ��������  #
#------------#

# ʸ���������Ѵ��饤�֥��
$jcode = '../jcode.pl';

# MIME���󥳡��ɥ饤�֥���Ȥ����ʿ侩��
#  �� �᡼��إå�������ʸ����BASE64�Ѵ����뵡ǽ
#  �� mimew.pl�����
#$mimer = './mimew.pl';

# �᡼�륽�եȤޤǤΥѥ�
#  �� sendmail���� ��/usr/lib/sendmail
#  �� BlatJ����    ��c:\blatj\blatj.exe
$mailprog = '/usr/sbin/sendmail';

# ������᡼�륢�ɥ쥹
$mailto = 'naka@lockon.co.jp';

# ��������ǧ
#   0 : no
#   1 : yes
$preview = 1;

# �᡼�륿���ȥ�
$subject = '���䤤��碌';

# ������ץ�̾
$script = './postmail.cgi';

# ������������
$back = 'http://www.foulee.co.jp/index.html';

# ������ method=POST ���� (0=no 1=yes)
# �� �������ƥ��к�
$postonly = 1;

# body����
$body = '<body bgcolor="#F0F0F0" text="#000000" link="#000FF" vlink="#800080">';

# �ץ�ӥ塼���̤��Ȥο�
$tbl_col1 = "#003399";

# �ץ�ӥ塼���̤β��Ϥο�
$tbl_col2 = "#FFFFFF";

#------------#
#  ���괰λ  #
#------------#

# �ǥ����ɽ���
require $jcode;
&decode;

# �����å��⡼��
if ($in{'mode'} eq "check") { &check; }

# POST�����å�
if ($postonly && !$postflag) { &error("�����ʥ��������Ǥ�"); }

# �᡼��ץ����Υѥ������å�����������å�
unless (-e $mailprog) { &error("�᡼��ץ����Υѥ��������Ǥ�"); }
if ($mailprog =~ /blat/i) { $prog_type=2; } else { $prog_type=1; }

# ���ϥ�쥨�顼
if ($flag) {
	$key2 =~ s/^\_//;
	&error("$key2�����Ϥ�ɬ�ܤǤ�");
}

# E-Mail�񼰥����å�
if ($in{'email'} =~ /\,/) { &error("�᡼�륢�ɥ쥹�˥���ޡ�\,�פ��ޤޤ�Ƥ��ޤ�"); }
if ($in{'email'} ne "" && $in{'email'} !~ /[\w\.\-]+\@[\w\.\-]+\.[a-zA-Z]{2,5}$/) {
	&error("�᡼�륢�ɥ쥹�ν񼰤������Ǥ�");
}

# �ץ�ӥ塼
if ($preview && $in{'mode'} ne "send") {

	&header;
	print "<br><div align=center>\n",
	"<h3>- �ʲ������ƤǤ������������ܥ���򲡤��Ʋ����� -</h3>\n",
	"<form action=\"$script\" method=\"POST\">\n",
	"<input type=hidden name=mode value=send>\n",
	"<TABLE BORDER=0 CELLSPACING=0 CELLPADDING=0 width='80%' BORDER=0><TR>",
	"<TD BGCOLOR=\"$tbl_col1\">",
	"<table border=0 cellspacing=1 cellpadding=5 width='100%'>",
	"<tr bgcolor=\"$tbl_col1\"><th bgcolor=\"$tbl_col2\" width='30%'>����</th>",
	"<th bgcolor=\"$tbl_col2\" width='70%'>����</th></tr>\n";

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
	print "<input type=submit value='�������Ƥ���������'></form></td>\n";
	print "<td><form><input type=button value='�����̤����' onClick='history.back()'></form></td></tr></table></div>\n";
	print "<!-- $ver -->\n</body>\n</html>\n";
	exit;
}

# ���֡��ۥ��Ȥ����
&get_time;
&get_host;

# blatj����
if ($prog_type == 2) {

	# ����ե������񤭽Ф�
	$tempfile = "./$$\.tmp";
	open(TMP,">$tempfile") || &error("Write Error : $tempfile");
	print TMP "��$subject�פ��᡼���ȯ��������ޤ���.\n\n";
	print TMP "����������$date\n";
	print TMP "�֥饦����$ENV{'HTTP_USER_AGENT'}\n";
	print TMP "�ۥ���̾��$host\n\n";
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

	# ��������
	open(MAIL,"| $mailprog $tempfile -t $mailto -s \"$subject\" -q")
					|| &error("�᡼�������˼��Ԥ��ޤ���");
	close(MAIL);

	# ����ե�������
	unlink($tempfile);
}
# sendmail����
else {
	$mail_body .= "��$subject�פ��᡼���ȯ��������ޤ���.\n\n";
	$mail_body .= "����������$date\n";
	$mail_body .= "�֥饦����$ENV{'HTTP_USER_AGENT'}\n";
	$mail_body .= "�ۥ���̾��$host\n\n";
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

	# JIS�������Ѵ�
    	&jcode'convert(*mail_body,'jis');

	# �᡼�륢�ɥ쥹���ʤ�������������֤�����
	if ($in{'email'} eq "") { $email = $mailto; }
	else { $email = $in{'email'}; }

	# MIME���󥳡���
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
	open(MAIL,"| $mailprog -t") || &error("�᡼�������˼��Ԥ��ޤ���");
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

# ��λ��å�����
&header2;
print <<"EOM";
<br>
<div align=center>
<hr width=400>
<P><big><b>���꤬�Ȥ��������ޤ���.</b>
<P><b>����������˴�λ���ޤ���.</b></big>
<P><hr width=400>
<form><input type=button value="�ȥåפ����" onClick=window.open("$back","_top")></form>
</div><br><br><p align=right>
<!-- ���ɽ\������������Բ� ($ver) -->
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
#  �ǥ����ɽ���  #
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

		# �����ӽ�
		$key =~ s/&/&amp;/g;
		$key =~ s/"/&quot;/g;
		$key =~ s/</&lt;/g;
		$key =~ s/>/&gt;/g;
		$val =~ s/&/&amp;/g;
		$val =~ s/"/&quot;/g;
		$val =~ s/</&lt;/g;
		$val =~ s/>/&gt;/g;

		# ɬ�ܹ���
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
#  HTML�إå�  #
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
# HTML�إå�2  #
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
#  ���顼����  #
#--------------#
sub error {
	unlink($tempfile) if (-e $tempfile);

	&header if (!$headflag);
	print <<"EOM";
<div align="center"><h3>ERROR !</h3>
<font color="red">$_[0]</font>
<form><input type=button value="�����̤ˤ�ɤ�" onClick="history.back()">
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
<form><input type=button value="�����̤ˤ�ɤ�" onClick="history.back()">
</form></div>
</body>
</html>
EOM
	exit;
}

#--------------#
#  ���֤����  #
#--------------#
sub get_time {
	$ENV{'TZ'} = "JST-9";
	local($min,$hour,$mday,$mon,$year,$wday) = (localtime(time))[1..6];
	local(@week) = ('Sun','Mon','Tue','Wed','Thu','Fri','Sat');

	# �����Υե����ޥå�
	$date = sprintf("%04d/%02d/%02d(%s) %02d:%02d",
			$year+1900,$mon+1,$mday,$week[$wday],$hour,$min);
}

#----------------#
#  �ۥ���̾����  #
#----------------#
sub get_host {
	$host = $ENV{'REMOTE_HOST'};
	$addr = $ENV{'REMOTE_ADDR'};

	if ($host eq "" || $host eq $addr) {
		$host = gethostbyaddr(pack("C4", split(/\./, $addr)), 2) || $addr;
	}
}

#----------------#
#  �ۥ���̾����  #
#----------------#
sub check {
	&header;
	print "<h3>Check Mode</h3>\n<UL>\n";

	# ��������å�
	if ($postonly && POST ne "POST") {
		print "<LI>$postonly=1;�ξ���POST=\"POST\"�Ȥ��뤳��";
	}

	# �᡼�륽�եȥ����å�
	print "<LI>�᡼�륽\�եȥѥ���";
	if (-e $mailprog) { print "OK\n"; }
	else { print "NG �� $mailprog\n"; }

	# jcode.pl �С����������å�
	print "<LI>jcode.pl�С����������å���";
	$flag=0;
	open(IN, $jcode);
	while (<IN>) {
		if ($_ =~ /jcode\.pl\,v (\d)\.(\d+)/) {
			$v1=$1; $v2=$2; $flag=1; last;
		}
	}
	close(IN);
	if ($v1 < 2 || $v2 < 13) {
		print "�С�������㤤�褦�Ǥ����� $v1.$v2\n";
	} else {
		print "�С������OK (v $v1.$v2)\n";
	}

	print "</UL>\n<body></html>\n";
	exit;
}

__END__

