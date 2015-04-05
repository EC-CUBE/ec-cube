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
                eccube.setModeAndSubmit(mode, 'plugin_id', plugin_id);
            }
        });

    /**
     * 通信エラー表示.
     */
    function remoteException(XMLHttpRequest, textStatus, errorThrown) {
        alert('通信中にエラーが発生しました。');
    }

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
            eccube.setModeAndSubmit('install','','');
        }
    }

    /**
     * アンインストール(削除)
     */
    function uninstall(plugin_id, plugin_code) {
        if (window.confirm('一度削除したデータは元に戻せません。\nプラグインを削除しても宜しいですか？')){
            eccube.setValue('plugin_id', plugin_id);
            eccube.setModeAndSubmit('uninstall', 'plugin_code', plugin_code);
        }
    }

    /**
     * アップデート処理
     */
    function update(plugin_id, plugin_code) {
        if (window.confirm('プラグインをアップデートしても宜しいですか？')){
            removeUpdateFile('update_file_' + plugin_id);
            eccube.setValue('plugin_id', plugin_id);
            eccube.setModeAndSubmit('update','plugin_code', plugin_code);
        }
    }

    /**
     * 優先度変更.
     */
    function update_priority(plugin_id, plugin_code) {
        var priority = $("*[name=priority_" + plugin_code +"]").val();
        eccube.setValue('priority', priority);
        eccube.setModeAndSubmit('priority','plugin_id',plugin_id);
    }

//]]></script>

