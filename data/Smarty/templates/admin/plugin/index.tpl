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
<form name="form1" id="form1" method="post" action="">
    <div class="contents-main">
        <h2>インストール済みのプラグイン</h2>
        <!--{foreach from="$arrInstalledPlugin" item="plugin" key="path" name="installedPlugin"}-->
            <!--{if $smarty.foreach.installedPlugin.first}-->
                <table>
                    <tr>
                        <th>プラグイン名</th>
                        <th>ディレクトリ名</th>
                        <th>バージョン</th>
                        <th>著作者</th>
                        <th>実行</th>
                    </tr>
            <!--{/if}-->
            <tr>
                <td><!--{$plugin.info.name|escape}--></td>
                <td><!--{$plugin.path|escape}--></td>
                <td><!--{$plugin.info.version|escape}--></td>
                <td><!--{$plugin.info.auther|escape}--></td>
                <td><a href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.ADMIN_DIR}-->plugin/uninstall.php?path=<!--{$plugin.path|escape}-->">アンインストール</a></td>
            </tr>
            <!--{if $smarty.foreach.installedPlugin.last}-->
                </table>
            <!--{/if}-->
        <!--{foreachelse}-->
            該当するプラグインはありません。
        <!--{/foreach}-->

        <h2>インストールされていないプラグイン</h2>
        <!--{foreach from="$arrInstallablePlugin" item="plugin" key="path" name="installablePlugin"}-->
            <!--{if $smarty.foreach.installablePlugin.first}-->
                <table>
                    <tr>
                        <th>プラグイン名</th>
                        <th>ディレクトリ名</th>
                        <th>バージョン</th>
                        <th>著作者</th>
                        <th>実行</th>
                    </tr>
            <!--{/if}-->
            <tr>
                <td><!--{$plugin.info.name|escape}--></td>
                <td><!--{$plugin.path|escape}--></td>
                <td><!--{$plugin.info.version|escape}--></td>
                <td><!--{$plugin.info.auther|escape}--></td>
                <td><a href="<!--{$smarty.const.URL_DIR}--><!--{$smarty.const.ADMIN_DIR}-->plugin/install.php?path=<!--{$plugin.path|escape}-->">インストール</a></td>
            </tr>
            <!--{if $smarty.foreach.installablePlugin.last}-->
                </table>
            <!--{/if}-->
        <!--{foreachelse}-->
            該当するプラグインはありません。
        <!--{/foreach}-->
    </div>
</form>
