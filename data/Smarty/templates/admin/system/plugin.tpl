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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
*}-->
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
                <input type="file" name="<!--{$key}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" class="box54" size="64" <!--{if $arrErr[$key]}-->style="background-color:<!--{$smarty.const.ERR_COLOR|h}-->"<!--{/if}-->>
            </td>
        </tr>
    </table>

    <div class="btn-area">
        <a class="btn-action" href="javascript:;" onclick="fnModeSubmit('upload', '', '');return false;"><span class="btn-next">この内容で登録する</span></a>
    </div>

    <!--▼プラグイン一覧ここから-->
    <h2>プラグイン一覧</h2>
    <!--{if count($plugins) > 0}-->
        <span class="attention"><!--{$arrErr.plugin_error}--><!--{$arrErr.mode}--><!--{$arrErr.plugin_id}--><!--{$arrErr.plugin_code}--></span>
        <table class="list" width="900">
            <colgroup width="15%">
            <colgroup width="10%">
            <colgroup width="25%">
            <colgroup width="28%">
            <colgroup width="7%">
            <colgroup width="10%">
            <colgroup width="5%">
            <tr>
                <th >プラグイン名</th>
                <th>作者</th>
                <th>サイトURL</th>
                <th>説明</th>
                <th>ステータス</th>
                <th>操作</th>
                <th>設定</th>
            </tr>
            <!--{section name=data loop=$plugins}-->
            <tr>
                <td><!--{$plugins[data].plugin_name|default:$plugins[data].plugin_code|h}--><!--{if $plugins[data].plugin_version != ''}--><br /><!--{$plugins[data].plugin_version|h}--><!--{/if}--></td>
                <td><!--{$plugins[data].author|default:'-'|h}--></td>
                <td><!--{$plugins[data].plugin_site_url|default:'-'|h}--></td>
                <td><!--{$plugins[data].plugin_description|default:'-'|h}--></td>
                <td class="center">
                    <!--{if $plugins[data].enable == $smarty.const.PLUGIN_ENABLE_TRUE}-->
                    有効
                    <!--{elseif $plugins[data].enable == $smarty.const.PLUGIN_ENABLE_FALSE}-->
                    無効
                    <!--{else}-->-<!--{/if}-->
                </td>
                <td class="center">
                    <!--{if $plugins[data].status == $smarty.const.PLUGIN_STATUS_UPLOADED}-->
                    <a class="btn-normal" href="javascript:;" name="install" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('install','plugin_code','<!--{$plugins[data].plugin_code}-->'); return false;">install</a>
                    <!--{else}-->
                        <!--{if $plugins[data].enable == $smarty.const.PLUGIN_ENABLE_TRUE}-->
                            <a class="btn-normal" href="javascript:;" name="disable" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('disable','plugin_code','<!--{$plugins[data].plugin_code}-->'); return false;">disable</a><br />
                        <!--{else}-->
                            <a class="btn-normal" href="javascript:;" name="enable" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('enable','plugin_code','<!--{$plugins[data].plugin_code}-->'); return false;">enable</a><br />
                        <!--{/if}-->
                            <a class="btn-normal" href="javascript:;" name="uninstall" onclick="fnSetFormValue('plugin_id', '<!--{$plugins[data].plugin_id}-->'); fnModeSubmit('uninstall','plugin_code','<!--{$plugins[data].plugin_code}-->'); return false;">uninstall</a>
                    <!--{/if}-->
                </td>
                <td class="center">
                    <!--{if $plugins[data].plugin_setting_path != ''}-->
                        <a href="?" onclick="win03('<!--{$plugins[data].plugin_setting_path}-->','plugin_setting','620','760'); return false;">設定</a>
                    <!--{else}-->
                        -
                    <!--{/if}-->
                </td>

            </tr>
            <!--{/section}-->
        </table>
    <!--{else}-->
        登録されているプラグインはありません。
    <!--{/if}-->

</div>
</form>
