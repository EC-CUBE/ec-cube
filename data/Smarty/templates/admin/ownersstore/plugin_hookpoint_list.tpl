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

<script type="text/javascript">//<![CDATA[
    $(function() {

        /**
         * 「有効/有効にする」チェックボタン押下時
         */
        $('input[name^=plugin_hookpoint_use]').change(function(event) {
            // モード(有効 or 無効)
            var value = event.target.value;
            var id = event.target.id;

            if(value === '0') {
                result = window.confirm('無効にしても宜しいですか？');
                if(result === false) {
                    //$(event.target).attr("checked", "checked");
                    event.target.value = '1';
                }
            } else if(value === '1') {
                result = window.confirm('有効にしても宜しいですか？');
                if(result === false) {
                    //$(event.target).attr("checked", "checked");
                    event.target.value = '0';
                }
            }
            // プラグインフックID
            eccube.setModeAndSubmit('update_use', 'plugin_hookpoint_id', id);
        });
    });

//]]></script>

<!--<form name="form1" id="form1" method="post" action="?">-->
<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="conflict_check" />
<input type="hidden" name="plugin_hookpoint_id" value="" />
<div id="system" class="contents-main">
    <!--▼プラグイン一覧ここから-->
    <h2>フックポイント別プラグイン一覧</h2>
    <!--{if count($arrHookPoint) > 0}-->

        <span class="attention"><!--{$arrErr.plugin_error}--></span>
        <table class="system-plugin" width="900">
            <col width="40%" />
            <col width="5" />
            <col width="40%" />
            <col width="15%" />
            <tr>
                <th>フックポイント</th>
                <th>優先度</th>
                <th>プラグイン名</th>
                <th>利用ON/OFF</th>
            </tr>
    <!--{foreach from=$arrHookPoint item=hookpoint}-->
    <!--{foreach from=$hookpoint item=val name="plugin"}-->
            <tr>
                <!--{if $hookpoint|@count > 0 && $smarty.foreach.plugin.iteration == '1'}-->
                <td <!--{if in_array($val.hook_point, $arrConflict)}-->bgcolor="pink"<!--{/if}--> rowspan="<!--{$hookpoint|@count}-->">
                    <!--{$val.hook_point}-->
                    <!--{if in_array($val.hook_point, $arrConflict)}--><br /><span class="attention">※ 競合中</span><!--{/if}-->
                </td>
                <!--{elseif !$smarty.foreach.plugin.iteration > 1}-->
                <td <!--{if in_array($val.hook_point, $arrConflict)}-->bgcolor="pink"<!--{/if}-->>
                    <!--{$val.hook_point}-->
                    <!--{if in_array($val.hook_point, $arrConflict)}--><br /><span class="attention">※ 競合中</span><!--{/if}-->
                </td>
                <!--{/if}-->
                <td<!--{if $val.use_flg == "0"}--> bgcolor="grey"<!--{/if}-->><!--{$val.priority}--></td>
                <td<!--{if $val.use_flg == "0"}--> bgcolor="grey"<!--{/if}-->><!--{$val.plugin_name}--></td>
                <td<!--{if $val.use_flg == "0"}--> bgcolor="grey"<!--{/if}-->>
                <!--{html_radios name="plugin_hookpoint_use[`$val.plugin_hookpoint_id`]" options=$arrUse selected=$val.use_flg id=$val.plugin_hookpoint_id}-->
                </td>
            </tr>
    <!--{/foreach}-->
    <!--{/foreach}-->
        </table>
    <!--{else}-->
        <span>登録されているプラグインはありません。</span>
    <!--{/if}-->
</div>
</form>