<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
    <input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
    <input type="hidden" name="mode" value="" />
    <input type="hidden" name="plugin_id" value="" />
    <input type="hidden" name="plugin_code" value="" />
    <input type="hidden" name="priority" value="" />
    <div id="system" class="contents-main">
        <h2>プラグイン登録</h2>
        <table class="form">
            <tr>
                <th>プラグイン<span class="attention"> *</span></th>
                <td>
                    <!--{assign var=key value="plugin_file"}-->
                    <span class="attention"><!--{$arrErr[$key]}--></span>
                    <input type="file" name="<!--{ $key }-->" class="box45" size="43"  style="<!--{$arrErr[$key]|sfGetErrorColor}--> <!--{if $arrErr[$key]}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->" />
                    <a class="btn-action" href="javascript:;" onclick="install(); return false;"><span class="btn-next">インストール</span></a>
                </td>
            </tr>
        </table>

        <!--▼プラグイン一覧ここから-->
        <h2>プラグイン一覧</h2>
        <!--{if count($plugins) > 0}-->
            <span class="attention"><!--{$arrErr.plugin_error}--></span>
            <table class="system-plugin" width="900">
                <col width="10%" />
                <col width="77" />
                <col width="13%" />
                <tr>
                    <th colspan="2">機能説明</th>
                    <th>優先度</th>
                </tr>
                <!--{section name=data loop=$plugins}-->
                <!--{assign var=plugin value=$plugins[data]}-->
                <tr <!--{if $plugin.enable == $smarty.const.PLUGIN_ENABLE_FALSE}--> style="background:#C9C9C9;" <!--{/if}-->>
                    <!--ロゴ-->
                    <td class="center plugin_img">
                        <!--{if $plugin.plugin_site_url != '' }-->
                            <a href="?" onclick="eccube.openWindow('<!--{$plugin.plugin_site_url|h}-->','plugin_site_url','620','760',{menubar:'no'}); return false;"><img src="<!--{$plugin.logo}-->" width="65" height="65" /></a>&nbsp;
                        <!--{else}-->
                            <img src="<!--{$plugin.logo}-->" width="65" height="65"/>
                        <!--{/if}-->

                    </td>
                    <!--機能説明-->
                    <td class="plugin_info">
                            <!-- プラグイン名 -->
                                <!-- ▼plugin_site_urlが設定されている場合はリンクとして表示 -->
                                <span class="plugin_name">
                                <!--{if $plugin.plugin_site_url != '' }-->
                                    <a href="?" onclick="eccube.openWindow('<!--{$plugin.plugin_site_url|h}-->','plugin_site_url','620','760',{menubar:'no'}); return false;"><!--{$plugin.plugin_name|default:$plugin.plugin_code|h}--></a>&nbsp;
                                <!--{else}-->
                                    <span><!--{$plugin.plugin_name|default:$plugin.plugin_code|h}-->&nbsp;</span>
                                <!--{/if}-->
                                </span>
                            <!-- プラグインバージョン -->
                                <!--{if $plugin.plugin_version != ''}--><!--{$plugin.plugin_version|h}--><!--{/if}-->&nbsp;
                            <!-- 作者 -->
                                <!--{if $plugin.author != ''}-->
                                    <!-- ▼author_site_urlが設定されている場合はリンクとして表示 -->
                                    <!--{if $plugin.author_site_url != '' }-->
                                        <span>(by <a href="?" onclick="eccube.openWindow('<!--{$plugin.author_site_url|h}-->','author_site_url','620','760',{menubar:'no'}); return false;"><!--{$plugin.author|default:'-'|h}--></a>)</span>
                                    <!--{else}-->
                                        <span>(by <!--{$plugin.author|default:'-'|h}-->)</span>
                                    <!--{/if}-->
                                <!--{/if}-->
                            <br />
                            <!-- 説明 -->
                                <p class="description"><!--{$plugin.plugin_description|default:'-'|h}--></p>
                            <div>
                                <span class="ec_cube_version">対応EC-CUBEバージョン ：<!--{$plugin.compliant_version|default:'-'|h}--></span><br/>
                                <span class="attention"><!--{$arrErr[$plugin.plugin_code]}--></span>
                                <!-- 設定 -->
                                    <!--{if $plugin.config_flg == true && $plugin.status != $smarty.const.PLUGIN_STATUS_UPLOADED}-->
                                        <a href="?" onclick="eccube.openWindow('../load_plugin_config.php?plugin_id=<!--{$plugin.plugin_id}-->', 'load', 615, 400);return false;">プラグイン設定</a>&nbsp;|&nbsp;
                                    <!--{else}-->
                                        <span>プラグイン設定&nbsp;|&nbsp;</span>
                                    <!--{/if}-->
                                <!-- アップデート -->
                                    <a class="update_link" href="javascript:;" name="<!--{$plugin.plugin_id}-->">アップデート</a>&nbsp;|&nbsp;
                                <!-- 削除 -->
                                    <a  href="javascript:;" name="uninstall" onclick="uninstall(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;">削除</a>&nbsp;|&nbsp;
                                <!-- 有効/無効 -->
                                    <!--{if $plugin.enable == $smarty.const.PLUGIN_ENABLE_TRUE}-->
                                        <label><input id="plugin_enable" type="checkbox" name="disable" value="<!--{$plugin.plugin_id}-->" checked="checked" />有効</label><br/>
                                    <!--{else}-->
                                        <label><input id="plugin_enable" type="checkbox" name="enable" value="<!--{$plugin.plugin_id}-->" />有効にする</label><br/>
                                    <!--{/if}-->

                                    <!-- アップデートリンク押下時に表示する. -->
                                    <div id="plugin_update_<!--{$plugin.plugin_id}-->" style="display: none">
                                        <input id="update_file_<!--{$plugin.plugin_id}-->" name="<!--{$plugin.plugin_code}-->" type="file" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box30" size="30" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> />
                                        <a class="btn-action" href="javascript:;" onclick="update(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;"><span class="btn-next">アップデート</span></a>
                                    </div>
                            </div>
                    </td>
                    <!--優先順位-->
                    <td class="center">
                        <span class="attention"><!--{$arrErr.priority[$plugin.plugin_id]}--></span>
                        <input type="text" class="center" name="priority_<!--{$plugin.plugin_code}-->" value="<!--{$plugin.priority|h}-->" size="1" />
                        <a class="btn-action" href="javascript:;" onclick="update_priority(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;"><span class="btn-next">変更</span></a><br/>
                        <span><!--{$plugin.priority_message}--></span>
                    </td>
                </tr>
                <!--競合アラート-->
                <!--{if $plugin.conflict_message != ""}-->
                <tr>
                    <td class="attention_fookpoint" colspan="3">
                        <p class="attention"><!--{$plugin.conflict_message}--></p>
                    </td>
                </tr>
                <!--{/if}-->
                <!--{/section}-->
            </table>
        <!--{else}-->
            <span>登録されているプラグインはありません。</span>
        <!--{/if}-->
    </div>
</form>
