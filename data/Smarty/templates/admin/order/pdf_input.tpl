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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_header.tpl"}-->

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

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="confirm" />
<!--{foreach from=$arrForm.order_id item=order_id}-->
    <input type="hidden" name="order_id[]" value="<!--{$order_id|h}-->">
<!--{/foreach}-->

<h2><!--コンテンツタイトル--><!--{t string="tpl_419"}--></h2>

<table class="form">
    <col width="20%" />
    <col width="80%" />
    <tr>
        <th><!--{t string="tpl_231"}--></th>
        <td><!--{$arrForm.order_id|@join:', '}--></td>
    </tr>
    <tr>
        <th><!--{t string="tpl_420"}--><span class="attention">※</span></th>
        <td><!--{if $arrErr.year}--><span class="attention"><!--{$arrErr.year}--></span><!--{/if}-->
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
    <tr>
        <th><!--{t string="tpl_421"}--></th>
        <td><!--{if $arrErr.download}--><span class="attention"><!--{$arrErr.download}--></span><!--{/if}-->
            <select name="type">
            <!--{html_options options=$arrType selected=$arrForm.type}-->
            </select>
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_422"}--></th>
        <td><!--{if $arrErr.download}--><span class="attention"><!--{$arrErr.download}--></span><!--{/if}-->
            <select name="download">
            <!--{html_options options=$arrDownload selected=$arrForm.download}-->
            </select>
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_423"}--></th>
        <td><!--{if $arrErr.title}--><span class="attention"><!--{$arrErr.title}--></span><!--{/if}-->
            <input type="text" name="title" size="40" value="<!--{$arrForm.title}-->" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_424"}--></span><br />
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_425"}--></th>
        <td><!--{if $arrErr.msg1}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_426"}--><input type="text" name="msg1" size="40" value="<!--{$arrForm.msg1|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
            <!--{if $arrErr.msg2}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_427"}--><input type="text" name="msg2" size="40" value="<!--{$arrForm.msg2|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
            <!--{if $arrErr.msg3}--><span class="attention"><!--{$arrErr.msg3}--></span><!--{/if}-->
            <!--{t string="tpl_428"}--><input type="text" name="msg3" size="40" value="<!--{$arrForm.msg3|h}-->" maxlength="<!--{$smarty.const.STEXT_LEN*3/5}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_429"}--></span><br />
        </td>
    </tr>
    <tr>
        <th><!--{t string="tpl_365"}--></th>
        <td>
            <!--{t string="tpl_426"}--><input type="text" name="etc1" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.etc2}--><span class="attention"><!--{$arrErr.msg1}--></span><!--{/if}-->
            <!--{t string="tpl_427"}--><input type="text" name="etc2" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <!--{if $arrErr.etc3}--><span class="attention"><!--{$arrErr.msg3}--></span><!--{/if}-->
            <!--{t string="tpl_428"}--><input type="text" name="etc3" size="40" value="" maxlength="<!--{$smarty.const.STEXT_LEN}-->"/><br />
            <span style="font-size: 80%;"><!--{t string="tpl_430"}--></span><br />
        </td>
    </tr>
    <!--{if $smarty.const.USE_POINT !== false}-->
        <tr>
            <th><!--{t string="tpl_431"}--></th>
            <td>
                <input type="radio" name="disp_point" value="1" checked="checked" /><!--{t string="tpl_432"}-->　<input type="radio" name="disp_point" value="0" /><!--{t string="tpl_433"}--><br />
                <span style="font-size: 80%;"><!--{t string="tpl_434"}--></span>
            </td>
        </tr>
    <!--{else}-->
        <input type="hidden" name="disp_point" value="0" />
    <!--{/if}-->
</table>

<div class="btn-area">
    <ul>
        <li><a class="btn-action" href="javascript:;" onclick="return lfPopwinSubmit('form1');"><span class="btn-next"><!--{t string="tpl_435"}--></span></a></li>
    </ul>
</div>

</form>

<!--{include file="`$smarty.const.TEMPLATE_ADMIN_REALDIR`admin_popup_footer.tpl"}-->
