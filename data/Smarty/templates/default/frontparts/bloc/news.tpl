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
<div id="newsarea" class="bloc_outer">
    <h2><img src="<!--{$TPL_DIR}-->img/icon/ico_block_news.gif" width="27" height="20" alt="*" class="title_icon" />
        新着情報</h2>
    <div class="bloc_body">
        <p>☆★☆ 新着情報は<a href="<!--{$smarty.const.URL_DIR}-->rss/<!--{$smarty.const.DIR_INDEX_URL}-->" target="_blank">RSS</a>で配信しています。★☆★</p>

        <!--{section name=data loop=$arrNews}-->
            <!--{assign var="date_array" value="-"|explode:$arrNews[data].news_date_disp}-->
            <dl>
                <dt><!--{$date_array[0]}-->年<!--{$date_array[1]}-->月<!--{$date_array[2]}-->日</dt>
                <dd>
                    <a
                        <!--{if $arrNews[data].news_url}-->
                            href="<!--{$arrNews[data].news_url}-->"
                            <!--{if $arrNews[data].link_method eq "2"}-->
                                target="_blank"
                            <!--{/if}-->
                        <!--{/if}-->
                    >
                        <!--{$arrNews[data].news_title|h|nl2br}--></a><br />
                    <!--{$arrNews[data].news_comment|h|nl2br}-->
                 </dd>
            </dl>
        <!--{/section}-->
    </div>
</div>
