<table width="678" border="0" cellspacing="1" cellpadding="4" summary=" ">
    <tr bgcolor="#f2f1ec" align="center" class="fs12n">
        <td width="">ﾛｺﾞ</td>
        <td width="">名称</td>
        <td width="">導入ﾊﾞｰｼﾞｮﾝ</td>
        <td width="">ﾀﾞｳﾝﾛｰﾄﾞ</td>
        <td width="">設定</td>
        <td width="">ｽﾃｰﾀｽ</td>
    </tr>
<!--{foreach from=$arrProducts item=product name=products_list_loop}-->
    <tr bgcolor="#ffffff" class="fs12">
        <td align="center">
            <a href="<!--{$smarty.const.OSTORE_URL}-->products/detail.php?product_id=<!--{$product.product_id}-->"
               target="_blank">
                <img src="<!--{$smarty.const.OSTORE_SSLURL}-->upload/save_image/<!--{$product.main_list_image}-->" width="50" height="50">
            </a>
        </td>
        <td align="center">
            <p>
                <a href="<!--{$smarty.const.SITE_URL}-->products/detail.php?product_id=<!--{$product.product_id}-->" target="_blank">
                    <!--{$product.name}-->
                </a>
            </p>
            <p>Version.<!--{$product.version|default:"--"}-->　<!--{$product.last_update_date|sfDispDBDate:false}--></p>
        </td>
        <td align="center"><!--{$product.installed_version|default:"--"}--></td>
        
        <!--{* ダウンロード対象商品 かつ 受注ステータスが「入金済み」なら各種ボタンを表示する *}-->
        <!--{if $product.download_flg}-->
        
        <td align="center">
            <!--{* インストール済み かつ 新バージョンが公開している場合 はアップデートボタン表示 *}-->
            <!--{if $product.installed_flg && $product.version_up_flg}-->
                <span class="icon_confirm">
                <a href="" onclick="OwnersStore.download(<!--{$product.product_id}-->);return false;">アップデート</a>
                </span>
            <!--{* 未インストールならインストールボタン表示 *}-->
            <!--{elseif !$product.installed_flg}-->
                <span class="icon_confirm">
                <a href="" onclick="OwnersStore.download(<!--{$product.product_id}-->);return false;">インストール</a>
                </span>
            <!--{else}-->
                --
            <!--{/if}-->
        </td>
            
        <td align="center">
            <!--{* インストール済みなら設定ボタン表示 *}-->
            <!--{if $product.installed_flg}-->
                <span class="icon_confirm">
                <a href="" onclick="win02('../load_module_config.php?module_id=<!--{$product.product_id}-->', 'load', 600, 400);return false;">
                   設定
                </a>
                </span>
            <!--{else}-->
                --
            <!--{/if}-->
        </td>

        <!--{else}-->
        
        <td align="center">--</td>
        <td align="center">--</td>
        <!--{/if}-->
        
        <td align="center"><!--{$product.status}--></td>
    </tr>
<!--{/foreach}-->
</table>