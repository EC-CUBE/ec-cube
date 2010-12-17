<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2010 LOCKON CO.,LTD. All Rights Reserved.
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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<!--{$smarty.const.CHAR_CODE}-->">
<meta http-equiv="content-script-type" content="text/javascript">
<meta http-equiv="content-style-type" content="text/css">
<link rel="stylesheet" href="<!--{$TPL_DIR}-->css/admin_contents.css" type="text/css" media="all" />
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/css.js"></script>
<script type="text/javascript" src="<!--{$smarty.const.URL_DIR}-->js/navi.js"></script>
<script type="text/javascript" src="<!--{$TPL_DIR}-->js/admin.js"></script>
<script type="text/javascript">
<!--
self.moveTo(20,20);self.focus();

function func_submit(customer_id){
    var fm = window.opener.document.form1;
    fm.edit_customer_id.value = customer_id;
    fm.mode.value = 'search_customer';
    fm.submit();
    window.close();
    return false;
}
//-->
</script>

<title>管理機能</title>
</head>

<body bgcolor="#ffffff" text="#494E5F" link="#006699" vlink="#006699" alink="#006699" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<!--{$GLOBAL_ERR}-->
<noscript>
<link rel="stylesheet" href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.ADMIN_DIR}-->css/common.css" type="text/css" >
</noscript>

<!--▼CONTENTS-->
<div align="center">
　
<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|escape}-->">
<input name="mode" type="hidden" value="search">
<input name="search_pageno" type="hidden" value="">
<input name="customer_id" type="hidden" value="">
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
    <tr class="fs12n">
        <td bgcolor="#f0f0f0" width="100">顧客ID</td>
        <td bgcolor="#ffffff" width="287" colspan="2">
            <!--{if $arrErr.search_customer_id}--><span class="red12"><!--{$arrErr.search_customer_id}--></span><!--{/if}-->
            <input type="text" name="search_customer_id" value="<!--{$arrForm.search_customer_id|escape}-->" size="40" class="box40" style="<!--{$arrErr.search_customer_id|sfGetErrorColor}-->"/>
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">顧客名</td>
        <td bgcolor="#ffffff">
            <!--{if $arrErr.search_name01}--><span class="red12"><!--{$arrErr.search_name01}--></span><!--{/if}-->
            <!--{if $arrErr.search_name02}--><span class="red12"><!--{$arrErr.search_name02}--></span><!--{/if}-->
            姓&nbsp;&nbsp;<input type="text" name="search_name01" value="<!--{$arrForm.search_name01|escape}-->" size="15" class="box15" style="<!--{$arrErr.search_name01|sfGetErrorColor}-->"/>
            &nbsp;名&nbsp;&nbsp;<input type="text" name="search_name02" value="<!--{$arrForm.search_name02|escape}-->" size="15" class="box15" style="<!--{$arrErr.search_name02|sfGetErrorColor}-->"/>
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">顧客名(カナ)</td>
        <td bgcolor="#ffffff">
            <!--{if $arrErr.search_kana01}--><span class="red12"><!--{$arrErr.search_kana01}--></span><!--{/if}-->
            <!--{if $arrErr.search_kana02}--><span class="red12"><!--{$arrErr.search_kana02}--></span><!--{/if}-->
            セイ<input type="text" name="search_kana01" value="<!--{$arrForm.search_kana01|escape}-->" size="15" class="box15" style="<!--{$arrErr.search_kana01|sfGetErrorColor}-->"/>
                                                メイ&nbsp;<input type="text" name="search_kana02" value="<!--{$arrForm.search_kana02|escape}-->" size="15" class="box15" style="<!--{$arrErr.search_kana02|sfGetErrorColor}-->"/>
        </td>
    </tr>
</table>

<br />
<input type="submit" name="subm" value="検索を開始" />
<br />
<br />
<!--{if $smarty.post.mode == 'search' }-->
    <!--▼検索結果表示-->
    <table width="420" border="0" cellspacing="0" cellpadding="0" summary=" " bgcolor="#FFFFFF">
        <!--{if $tpl_linemax > 0}-->
            <tr class="fs12">
                <td align="left"><!--{$tpl_linemax}-->件が該当しました。	</td>
            </tr>
        <!--{/if}-->
        <tr class="fs12">
            <td align="center">
            <!--▼ページナビ-->
            <!--{$tpl_strnavi}-->
            <!--▲ページナビ-->
            </td>
        </tr>
        <tr><td height="10"></td></tr>
    </table>

    <!--▼検索後表示部分-->
    <table width="420" border="0" cellspacing="1" cellpadding="5" bgcolor="#cccccc">
        <tr bgcolor="#f0f0f0" align="center" class="fs12">
            <td>顧客ID</td>
            <td>顧客名(カナ)</td>
            <td>TEL</td>
            <td>決定</td>
        </tr>
        <!--{section name=cnt loop=$arrCustomer}-->
        <!--▼顧客<!--{$smarty.section.cnt.iteration}-->-->
        <tr bgcolor="#FFFFFF" class="fs12n">
            <td width="90" align="center">
            <!--{$arrCustomer[cnt].customer_id|escape}-->
            </td>
            <td><!--{$arrCustomer[cnt].name01|escape}--><!--{$arrCustomer[cnt].name02|escape}-->(<!--{$arrCustomer[cnt].kana01|escape}--><!--{$arrCustomer[cnt].kana02|escape}-->)</td>
            <td><!--{$arrCustomer[cnt].tel01|escape}-->-<!--{$arrCustomer[cnt].tel02|escape}-->-<!--{$arrCustomer[cnt].tel03|escape}--></td>
            <td align="center"><a href="" onClick="return func_submit(<!--{$arrCustomer[cnt].customer_id}-->)">決定</a></td>
        </tr>
        <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{sectionelse}-->
        <tr bgcolor="#FFFFFF" class="fs10n">
            <td colspan="4">会員情報が存在しません。</td>
        </tr>
        <!--{/section}-->
    </table>

    <!--▲検索結果表示-->
<!--{/if}-->
</form>

</div>
<!--▲CONTENTS-->

</body>
</html>
