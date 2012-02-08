<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2011 LOCKON CO.,LTD. All Rights Reserved.
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

        $('input[id^=plugin_enable]').change(function(event) {
            var data = {};
            
            // モード(有効 or 無効)
            data.mode = event.target.name;
            // プラグインID
            data.plugin_id = event.target.value;
            data['<!--{$smarty.const.TRANSACTION_ID_NAME}-->'] = '<!--{$transactionid}-->';
            $.ajax({
                type : 'POST',
                url : location.pathname,
                dataType : "json",
                data: data,
                cache : false,
                error : remoteException,
                success : function(data, dataType) {
                        alert(data.message);
                        location.href = location.pathname;
                }
            });
        });

        /**
         * 通信エラー表示.
         */
        function remoteException(XMLHttpRequest, textStatus, errorThrown) {
            alert('通信中にエラーが発生しました。');
        }

    $('.update_link').click(function(event) {
        var plugin_id = event.target.name;
        $('div[id="plugin_update_' + plugin_id + '"]').toggle("slow");
        });
    });


    function removeUpdateFile(select_id) {
        $('input[name="update_plugin_file"]').attr("disabled", "disabled");
        $('input[id="' + select_id + '"]').removeAttr("disabled");
    }
//]]>
</script>

<!--<form name="form1" id="form1" method="post" action="?">-->
<form name="form1" method="post" action="?" enctype="multipart/form-data">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="" />
<input type="hidden" name="plugin_id" value="" />
<input type="hidden" name="plugin_code" value="" />

