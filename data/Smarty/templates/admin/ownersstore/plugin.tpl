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

<script type="text/javascript">//<![CDATA[
    $(function() {

        /**
         * 「有効/有効にする」チェックボタン押下時
         */
        $('input[id^=plugin_enable]').change(function(event) {
            // モード(有効 or 無効)
            var mode = event.target.name;

            if(mode === 'disable') {
                result = window.confirm('<!--{t string="tpl_Do you want to void the plug-in?_01" escape="j"}-->');
                if(result === false) {
                    $(event.target).attr("checked", "checked");
                }
            } else if(mode === 'enable') {
                result = window.confirm('<!--{t string="tpl_Do you want to activate the plug-in?_01" escape="j"}-->');
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
     * 通信エラー表示.
     */
    function remoteException(XMLHttpRequest, textStatus, errorThrown) {
        alert('<!--{t string="tpl_An error occurred during transmission._01" escape="j"}-->');
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
        if (window.confirm('<!--{t string="tpl_Do you want to install the plug-in?_01" escape="j"}-->')){
            fnModeSubmit('install','','');
        }
    }

    /**
     * アンインストール(削除)
     */
    function uninstall(plugin_id, plugin_code) {
        if (window.confirm('<!--{t string="tpl_Data that has been erased cannot be restored.Do you want to delete the plug-in?_01" escape="j"}-->')){
            fnSetFormValue('plugin_id', plugin_id);
            fnModeSubmit('uninstall', 'plugin_code', plugin_code);
        }
    }

    /**
     * アップデート処理
     */
    function update(plugin_id, plugin_code) {
        if (window.confirm('<!--{t string="tpl_Do you want to update the plug-in?_01" escape="j"}-->')){
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
    <h2><!--{t string="tpl_Plug-in registration_01"}--></h2>
    <table class="form">
        <tr>
            <th><!--{t string="tpl_Plug-in<span class='attention'> *</span>_01" escape="none"}--></th>
            <td>
                <!--{assign var=key value="plugin_file"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="file" name="<!--{ $key }-->" class="box45" size="43"  style="<!--{$arrErr[$key]|sfGetErrorColor}--> <!--{if $arrErr[$key]}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->">
                <a class="btn-action" href="javascript:;" onclick="install(); return false;"><span class="btn-next"><!--{t string="tpl_Install_01"}--></span></a>
            </td>
        </tr>
    </table>

    <!--▼プラグイン一覧ここから-->
    <h2><!--{t string="tpl_Plug-in list_01"}--></h2>
    <!--{if count($plugins) > 0}-->
        <span class="attention"><!--{$arrErr.plugin_error}--></span>
        <table class="system-plugin" width="900">
            <col width="10%" />
            <col width="77" />
            <col width="13%" />
            <tr>
                <th colspan="2"><!--{t string="tpl_Explanation of functions_01"}--></th>
                <th><!--{t string="tpl_Priority_01"}--></th>
            </tr>
            <!--{section name=data loop=$plugins}-->
            <!--{assign var=plugin value=$plugins[data]}-->
            <tr <!--{if $plugin.enable == $smarty.const.PLUGIN_ENABLE_FALSE}--> style="background:#C9C9C9;" <!--{/if}-->>
                <!--ロゴ-->
                <td class="center plugin_img">
                    <!--{if $plugin.plugin_site_url != '' }-->
                        <a href="?" onclick="win03('<!--{$plugin.plugin_site_url|h}-->','plugin_site_url','620','760'); return false;"><img src="<!--{$plugin.logo}-->" width="65" height="65"/></a>&nbsp;
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
                                <a href="?" onclick="win03('<!--{$plugin.plugin_site_url|h}-->','plugin_site_url','620','760'); return false;"><!--{$plugin.plugin_name|default:$plugin.plugin_code|h}--></a>&nbsp;
                            <!--{else}-->
                                <sapn><!--{$plugin.plugin_name|default:$plugin.plugin_code|h}-->&nbsp;</sapn>
                            <!--{/if}-->
                            </span>
                        <!-- プラグインバージョン -->
                            <!--{if $plugin.plugin_version != ''}--><!--{$plugin.plugin_version|h}--><!--{/if}-->&nbsp;
                        <!-- 作者 -->
                            <!--{if $plugin.author != ''}-->
                                <!-- ▼author_site_urlが設定されている場合はリンクとして表示 -->
                                <!--{if $plugin.author_site_url != '' }-->
                                    <span>(by <a href="?" onclick="win03('<!--{$plugin.author_site_url|h}-->','author_site_url','620','760'); return false;"><!--{$plugin.author|default:'-'|h}--></a>)</span>
                                <!--{else}-->
                                    <span>(by <!--{$plugin.author|default:'-'|h}-->)</span>
                                <!--{/if}-->
                            <!--{/if}-->
                        <br />
                        <!-- 説明 -->
                            <p class="description"><!--{$plugin.plugin_description|default:'-'|h}--></p>
                        <div>
                            <span class="ec_cube_version"><!--{t string="tpl_Compatible EC-CUBE version :_01"}--><!--{$plugin.compliant_version|default:'-'|h}--></span><br/>
                            <span class="attention"><!--{$arrErr[$plugin.plugin_code]}--></span>
                            <!-- 設定 -->
                                <!--{if $plugin.config_flg == true && $plugin.status != $smarty.const.PLUGIN_STATUS_UPLOADED}-->
                                    <a href="?" onclick="win02('../load_plugin_config.php?plugin_id=<!--{$plugin.plugin_id}-->', 'load', 615, 400);return false;"><!--{t string="tpl_Plug-in settings_01"}--></a>&nbsp;|&nbsp;
                                <!--{else}-->
                                    <span><!--{t string="tpl_Plug-in settings_01"}-->&nbsp;|&nbsp;</span>
                                <!--{/if}-->
                            <!-- アップデート -->
                                <a class="update_link" href="javascript:;" name="<!--{$plugin.plugin_id}-->"><!--{t string="tpl_Update_02"}--></a>&nbsp;|&nbsp;
                            <!-- 削除 -->
                                <a  href="javascript:;" name="uninstall" onclick="uninstall(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;"><!--{t string="tpl_Remove_01"}--></a>&nbsp;|&nbsp;
                            <!-- 有効/無効 -->
                                <!--{if $plugin.enable == $smarty.const.PLUGIN_ENABLE_TRUE}-->
                                    <label><input id="plugin_enable" type="checkbox" name="disable" value="<!--{$plugin.plugin_id}-->" id="login_memory" checked="checked"><!--{t string="tpl_Enabled_01"}--></input></label><br/>
                                <!--{else}-->
                                    <label><input id="plugin_enable" type="checkbox" name="enable" value="<!--{$plugin.plugin_id}-->" id="login_memory"><!--{t string="tpl_Enable_01"}--></input></label><br/>
                                <!--{/if}-->

                                <!-- アップデートリンク押下時に表示する. -->
                                <div id="plugin_update_<!--{$plugin.plugin_id}-->" style="display: none">
                                    <input id="update_file_<!--{$plugin.plugin_id}-->" name="<!--{$plugin.plugin_code}-->" type="file" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box30" size="30" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> />
                                    <a class="btn-action" href="javascript:;" onclick="update(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;"><span class="btn-next"><!--{t string="tpl_Update_02"}--></span></a>
                                </div>
                        </div>
                </td>
                <!--優先順位-->
                <td class="center">
                    <span class="attention"><!--{$arrErr.priority[$plugin.plugin_id]}--></span>
                    <input type="text" class="center" name="priority_<!--{$plugin.plugin_code}-->" value="<!--{$plugin.priority|h}-->" size="1" class="priority" />
                    <a class="btn-action" href="javascript:;" onclick="update_priority(<!--{$plugin.plugin_id}-->, '<!--{$plugin.plugin_code}-->'); return false;"><span class="btn-next"><!--{t string="tpl_Change_01"}--></span></a><br/>
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
        <span><!--{t string="tpl_There are no plug-ins registered._01"}--></span>
    <!--{/if}-->
</div>
</form>
