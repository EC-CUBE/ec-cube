<!--{*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
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
 }-->
<!--商品カテゴリーここから-->
<h2>
  <img src="<!--{$TPL_DIR}-->/img/side/title_cat.jpg" width="166" height="35" alt="商品カテゴリー" />
</h2>
<div id="categoryarea">
  <!--{section name=cnt loop=$arrTree}-->

      <!--{* 階層を level へ *}-->
      <!--{assign var=level value="`$arrTree[cnt].level`}-->

      <!--{* 最上位階層のリスト終了タグ *}-->
      <!--{if $level == 1 && !$smarty.section.cnt.first}-->
          </dl>
      <!--{/if}-->

      <!--{* カテゴリ名を disp_name へ *}-->
      <!--{assign var=disp_name value="`$arrTree[cnt].category_name`"}-->

      <!--{* 表示カテゴリのみ *}-->
      <!--{if $arrTree[cnt].display == 1}-->

          <!--{* 選択したカテゴリ *}-->
          <!--{if in_array($arrTree[cnt].category_id, $tpl_category_id) || in_array($arrTree[cnt].category_id, $root_parent_id)}-->
              <!--{if $level == 1}-->
                  <dl><dt class="onmark">
              <!--{else}-->
                  <dd>
              <!--{/if}-->

          <!--{* 未選択カテゴリ *}-->
          <!--{else}-->
              <!--{if $level == 1}-->
                  <dl><dt>
              <!--{else}-->
                  <dd>
              <!--{/if}-->
          <!--{/if}-->

          <!--{* 選択したカテゴリ *}-->
          <!--{if in_array($arrTree[cnt].category_id, $tpl_category_id) }-->
              <a href="<!--{$smarty.const.URL_DIR}-->products/list.php?category_id=<!--{$arrTree[cnt].category_id}-->" class="onlink"><!--{$disp_name|sfCutString:20|escape}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</a>

          <!--{* 未選択カテゴリ *}-->
          <!--{else}-->
              <a href="<!--{$smarty.const.URL_DIR}-->products/list.php?category_id=<!--{$arrTree[cnt].category_id}-->"><!--{$disp_name|sfCutString:20|escape}-->(<!--{$arrTree[cnt].product_count|default:0}-->)</a>
          <!--{/if}-->

          <!--{if $level == 1}-->
            </dt>
          <!--{else}-->
            </dd>
          <!--{/if}-->

      <!--{/if}-->

      <!--{* 最後に閉じるリスト終了タグ *}-->
      <!--{if $smarty.section.cnt.last}-->
        </dl>
      <!--{/if}-->

    <!--{/section}-->
</div>
<!--商品カテゴリーここまで-->
