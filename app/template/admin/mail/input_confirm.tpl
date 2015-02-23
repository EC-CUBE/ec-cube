<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
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

<script type="text/javascript">
<!--
function winSubmitMail(URL,formName,Winname,Wwidth,Wheight){
    var WIN = window.open(URL,Winname,"width="+Wwidth+",height="+Wheight+",scrollbars=yes,resizable=yes,toolbar=no,location=no,directories=no,status=no,menubar=no");
    document.forms[formName].target = Winname;
    document.forms[formName].submit();
    WIN.focus();
}
//-->
</script>
<form name="form1" id="form1" method="post" action="?">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="template">
    <input type="hidden" name="subject" value="<!--{$arrForm.subject.value|h}-->">
    <input type="hidden" name="body" value="<!--{$arrForm.body.value|h}-->">
    <input type="hidden" name="mail_method" value="<!--{$arrForm.mail_method.value|h}-->">
    <input type="hidden" name="template_id" value="<!--{$arrForm.template_id.value|h}-->">
    <!--{foreach key=key item=item from=$arrHidden}-->
        <!--{if is_array($item)}-->
            <!--{foreach item=c_item from=$item}-->
                <input type="hidden" name="<!--{$key}-->[]" value="<!--{$c_item|h}-->" />
            <!--{/foreach}-->
        <!--{else}-->
            <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
        <!--{/if}-->
    <!--{/foreach}-->
    <div id="mail" class="contents-main">
        <table class="form">
            <tr>
                <th>Subject</th>
                <td><!--{$arrForm.subject.value|h}--></td>
            </tr>
            <!--{if $arrForm.mail_method.value ne 2}-->
                <tr>
                    <td colspan="2"><a href="javascript:;" onclick="winSubmitMail('','form2','preview',650,700); return false;">HTMLで確認</a>
                </tr>
            <!--{/if}-->
            <tr>
                <th>本文</th>
                <td><!--{$arrForm.body.value|h|nl2br}--></td>
            </tr>
        </table>

        <div class="btn-area">
            <ul>
                <li><a class="btn-action" href="javascript:;" name="subm02" onclick="return eccube.insertValueAndSubmit( document.form1, 'mode', 'regist_back', '' ); return false;"><span class="btn-prev">テンプレート設定画面へ戻る</span></a></li>
                <li><a class="btn-action" href="javascript:;" name="subm03" onclick="return eccube.insertValueAndSubmit( document.form1, 'mode', 'regist_complete', '' ); return false;"><span class="btn-next">配信する</span></a></li>
            </ul>
        </div>
    </div>
</form>
<form name="form2" id="form2" method="post" action="./preview.php" target="_blank">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="presend" />
    <input type="hidden" name="body" value="<!--{$arrForm.body.value|h}-->" />
</form>
