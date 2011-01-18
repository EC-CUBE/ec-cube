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
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->
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

<!--▼検索フォーム-->
<form name="form1" id="form1" method="post" action="<!--{$smarty.server.REQUEST_URI|h}-->">
<input name="mode" type="hidden" value="search">
<input name="search_pageno" type="hidden" value="">
<input name="customer_id" type="hidden" value="">
<table bgcolor="#cccccc" width="420" border="0" cellspacing="1" cellpadding="5" summary=" ">
    <tr class="fs12n">
        <td bgcolor="#f0f0f0" width="100">顧客ID</td>
        <td bgcolor="#ffffff" width="287" colspan="2">
            <!--{if $arrErr.search_customer_id}--><span class="red12"><!--{$arrErr.search_customer_id}--></span><!--{/if}-->
            <input type="text" name="search_customer_id" value="<!--{$arrForm.search_customer_id|h}-->" size="40" class="box40" style="<!--{$arrErr.search_customer_id|sfGetErrorColor}-->"/>
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">顧客名</td>
        <td bgcolor="#ffffff">
            <!--{if $arrErr.search_name01}--><span class="red12"><!--{$arrErr.search_name01}--></span><!--{/if}-->
            <!--{if $arrErr.search_name02}--><span class="red12"><!--{$arrErr.search_name02}--></span><!--{/if}-->
            姓&nbsp;&nbsp;<input type="text" name="search_name01" value="<!--{$arrForm.search_name01|h}-->" size="15" class="box15" style="<!--{$arrErr.search_name01|sfGetErrorColor}-->"/>
            &nbsp;名&nbsp;&nbsp;<input type="text" name="search_name02" value="<!--{$arrForm.search_name02|h}-->" size="15" class="box15" style="<!--{$arrErr.search_name02|sfGetErrorColor}-->"/>
        </td>
    </tr>
    <tr class="fs12n">
        <td bgcolor="#f0f0f0">顧客名(カナ)</td>
        <td bgcolor="#ffffff">
            <!--{if $arrErr.search_kana01}--><span class="red12"><!--{$arrErr.search_kana01}--></span><!--{/if}-->
            <!--{if $arrErr.search_kana02}--><span class="red12"><!--{$arrErr.search_kana02}--></span><!--{/if}-->
            セイ<input type="text" name="search_kana01" value="<!--{$arrForm.search_kana01|h}-->" size="15" class="box15" style="<!--{$arrErr.search_kana01|sfGetErrorColor}-->"/>
                                                メイ&nbsp;<input type="text" name="search_kana02" value="<!--{$arrForm.search_kana02|h}-->" size="15" class="box15" style="<!--{$arrErr.search_kana02|sfGetErrorColor}-->"/>
        </td>
    </tr>
</table>

<div class="btn-area">
  <ul>
    <li><a class="btn-normal" href="javascript:;" onclick="fnFormModeSubmit('form1', 'search', '', ''); return false;" name="subm">検索を開始</a></li>
  </ul>
</div>

<!--{if $smarty.post.mode == 'search' }-->
    <!--▼検索結果表示-->
        <!--{if $tpl_linemax > 0}-->
        <p><!--{$tpl_linemax}-->件が該当しました。<!--{$tpl_strnavi}--></p>
        <!--{/if}-->

    <!--▼検索後表示部分-->
    <table class="list">
        <tr>
            <th>顧客ID</th>
            <th>顧客名(カナ)</th>
            <th>TEL</th>
            <th>決定</th>
        </tr>
        <!--{section name=cnt loop=$arrCustomer}-->
        <!--▼顧客<!--{$smarty.section.cnt.iteration}-->-->
        <tr>
            <td>
            <!--{$arrCustomer[cnt].customer_id|h}-->
            </td>
            <td><!--{$arrCustomer[cnt].name01|h}--><!--{$arrCustomer[cnt].name02|h}-->(<!--{$arrCustomer[cnt].kana01|h}--><!--{$arrCustomer[cnt].kana02|h}-->)</td>
            <td><!--{$arrCustomer[cnt].tel01|h}-->-<!--{$arrCustomer[cnt].tel02|h}-->-<!--{$arrCustomer[cnt].tel03|h}--></td>
            <td align="center"><a href="" onClick="return func_submit(<!--{$arrCustomer[cnt].customer_id}-->)">決定</a></td>
        </tr>
        <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
        <!--{sectionelse}-->
        <tr>
            <td colspan="4">会員情報が存在しません。</td>
        </tr>
        <!--{/section}-->
    </table>

    <!--▲検索結果表示-->
<!--{/if}-->
</form>
<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
