<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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
        $('input[id^=plugin_enable]').change(function(event) {
            // モード(有効 or 無効)
            var mode = event.target.name;

            if(mode === 'disable') {
                result = window.confirm('プラグインを無効にしても宜しいですか？');
                if(result === false) {
                    $(event.target).attr("checked", "checked");
                }
            } else if(mode === 'enable') {
                result = window.confirm('プラグインを有効にしても宜しいですか？');
                if(result === false) {
                    $(event.target).attr("checked", "");
                }
            }
            if(result === true){
                // プラグインID
                var plugin_id = event.target.value;
                fnModeSubmit(mode, 'plugin_id', plugin_id);
            }
        });

    /**
     * アップデートリンク押下時の処理.
     */
    $('.update_link').click(function(event) {
        var plugin_id = event.target.name;
        $('div[id="plugin_update_' + plugin_id + '"]').toggle("slow");
        });
    });

    /**
     * アプデートボタン押下時の処理.
     * アップデート対象ファイル以外はPOSTされない様にdisabled属性を付与
     */
    function removeUpdateFile(select_id) {
        $('input[name="update_plugin_file"]').attr("disabled", "disabled");
        $('input[id="' + select_id + '"]').removeAttr("disabled");
    }

    /**
     * インストール
     */
    function install() {
        if (window.confirm('プラグインをインストールしても宜しいでしょうか？')){
            fnModeSubmit('install','','');
        }
    }

    /**
     * アンインストール(削除)
     */
    function uninstall(plugin_id, plugin_code) {
        if (window.confirm('一度削除したデータは元に戻せません。\nプラグインを削除しても宜しいですか？')){
            fnSetFormValue('plugin_id', plugin_id);
            fnModeSubmit('uninstall', 'plugin_code', plugin_code);
        }
    }

    /**
     * アップデート処理
     */
    function update(plugin_id, plugin_code) {
        if (window.confirm('プラグインをアップデートしても宜しいですか？')){
            removeUpdateFile('update_file_' + plugin_id);
            fnSetFormValue('plugin_id', plugin_id);
            fnModeSubmit('update','plugin_code', plugin_code);
        }
    }


    /**
     * 優先度変更.
     */
    function update_priority(plugin_id, plugin_code) {
        var priority = $("*[name=priority_" + plugin_code +"]").val();
        fnSetFormValue('priority', priority);
        fnModeSubmit('priority','plugin_id',plugin_id);
    }

//]]></script>

<!--<form name="form1" id="form1" method="post" action="?">-->
<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="plugin_id" value="" />
<input type="hidden" name="plugin_code" value="" />
<input type="hidden" name="priority" value="" />
<div id="system" class="contents-main">
    <h2>プラグイン競合チェック</h2>

    <!--▼プラグイン一覧ここから-->
    <h2>フックポイント別プラグイン一覧</h2>
    <!--{if count($arrHookPoint) > 0}-->

        <span class="attention"><!--{$arrErr.plugin_error}--></span>
        <table class="system-plugin" width="900">
            <col width="10%" />
            <col width="77" />
            <col width="13%" />
            <tr>
                <th>フックポイント</th>
                <th>優先度</th>
                <th>プラグイン名</th>
                <th>利用ON/OFF</th>
            </tr>
    <!--{foreach from=$arrHookPoint item=hookpoint}-->
    <!--{foreach from=$hookpoint item=val}-->


            <tr>
                <td><!--{$val.hook_point}--></td>
                <td><!--{$val.priority}--></td>
                <td><!--{$val.plugin_name}--></td>
                <td>
plugin_hookpoint_id:<!--{$val.plugin_id}-->, plugin_id:<!--{$val.plugin_id}-->
use_flg:<!--{$val.use_flg}--><br />
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