<div id="system" class="contents-main">
    <h2>プラグイン登録</h2>
    <table class="form">
        <tr>
            <th>プラグイン<span class="attention"> *</span></th>
            <td>
                <!--{assign var=key value="plugin_file"}-->
                <span class="attention"><!--{$arrErr[$key]}--></span>
                <input type="file" name="<!--{ $key }-->" class="box45" size="43"  style="<!--{$arrErr[$key]|sfGetErrorColor}--> <!--{if $arrErr[$key]}--> background-color:<!--{$smarty.const.ERR_COLOR|h}--><!--{/if}-->">
                <a class="btn-action" href="javascript:;" onclick="fnModeSubmit('install', '', '');return false;"><span class="btn-next">インストール</span></a>
            </td>
        </tr>
    </table>

    <!--▼プラグイン一覧ここから-->
    <h2>プラグイン一覧</h2>
    <!--{if count($plugins) > 0}-->
        <span class="attention"><!--{$arrErr.plugin_error}--><!--{$arrErr.update_plugin_file}--></span>
        <table class="system-plugin" width="900">
            <col width="10%" />
            <col width="80%" />
            <col width="10%" />
            <tr>
                <th colspan="2">機能説明</th>
                <th>優先度<a class="btn-action" href="javascript:;" onclick="fnModeSubmit('priority','','');return false;"><span class="btn-next">反映</span></a></th>
            </tr>
            <!--{section name=data loop=$plugins}-->
            <tr <!--{if $plugins[data].enable == $smarty.const.PLUGIN_ENABLE_FALSE}--> style="background:#C9C9C9;" <!--{/if}-->>
                <!--ロゴ-->
                <td class="center plugin_img">
                    <!--{if $plugins[data].plugin_site_url != '' }-->
                        <a href="?" onclick="win03('<!--{$plugins[data].plugin_site_url|h}-->','plugin_site_url','620','760'); return false;"><img src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/<!--{$plugins[data].plugin_code}-->/logo.png"/></a>&nbsp;
                    <!--{else}-->
                        <img src="<!--{$smarty.const.ROOT_URLPATH}-->plugin/<!--{$plugins[data].plugin_code}-->/logo.png"/>
                    <!--{/if}-->

                </td>
                <!--機能説明-->
                <td class="plugin_info">
                        <!-- プラグイン名 -->
                            <!-- ▼plugin_site_urlが設定されている場合はリンクとして表示 -->
                            <span class="plugin_name">
                            <!--{if $plugins[data].plugin_site_url != '' }-->
                                <a href="?" onclick="win03('<!--{$plugins[data].plugin_site_url|h}-->','plugin_site_url','620','760'); return false;"><!--{$plugins[data].plugin_name|default:$plugins[data].plugin_code|h}--></a>&nbsp;
                            <!--{else}-->
                                <sapn><!--{$plugins[data].plugin_name|default:$plugins[data].plugin_code|h}-->&nbsp;</sapn>
                            <!--{/if}-->
                            </span>
                        <!-- プラグインバージョン -->
                            <!--{if $plugins[data].plugin_version != ''}--><!--{$plugins[data].plugin_version|h}--><!--{/if}-->&nbsp;
                        <!-- 作者 -->
                            <!--{if $plugins[data].author != ''}-->
                                <!-- ▼author_site_urlが設定されている場合はリンクとして表示 -->
                                <!--{if $plugins[data].author_site_url != '' }-->
                                    <span>(by <a href="?" onclick="win03('<!--{$plugins[data].author_site_url|h}-->','author_site_url','620','760'); return false;"><!--{$plugins[data].author|default:'-'|h}--></a>)</span>
                                <!--{else}-->
                                    <span>(by <!--{$plugins[data].author|default:'-'|h}-->)</span>
                                <!--{/if}-->
                            <!--{/if}-->
                        <br />
                        <!-- 説明 -->
                            <p class="description"><!--{$plugins[data].plugin_description|default:'-'|h}--></p>
                        <div>
                            <span class="ec_cube_version">対応EC-CUBEバージョン ：<!--{$plugins[data].compliant_version|default:'-'|h}--></span><br/>
                            <!-- 設定 -->
                                <!--{if $plugins[data].config_flg == true && $plugins[data].status != $smarty.const.PLUGIN_STATUS_UPLOADED}-->
                                    <a href="?" onclick="win03('<!--{$smarty.const.ROOT_URLPATH}-->plugin/<!--{$plugins[data].plugin_code}-->/config.php','plugin_setting','620','760'); return false;">プラグイン設定</a>&nbsp;|&nbsp;
                                <!--{else}-->
                                    <span>プラグイン設定&nbsp;|&nbsp;</span>
                                <!--{/if}-->
                            <!-- アップデート -->
                                <a class="update_link" href="#" name="<!--{$plugins[data].plugin_id}-->">アップデート</a>&nbsp;|&nbsp;
                            <!-- 削除 -->
                                <a  href="javascript:;" name="uninstall" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('uninstall','plugin_code','<!--{$plugins[data].plugin_code}-->'); return false;">削除</a>&nbsp;|&nbsp;
                            <!-- 有効/無効 -->
                                <!--{if $plugins[data].enable == $smarty.const.PLUGIN_ENABLE_TRUE}-->
                                    <input id="plugin_enable" type="checkbox" name="disable" value="<!--{$plugins[data].plugin_id}-->" id="login_memory" checked="checked">有効</input><br/>
                                <!--{else}-->
                                    <input id="plugin_enable" type="checkbox" name="enable" value="<!--{$plugins[data].plugin_id}-->" id="login_memory" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); return false;">有効にする</input><br/>
                                <!--{/if}-->

                                <!-- アップデートリンク押下時に表示する. -->
                                <div id="plugin_update_<!--{$plugins[data].plugin_id}-->" style="display: none">                                
                                    <input id="update_file_<!--{$plugins[data].plugin_id}-->" name="update_plugin_file" type="file" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box30" size="30" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}--> />
                                    <a class="btn-action" href="javascript:;" onclick="removeUpdateFile('update_file_<!--{$plugins[data].plugin_id}-->'); fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('update','plugin_code','<!--{$plugins[data].plugin_code}-->');return false;"><span class="btn-next">アップデート</span></a>
                                </div>
                        </div>
                </td>
                <!--優先順位-->
                <!--{assign var=key value="rank"}-->
                <td class="center">
                    <input type="text" name="priority[<!--{$plugins[data].plugin_id}-->]" value="<!--{$plugins[data].rank|h}-->" size="1" class="rank" /><br/>
                </td>
            </tr>
            <!--競合アラート-->
            <!--{if $plugins[data].conflict_message != ""}-->
            <tr> 
                <td class="attention_fookpoint" colspan="3">
                    <p class="attention"><!--{$plugins[data].conflict_message}--></p>
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
