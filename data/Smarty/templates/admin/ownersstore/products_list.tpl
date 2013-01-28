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

<table id="ownersstore-products-list" class="list center">
    <tr>
        <th><!--{t string="tpl_Logo_01"}--></th>
        <th><!--{t string="tpl_Product name_01"}--></th>
        <th><!--{t string="tpl_Introduction version_01"}--></th>
        <th><!--{t string="tpl_Installation_01"}--></th>
        <th><!--{t string="tpl_Settings_01"}--></th>
        <th><!--{t string="tpl_Purchase status_01"}--></th>
    </tr>
    <!--{foreach from=$arrProducts item=product name=products_list_loop}-->
        <tr>
            <td>
                <a href="<!--{$smarty.const.OSTORE_URL}-->products/detail.php?product_id=<!--{$product.product_id|h}-->" target="_blank">
                    <img src="<!--{$smarty.const.OSTORE_SSLURL}-->resize.php?image=<!--{$product.main_list_image|h}-->&amp;width=50&amp;height=50" /></a>
            </td>
            <td>
                <p>
                    <a href="<!--{$smarty.const.OSTORE_URL}-->products/detail.php?product_id=<!--{$product.product_id|h}-->" target="_blank">
                        <!--{$product.name}--></a>
                </p>
                <p>Version.<!--{$product.version|default:"--"}-->　<!--{$product.last_update_date|sfDispDBDate:false|h}--></p>
            </td>
            <td>
                <div id="ownersstore_version<!--{$product.product_id|h}-->">
                    <!--{$product.installed_version|default:"--"|h}-->
                </div>
            </td>

            <!--{* ダウンロード対象商品なら各種ボタンを表示する *}-->
            <!--{if $product.download_flg}-->

                <td>
                    <div id="ownersstore_download<!--{$product.product_id|h}-->">
                    <!--{* 新バージョンが公開している場合 はアップデートボタン表示 *}-->
                    <!--{if $product.version_up_flg}-->
                        <span class="icon_confirm">
                        <a href="#" onclick="OwnersStore.download(<!--{$product.product_id|h}-->);return false;"><!--{t string="tpl_Update_02"}--></a>
                        </span>
                    <!--{* それ以外ならダウンロードボタン表示 *}-->
                    <!--{else}-->
                        <span class="icon_confirm">
                        <a href="#" onclick="OwnersStore.download(<!--{$product.product_id|h}-->);return false;"><!--{t string="tpl_Download_01"}--></a>
                        </span>
                    <!--{/if}-->
                    </div>
                </td>

                <td>
                    <!--{* インストール済みなら設定ボタン表示 *}-->
                    <!--{if $product.installed_flg}-->
                        <span class="icon_confirm">
                        <a href="#" onclick="win02('../load_module_config.php?module_id=<!--{$product.product_id}-->', 'load', 615, 400);return false;">
                            <!--{t string="tpl_Settings_01"}--></a>
                        </span>
                    <!--{else}-->
                        <div id='ownersstore_settings<!--{$product.product_id|h}-->' style="display:none">
                        <span class="icon_confirm">
                        <a href="#" onclick="win02('../load_module_config.php?module_id=<!--{$product.product_id}-->', 'load', 615, 400);return false;">
                            <!--{t string="tpl_Settings_01"}--></a>
                        </span>
                        </div>
                        <div id='ownersstore_settings_default<!--{$product.product_id|h}-->' style="display:bloc">--</div>
                    <!--{/if}-->
                </td>

            <!--{else}-->

                <td>--</td>
                <td>--</td>
            <!--{/if}-->

            <td><!--{$product.status|h|nl2br}--></td>
        </tr>
    <!--{/foreach}-->
</table>
