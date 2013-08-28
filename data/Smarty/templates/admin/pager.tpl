<!--{* ★ ページャここから ★ *}-->
<div class="pager">
    <ul>
    <!--{foreach from=$arrPagenavi.arrPageno key="key" item="item"}-->
        <li<!--{if $arrPagenavi.now_page == $item}--> class="on"<!--{/if}-->><a href="#" onclick="eccube.moveNaviPage(<!--{$item}-->, '<!--{$arrPagenavi.mode}-->'); return false;"><span><!--{$item}--></span></a></li>
    <!--{/foreach}-->
    </ul>
</div>
<!--{* ★ ページャここまで ★ *}-->
