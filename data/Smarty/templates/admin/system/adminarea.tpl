<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2012 LOCKON CO.,LTD. All Rights Reserved.
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
jQuery(function(){
    $("a.btn-action").click(function(){
        $("form#form1").submit();
        return false;
    });
});
</script>
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<!--{* ▼登録テーブルここから *}-->
<div id="system" class="contents-main">
    <div class="notice">
        <p class="remark"><span class="attention">間違った設定を適用すると管理画面にアクセス出来なくなる可能性があります。<br/>
        良く解らない場合はこの設定は変更しないでください。</span></p>
        <!--{if $arrErr.all}-->
            <p class="error"><!--{$arrErr.all|h}--></p>
        <!--{/if}-->
    </div>
    <h2>管理機能設定</h2>
    <table id="basis-index-admin">
        <tr>
            <th>ディレクトリ名</th>
            <td>
                <!--{assign var=key value="admin_dir"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <!--{$smarty.const.ROOT_URLPATH}--><input type="text" name="<!--{$key}-->" value="<!--{$arrForm[$key]|h}-->" maxlength="<!--{$smarty.const.ID_MAX_LEN}-->" size="40" class="box40" style="<!--{if $arrErr[$key] != ""}-->background-color: <!--{$smarty.const.ERR_COLOR}-->;<!--{/if}-->"/>/
            </td>
        </tr>
        <tr>
            <th>SSL制限</th>
            <td>
                <!--{assign var=key value="admin_force_ssl"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="checkbox" name="<!--{$key}-->" value="1" id="<!--{$key}-->" <!--{if $arrForm[$key] == 1}-->checked="checked"<!--{/if}--><!--{if !$tpl_enable_ssl}--> disabled="disabled"<!--{/if}--> /><label for="<!--{$key}-->">SSLを強制する。</label>
            </td>
        </tr>
        <tr>
            <th>IP制限</th>
            <td>
                <!--{assign var=key value="admin_allow_hosts"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <textarea name="<!--{$key}-->" cols="60" rows="8" class="area60" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" ><!--{$arrForm[$key]|h}--></textarea>
                <span class="attention"> (上限<!--{$smarty.const.LTEXT_LEN}-->文字)</span><br />
                <span>※管理機能へのアクセスを特定のIPアドレスからの接続のみに制限します。<br />
                アクセスを許可するIPアドレスを1行づつ入力してください。何も入力しない場合は全てを許可します。</span><br />
            </td>
        </tr>
    </table>


    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="#"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>
</div>
<div style="display: none">
    <div id="maparea">
        <div id="maps" style="width: 300px; height: 300px"></div>
        <a class="btn-normal" href="javascript:;" id="inputPoint">この位置を入力</a>
    </div>
</div>
<!--{* ▲登録テーブルここまで *}-->
</form>
