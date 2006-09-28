<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<!--{*
 * Copyright(c) 2000-2006 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=EUC-JP" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="../css/contents.css" type="text/css" />
<script type="text/javascript" src="../js/css.js"></script>
<script type="text/javascript" src="../js/navi.js"></script>
<title>ECサイト管理者ページ</title>
</head>

<body bgcolor="#f3f3f3" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="preLoadImg()">
<noscript>
<link rel="stylesheet" href="../css/common.css" type="text/css" />
</noscript>


<div align="center">
<!--▼HEADER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#bfbf9f" height="3" colspan="2"></td></tr>
	<tr bgcolor="#dedecd">
		<td height="40" width="365"><a href="../home.php"><img src="../misc/logo.gif" width="365" height="16" alt="ECサイト管理者ページ" border="0" /></a></td>
		<td width="415" align="right"><a href="../home.php" onmouseover="chgImg('../misc/home_on.gif','home');" onmouseout="chgImg('../misc/home.gif','home');"><img src="../misc/home.gif" width="70" height="20" alt="HOME" border="0" name="home" id="home" /></a><img src="../misc/_.gif" width="3" height="1" alt="" />
		<a href="#" onmouseover="chgImg('../misc/hp_on.gif','hp');" onmouseout="chgImg('../misc/hp.gif','hp');" target="_blank"><img src="../misc/hp.gif" width="70" height="20" alt="HPを見る" border="0" name="hp" id="hp" /></a><img src="../misc/_.gif" width="3" height="1" alt="" />
		<a href="../index.html" onmouseover="chgImg('../misc/logout_on.gif','logout');" onmouseout="chgImg('../misc/logout.gif','logout');"><img src="../misc/logout.gif" width="70" height="20" alt="ログアウト" border="0" name="logout" id="logout" /></a><img src="../misc/_.gif" width="20" height="1" alt="" /></td>
	</tr>
	<tr><td bgcolor="#ffffff" height="1" colspan="3"></td></tr>
</table>
<!--▲HEADER-->

<!--▼MAIN NAVI-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td><a href="../basis/index.php" onmouseover="chgImg('../misc/basis_on.gif','basis');" onmouseout="chgImg('../misc/basis.gif','basis');"><img src="../misc/basis.gif" width="108" height="26" alt="基本情報" border="0" name="basis" id="basis" /></a></td>
		<td><a href="../product/index.php"><img src="../misc/product_on.gif" width="108" height="26" alt="商品管理" border="0" /></a></td>
		<td><a href="../customer/index.php" onmouseover="chgImg('../misc/customer_on.gif','customer');" onmouseout="chgImg('../misc/customer.gif','customer');"><img src="../misc/customer.gif" width="108" height="26" alt="顧客管理" border="0" name="customer" id="customer" /></a></td>
		<td><a href="../order/index.php" onmouseover="chgImg('../misc/order_on.gif','order');" onmouseout="chgImg('../misc/order.gif','order');"><img src="../misc/order.gif" width="108" height="26" alt="受注管理" border="0" name="order" id="order" /></a></td>
		<td><a href="../sales/index.php" onmouseover="chgImg('../misc/sales_on.gif','sales');" onmouseout="chgImg('../misc/sales.gif','sales');"><img src="../misc/sales.gif" width="108" height="26" alt="売上管理" border="0" name="sales" id="sales" /></a></td>
		<td><a href="../mail/index.php" onmouseover="chgImg('../misc/mail_on.gif','mail');" onmouseout="chgImg('../misc/mail.gif','mail');"><img src="../misc/mail.gif" width="108" height="26" alt="メール配信" border="0" name="mail" id="mail" /></a></td>
		<td><a href="../contents/index.php" onmouseover="chgImg('../misc/contents_on.gif','contents');" onmouseout="chgImg('../misc/contents.gif','contents');"><img src="../misc/contents.gif" width="132" height="26" alt="コンテンツ管理" border="0" name="contents" id="contents" /></a></td>
	</tr>
	<tr><td colspan="7" height="1" bgcolor="#dedecd"></td></tr>
	<tr><td colspan="7" bgcolor="#ffffff" height="15"></td></tr>
</table>
<!--▲MAIN NAVI-->

<!--▼CONTENTS-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td bgcolor="#ffffff" align="center" valign="top" height="400">
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<!--▼SUB NAVI-->
				<td class="fs12n"><span class="over">■商品マスタ</span>　<a href="./csv.php">■CSVアップロード</a>　<a href="./input.php">■商品登録</a>　<a href="./standard_input.php">■規格登録</a>　<a href="./category.php">■カテゴリ設定</a>　<a href="./review.php">■レビュー管理</a></td>
				<!--▲SUB NAVI-->
			</tr><tr><td height="25"></td></tr>
		</table>
		
		<!--▼MAIN CONTENTS-->
		<table width="740" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td class="fs14n"><strong>■商品マスタ：商品編集</strong></td>
			</tr>
		</table>
		
		<!--▼登録テーブルここから-->
		<form name="form1" id="form1" method="post" action="">
		<table width="740" border="0" cellspacing="1" cellpadding="5" summary=" " bgcolor="#cccccc">
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">商品コード<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><input type="text" name="code" size="60" class="box60" /><span class="red"> （上限50文字）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">商品名<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><input type="text" name="name" size="60" class="box60" /><span class="red"> （上限50文字）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">カテゴリ<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><select name="category">
				<option value="" selected="selected">選択してください</option>
				<option value="ダミー">ダミー</option>
				</select></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="160">商品価格<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557">￥<input type="text" name="price" size="6" class="box6" /><span class="red10"> （半角数字で入力 例：28800）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">製造元</td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><input type="text" name="maker" size="60" class="box60" /><span class="red"> （上限50文字）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">販売元</td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><input type="text" name="dealer" size="60" class="box60" /><span class="red"> （上限50文字）</span></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="160">商品ステータス<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><input type="radio" name="status" value="無し" checked="checked" />無し　<input type="radio" name="status" value="NEW" />NEW　<input type="radio" name="status" value="オススメ" />オススメ　<input type="radio" name="status" value="注目" />注目　<input type="radio" name="status" value="限定" />限定</td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">一覧画面コメント<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><textarea name="textfield01" cols="60" rows="8" class="area60"></textarea><span class="red"> （上限200文字）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">おすすめポイント<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><textarea name="textfield02" cols="60" rows="8" class="area60"></textarea><span class="red"> （上限3000文字）</span></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">製品紹介<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><textarea name="textfield03" cols="60" rows="8" class="area60"></textarea><span class="red"> （上限3000文字）</span></td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="160">在庫数<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><input type="text" name="stock" size="6" class="box6" /> 単位　<input type="checkbox" name="nostock" value="在庫制限無し" />在庫制限無し</td>
			</tr>
			<tr class="fs12n">
				<td bgcolor="#f0f0f0" width="160">購入制限数<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><input type="text" name="purchase" size="6" class="box6" /> 単位まで</td>
			</tr>
			<tr class="fs12">
				<td bgcolor="#f0f0f0" width="160">配送業者<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><input type="checkbox" name="delivery01" value="ヤマト運輸" />ヤマト運輸<br />
				<input type="checkbox" name="delivery02" value="佐川急便" />佐川急便<br />
				<input type="checkbox" name="delivery03" value="日本郵政公社" />日本郵政公社</td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">一覧用画像</td>
				<td bgcolor="#ffffff" width="557"><input type="file" name="image00" size="60" class="box60" /></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">詳細用画像（1）<span class="red"> *</span></td>
				<td bgcolor="#ffffff" width="557"><input type="file" name="image01" size="60" class="box60" /></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">詳細用画像（2）</td>
				<td bgcolor="#ffffff" width="557"><input type="file" name="image02" size="60" class="box60" /></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">詳細用画像（3）</td>
				<td bgcolor="#ffffff" width="557"><input type="file" name="image03" size="60" class="box60" /></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">別ウインドウ用画像</td>
				<td bgcolor="#ffffff" width="557"><input type="file" name="image_window" size="60" class="box60" /></td>
			</tr>
			<tr>
				<td bgcolor="#f0f0f0" width="160" class="fs12n">商品詳細ファイル</td>
				<td bgcolor="#ffffff" width="557" class="fs10n"><input type="file" name="product_file" size="60" class="box60" /><span class="red"> （○○形式）</span></td>
			</tr>
		</table>
		<!--▲登録テーブルここまで-->
		<br />
		<input type="button" name="subm" value="この内容で登録する" />
		</form>
		
		<!--▲MAIN CONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->

<!--▼FOOTER-->
<table width="780" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr><td bgcolor="#ffffff" height="60"></td></tr>
	<tr><td bgcolor="#e5e5df" height="5"></td></tr>
</table>
<!--▲FOOTER-->
</div>
</body>
</html>