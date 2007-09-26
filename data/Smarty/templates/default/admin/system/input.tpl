<!--{*
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 */
*}-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}-->admin/css/contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title>メンバー登録・編集</title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">
<!--★★メインコンテンツ★★-->
<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
<form name="form1" id="form1" method="post" action="" onsubmit="return fnRegistMember();">
<input type="hidden" name="mode" value="<!--{$tpl_mode|escape}-->">
<input type="hidden" name="member_id" value="<!--{$tpl_member_id|escape}-->">
<input type="hidden" name="pageno" value="<!--{$tpl_pageno|escape}-->">
<input type="hidden" name="old_login_id" value="<!--{$tpl_old_login_id|escape}-->">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid|escape}-->">
    <tr valign="top">
        <td class="mainbg">
            <!--▼登録テーブルここから-->
            <table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
                <!--メインエリア-->
                <tr>
                    <td align="center">
                        <table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
                            <tr><td height="14"></td></tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_top.jpg" width="470" height="14" alt=""></td>
                            </tr>
                            <tr>
                                <td background="<!--{$TPL_DIR}-->img/contents/main_left.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                                <td bgcolor="#cccccc">

                                    <table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                        <tr>
                                            <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_top.gif" width="440" height="7" alt=""></td>
                                        </tr>
                                        <tr>
                                            <td background="<!--{$TPL_DIR}-->img/contents/contents_title_left_bg.gif"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
                                            <td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->メンバー登録/編集</span></td>
                                            <td background="<!--{$TPL_DIR}-->img/contents/contents_title_right_bg.gif"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="18" height="1" alt=""></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/contents_title_bottom.gif" width="440" height="7" alt=""></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bar.jpg" width="440" height="10" alt=""></td>
                                        </tr>
                                    </table>

                                    <table width="440" border="0" cellspacing="1" cellpadding="8" summary=" ">
                                        <tr class="fs12n">
                                            <td width="90" bgcolor="#f3f3f3">名前</td>
                                            <td width="337" bgcolor="#ffffff"><!--{if $arrErr.name}--><span class="red"><!--{$arrErr.name}--></span><!--{/if}--><input type="text" name="name" size="30" class="box30" value="<!--{$arrForm.name}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>　<span class="red">※必須入力</span>
                                            </td>
                                        </tr>
                                        <tr class="fs12n">
                                            <td width="90" bgcolor="#f3f3f3">所属</td>
                                            <td width="337" bgcolor="#ffffff"><!--{if $arrErr.department}--><span class="red"><!--{$arrErr.department}--></span><!--{/if}--><input type="text" name="department" size="30" class="box30" value="<!--{$arrForm.department}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>
                                            </td>
                                        </tr>
                                        <tr class="fs12">
                                            <td width="90" bgcolor="#f3f3f3">ログインＩＤ</td>
                                            <td width="337" bgcolor="#ffffff"><!--{if $arrErr.login_id}--><span class="red"><!--{$arrErr.login_id}--></span><!--{/if}--><input type="text" name="login_id" size="20" class="box20"  value="<!--{$arrForm.login_id}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>　<span class="red">※必須入力</span><br />
                                            ※半角英数字・15文字以内</td>
                                        </tr>
                                        <tr class="fs12">
                                            <td width="90" bgcolor="#f3f3f3">パスワード</td>
                                            <td width="337" bgcolor="#ffffff"><!--{if $arrErr.password}--><span class="red"><!--{$arrErr.password}--></span><!--{/if}--><input type="password" name="password" size="20" class="box20" value="<!--{$arrForm.password}-->" onfocus="<!--{$tpl_onfocus}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/>　<span class="red">※必須入力</span><br />
                                            ※半角英数字・15文字以内</td>
                                        </tr>
                                        <tr class="fs12n">
                                            <td width="90" bgcolor="#f3f3f3">管理権限</td>
                                            <td width="337" bgcolor="#ffffff"><!--{if $arrErr.authority}--><span class="red"><!--{$arrErr.authority}--></span><!--{/if}--><select name="authority">
                                            <option value="" >選択してください</option>
                                            <!--{html_options options=$arrAUTHORITY selected=$arrForm.authority}-->
                                            </select>　<span class="red">※必須入力</span></td>
                                        </tr>
                                    </table>

                                    <table width="440" border="0" cellspacing="0" cellpadding="0" summary=" ">
                                        <tr>
                                            <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                            <td><img src="<!--{$TPL_DIR}-->img/contents/tbl_top.gif" width="438" height="7" alt=""></td>
                                            <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="5" alt=""></td>
                                        </tr>
                                        <tr>
                                            <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                            <td bgcolor="#e9e7de" align="center">
                                            <table border="0" cellspacing="0" cellpadding="0" summary=" ">
                                                <tr>
                                                    <td><input type="image" onMouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist_on.jpg',this)" onMouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/contents/btn_regist.jpg',this)" src="<!--{$TPL_DIR}-->img/contents/btn_regist.jpg" width="123" height="24" alt="この内容で登録する" border="0" name="subm" ></td>
                                                </tr>
                                            </table>
                                            </td>
                                            <td bgcolor="#cccccc"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="1" height="10" alt=""></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/tbl_bottom.gif" width="440" height="8" alt=""></td>
                                        </tr>
                                    </table>
                                </td>
                                <td background="<!--{$TPL_DIR}-->img/contents/main_right.jpg"><img src="<!--{$TPL_DIR}-->img/common/_.gif" width="14" height="1" alt=""></td>
                            </tr>
                            <tr>
                                <td colspan="3"><img src="<!--{$TPL_DIR}-->img/contents/main_bottom.jpg" width="470" height="14" alt=""></td>
                            </tr>
                            <tr><td height="30"></td></tr>
                        </table>
                    </td>
                </tr>
                <!--メインエリア-->
            </table>
            <!--▲登録テーブルここまで-->
        </td>
    </tr>
</form>
</table>
<!--★★メインコンテンツ★★-->
</div>

</body>
</html>


