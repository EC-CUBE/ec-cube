<!--{*
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
 *}-->

<div class="block_outer">
    <div id="news_area">
    <h2><img src="<!--{$TPL_URLPATH}-->img/title/icon_bloc_news.png" alt="" /><span class="title">New information</span><span class="rss"><a href="<!--{$smarty.const.ROOT_URLPATH}-->rss/<!--{$smarty.const.DIR_INDEX_PATH}-->" target="_blank"><img src="<!--{$TPL_URLPATH}-->img/button/btn_rss.jpg" alt="RSS" /></a></span></h2>
        <div class="block_body">
            <div class="news_contents">
            <!--{section name=data loop=$arrNews}-->
            <!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
            <dl class="newslist">
                <dt><!--{$date_array[0]}--> / <!--{$date_array[1]}--> / <!--{$date_array[2]}--></dt>
                <dt>
                    <a
                        <!--{if $arrNews[data].news_url}--> href="<!--{$arrNews[data].news_url}-->" <!--{if $arrNews[data].link_method eq "2"}--> target="_blank"
                            <!--{/if}-->
                        <!--{/if}-->
                    >
                        <!--{$arrNews[data].news_title|h|nl2br}--></a>
                </dt>
                <dd class="mini"><!--{$arrNews[data].news_comment|h|nl2br}--></dd>
            </dl>
            <!--{/section}-->
            </div>
        </div>
    </div>
</div>
