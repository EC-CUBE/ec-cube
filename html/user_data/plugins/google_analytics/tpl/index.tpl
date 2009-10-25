<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/admin_contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/jquery.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/admin.js"></script>
<title><!--{$tpl_subtitle}--></title>
<script type="text/javascript">

</script>
<style type="text/css">
@charset "UTF-8";
</style>
</head>

<body onload="<!--{$tpl_onload}-->" class="authority_0" style="min_width: 400px">
<noscript>
  <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<div id="container">
<a name="top"></a>
<!--▼CONTENTS-->
<h1>Google Analytics Plugin</h1>
<div id="contents" class="clear-block">
  <form method="post" action="index.php">
    <div id="system" class="contents-main">
      <table class="list">
	<tr>
	  <th>ウェブ プロパティ ID</th>
	  <td>UA-<input type="text" name="ga_ua" value="<!--{$smarty.const.GA_UA}-->" /></td>
	</tr>
      </table>
      <div class="btn addnew">
	<input type="hidden" name="mode" value="register" />
	<button type="submit"><span>この内容で登録する</span></button>
      </div>
    </div>
  </form>
</div>
<!--▲CONTENTS-->
</div>
</body>
</html>
