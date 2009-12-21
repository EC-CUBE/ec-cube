<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.    See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA    02111-1307, USA.
 */
*}-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">

<head>
<meta http-equiv="content-type" content="application/xhtml+xml; charset=<!--{$smarty.const.CHAR_CODE}-->" />
<meta http-equiv="content-script-type" content="text/javascript" />
<meta http-equiv="content-style-type" content="text/css" />
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/win_op.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/site.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/admin.js"></script>
<!--{include file='css/contents.tpl'}-->
<title><!--{$tpl_subtitle}--></title>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function lfPopwinSubmit(formName) {
    win02('about:blank','pdf','1000','900');
    document[formName].target = "pdf";
    document[formName].submit();
    return false;
}
//-->
</script>
</head>

<body bgcolor="#ffffff" text="#666666" link="#007bb7" vlink="#007bb7" alink="#cc0000" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onload="<!--{$tpl_onload}-->">
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_ADMIN_CSS}-->common.css" type="text/css" />
</noscript>

<div align="center">

<form name="form1" id="form1" method="post" action="<!--{$smarty.server.PHP_SELF}-->">
<input type="hidden" name="mode" value="confirm">
<!--{foreach from=$arrForm.order_id item=order_id}-->
    <input type="hidden" name="order_id[]" value="<!--{$order_id}-->">
<!--{/foreach}-->
<input type="hidden" name="uniqid" value="<!--{$tpl_uniqid}-->">

<table width="500" border="0" cellspacing="0" cellpadding="0" summary=" ">
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
                            <td bgcolor="#636469" width="400" class="fs14n"><span class="white"><!--コンテンツタイトル-->帳票の作成</span></td>
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
                                <td width="120" bgcolor="#f3f3f3">注文番号</td>
                                    <td width="307" bgcolor="#ffffff">
                                    <!--{foreach name=order_id from=$arrForm.order_id item=order_id}-->
                                        <!--{$order_id|escape}--><!--{if $smarty.foreach.order_id.last === false}-->,<!--{/if}-->
                                    <!--{/foreach}-->
                                    </td>
                            </tr>
                            <tr class="fs12n">
                                <td width="120" bgcolor="#f3f3f3">発行日<span class="red">※</span></td>
                                <td width="307" bgcolor="#ffffff"><!--{if $arrErr.year}--><span class="red"><!--{$arrErr.year}--></span><!--{/if}-->
                                    <select name="year">
                                    <!--{html_options options=$arrYear selected=$arrForm.year}-->
                                    </select>年
                                    <select name="month">
                                    <!--{html_options options=$arrMonth selected=$arrForm.month}-->
                                    </select>月
                                    <select name="day">
                                    <!--{html_options options=$arrDay selected=$arrForm.day}-->
                                    </select>日
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td width="120" bgcolor="#f3f3f3">帳票の種類</td>
                                <td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
                                    <select name="type">
                                    <!--{html_options options=$arrType selected=$arrForm.type}-->
                                    </select>
                                </td>
                            </tr>
                            <tr class="fs12n">
                                <td width="120" bgcolor="#f3f3f3">ダウンロード方法</td>
                                <td width="307" bgcolor="#ffffff"><!--{if $arrErr.download}--><span class="red"><!--{$arrErr.download}--></span><!--{/if}-->
                                    <select name="download">
                                    <!--{html_options options=$arrDownload selected=$arrForm.download}-->
                                    </select>
                                </td>
                            </tr>
                            <tr class="fs12">
                                <td width="120" bgcolor="#f3f3f3">帳票タイトル</td>
                                <td width="307" bgcolor="#ffffff"><!--{if $arrErr.title}--><span class="red"><!--{$arrErr.title}--></span><!--{/if}-->
                                    <input type="text" name="title" size="40" value="<!--{$arrForm.title}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
                                    <span style="font-size: 80%;">※未入力時はデフォルトのタイトルが表示されます。</span><br />
                                </td>
                            </tr>
                            <tr class="fs12">
                                <td width="120" bgcolor="#f3f3f3">帳票メッセージ</td>
                                <td width="307" bgcolor="#ffffff"><!--{if $arrErr.msg1}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
                                    1行目：<input type="text" name="msg1" size="40" value="<!--{$arrForm.msg1}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
                                    <!--{if $arrErr.msg2}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
                                    2行目：<input type="text" name="msg2" size="40" value="<!--{$arrForm.msg2}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
                                    <!--{if $arrErr.msg3}--><span class="red"><!--{$arrErr.msg3}--></span><!--{/if}-->
                                    3行目：<input type="text" name="msg3" size="40" value="<!--{$arrForm.msg3}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
                                    <span style="font-size: 80%;">※未入力時はデフォルトのメッセージが表示されます。</span><br />
                                </td>
                            </tr>
                            <tr class="fs12">
                                <td width="120" bgcolor="#f3f3f3">備考</td>
                                <td width="307" bgcolor="#ffffff">
                                    1行目：<input type="text" name="etc1" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
                                    <!--{if $arrErr.etc2}--><span class="red"><!--{$arrErr.msg1}--></span><!--{/if}-->
                                    2行目：<input type="text" name="etc2" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
                                    <!--{if $arrErr.etc3}--><span class="red"><!--{$arrErr.msg3}--></span><!--{/if}-->
                                    3行目：<input type="text" name="etc3" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
                                    <span style="font-size: 80%;">※未入力時は表示されません。</span><br />
                                </td>
                            </tr>
                            <!--{if $smarty.const.USE_POINT === true}-->
                            <tr class="fs12">
                                <td width="120" bgcolor="#f3f3f3">ポイント表記</td>
                                <td width="307" bgcolor="#ffffff">
                                    <input type="radio" name="disp_point" value="1" checked="checked" />する　<input type="radio" name="disp_point" value="0" />しない<br />
                                    <span style="font-size: 80%;">※「する」を選択されても、お客様が非会員の場合は表示されません。</span>
                                </td>
                            </tr>
                            <!--{else}-->
                                <input type="hidden" name="disp_point" value="0" />
                            <!--{/if}-->
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
                                            <td>
                                                <input type="button" name="pdf_input" value="この内容で作成する" onclick="return lfPopwinSubmit('form1');" />
                                            </td>
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
        </table>
    </td>
</tr>
</table>

</form>

</div>

</body>
</html>