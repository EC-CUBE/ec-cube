<!--{*
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
 *}-->
<!--{assign var=mypage value="`$smarty.const.ROOT_URLPATH`mypage/index.php"}-->
<!--{if $smarty.server.PHP_SELF != $mypage}-->

<!--{if $arrNews}-->
<div id="block-news" class="block-center">
<div class="create-box">

<!--{section name=data loop=$arrNews max=3}-->
    <div class="anews">
        <span><!--{$arrNews[data].news_date_disp|date_format:"%m&frasl;%d"}--></span>&nbsp;
        <!--{if $arrNews[data].news_url}--><a href="<!--{$arrNews[data].news_url|h}-->"><!--{/if}--><!--{$arrNews[data].news_title|h}--><!--{if $arrNews[data].news_url}--></a><!--{/if}-->
    </div>
<!--{/section}-->

</div>
</div>

<!--{/if}-->


<!--{elseif $smarty.server.PHP_SELF == $mypage}-->

<!--{if $arrMemberNews}-->
<h3>お知らせ</h3>
<div id="block-news-mypage">

<!--{section name=data loop=$arrMemberNews max=3}-->
<div class=" ">
<span><!--{$arrMemberNews[data].news_date_disp|date_format:"%m.%d"}--></span>&nbsp;
<!--{if $arrMemberNews[data].news_url}--><a href="<!--{$arrMemberNews[data].news_url|h}-->"><!--{/if}-->
<!--{$arrMemberNews[data].news_title|h}-->
<!--{if $arrMemberNews[data].news_url}--></a><!--{/if}-->
</div>
<!--{/section}-->

</div>
<!--{/if}-->

<!--{/if}-->
