<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<body onload="preLoadImg('<!--{$smarty.const.URL_DIR}-->'); <!--{$tpl_onload}-->">
<noscript>
  <p>JavaScript を有効にしてご利用下さい.</p>
</noscript>

<a name="top" id="top"></a>

<!--{* ▼HEADER *}-->
<!--{if $arrPageLayout.header_chk != 2}-->
<!--{assign var=header_dir value="`$smarty.const.TEMPLATE_DIR`header.tpl"}-->
<!--{include file= $header_dir}-->
<!--{/if}-->
<!--{* ▲HEADER *}-->

<!--{* ▼CONTENTS *}-->
<div id="container">

  <!--{if $tpl_column_num > 1}-->
  <!--{* ▼LEFT COLUMN *}-->
  <div id="leftcolumn">

    <!--{* ▼左ナビ *}-->
    <!--{if $arrPageLayout.LeftNavi|@count > 0}-->
      <!--{foreach key=LeftNaviKey item=LeftNaviItem from=$arrPageLayout.LeftNavi}-->
        <!-- ▼<!--{$LeftNaviItem.bloc_name}--> ここから-->
          <!--{if $LeftNaviItem.php_path != ""}-->
            <!--{include_php file=$LeftNaviItem.php_path}-->
          <!--{else}-->
            <!--{include file=$LeftNaviItem.tpl_path}-->
          <!--{/if}-->
        <!-- ▲<!--{$LeftNaviItem.bloc_name}--> ここまで-->
      <!--{/foreach}-->
    <!--{/if}-->
    <!--{* ▲左ナビ *}-->
  </div>
  <!--{* ▲LEFT COLUMN *}-->
  <!--{/if}-->

  <!--{if $tpl_column_num == 3}-->
  <!--{* ▼CENTER COLUMN *}-->
  <div id="centercolumn">
  <!--{/if}-->

    <!--{* ▼メイン上部 *}-->
    <!--{if $arrPageLayout.MainHead|@count > 0}-->
      <!--{foreach key=MainHeadKey item=MainHeadItem from=$arrPageLayout.MainHead}-->
        <!-- ▼<!--{$MainHeadItem.bloc_name}--> ここから-->
        <!--{if $MainHeadItem.php_path != ""}-->
          <!--{include_php file=$MainHeadItem.php_path}-->
        <!--{else}-->
          <!--{include file=$MainHeadItem.tpl_path}-->
        <!--{/if}-->
        <!-- ▲<!--{$MainHeadItem.bloc_name}--> ここまで-->
      <!--{/foreach}-->
    <!--{/if}-->
    <!--{* ▲メイン上部 *}-->

    <!--{include file=$tpl_mainpage}-->

    <!--{* ▼メイン下部 *}-->
    <!--{if $arrPageLayout.MainFoot|@count > 0}-->
      <!--{foreach key=MainFootKey item=MainFootItem from=$arrPageLayout.MainFoot}-->
        <!-- ▼<!--{$MainFootItem.bloc_name}--> ここから-->
        <!--{if $MainFootItem.php_path != ""}-->
          <!--{include_php file=$MainFootItem.php_path}-->
        <!--{else}-->
          <!--{include file=$MainFootItem.tpl_path}-->
        <!--{/if}-->
        <!-- ▲<!--{$MainFootItem.bloc_name}--> ここまで-->
      <!--{/foreach}-->
    <!--{/if}-->
    <!--{* ▲メイン下部 *}-->

  <!--{if $tpl_column_num == 3}-->
  </div>
  <!--{* ▲CENTER COLUMN *}-->

  <!--{* ▼RIGHT COLUMN *}-->
  <div id="rightcolumn">
    <!--{* ▼右ナビ *}-->
    <!--{if $arrPageLayout.RightNavi|@count > 0}-->
      <!--{foreach key=RightNaviKey item=RightNaviItem from=$arrPageLayout.RightNavi}-->
        <!-- ▼<!--{$RightNaviItem.bloc_name}--> ここから-->
        <!--{if $RightNaviItem.php_path != ""}-->
          <!--{include_php file=$RightNaviItem.php_path}-->
        <!--{else}-->
          <!--{include file=$RightNaviItem.tpl_path}-->
        <!--{/if}-->
        <!-- ▲<!--{$RightNaviItem.bloc_name}--> ここまで-->
      <!--{/foreach}-->
    <!--{/if}-->
    <!--{* ▲右ナビ *}-->
  </div>
  <!--{* ▲RIGHT COLUMN *}-->
  <!--{/if}-->

</div>
<!--{* ▲CONTENTS *}-->

<!--{* ▼FOTTER *}-->
<!--{if $arrPageLayout.footer_chk != 2}-->
<!--{include file="`$smarty.const.TEMPLATE_DIR`footer.tpl"}-->
<!--{/if}-->
<!--{* ▲FOTTER *}-->

<!--{* EBiSタグ表示用 *}-->
<!--{$tpl_mainpage|sfPrintEbisTag}-->
<!--{* アフィリエイトタグ表示用 *}-->
<!--{$tpl_conv_page|sfPrintAffTag:$tpl_aff_option}-->
</body>
