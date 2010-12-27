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
<script type="text/javascript">
<!--
function lfnCheckSubmit( fm ){
  
  var err = '';
  /*
  if ( ! fm["title"].value ){
    err += '見出しコメントを入力して下さい。';
  }
  */
  if ( ! fm["comment"].value ){
    if ( err ) err += '';
    err += 'コメントを入力して下さい。';
  }
  if ( err ){
    alert(err);
    return false;
  } else {
    if(window.confirm('内容を登録しても宜しいですか')){
      return true;
    }
  }
}

function lfnCheckSetItem( rank ){
  var flag = true;
  var checkRank = '<!--{$checkRank}-->';
  if ( checkRank ){
    if ( rank != checkRank ){
      if( ! window.confirm('さきほど選択した<!--{$checkRank}-->位の情報は破棄されます。宜しいでしょうか')){
        flag = false;
      }
    } 
  }
  
  if ( flag ){
    win03('./recommend_search.php?rank=' + rank,'search','500','500');
  }
}

//-->
</script>

<div id="admin-contents" class="contents-main">
  <table class="list center" id="recommend-table">
    <tr>
      <th>#</th>
      <th>画像</th>
      <th>商品名</th>
      <th>削除</th>
      <th>変更</th>
      <th>コメント</th>
    </tr>
    <!--{section name=cnt loop=$tpl_disp_max}-->
    <!--▼おすすめ商品<!--{$smarty.section.cnt.iteration}-->-->
    <tr>
      <th><!--{$smarty.section.cnt.iteration}--></th>
      <td>
        <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
          <img src="<!--{$smarty.const.IMAGE_SAVE_URL}--><!--{$arrItems[$smarty.section.cnt.iteration].main_list_image|sfNoImageMainList|escape}-->" alt="<!--{$arrItems[$smarty.section.cnt.iteration].name|escape}-->" />
        <!--{/if}-->
      </td>
      <td><!--{$arrItems[$smarty.section.cnt.iteration].name|escape}--></td>
      <td>
        <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
        <a href="#" onClick="return fnInsertValAndSubmit( document.form<!--{$smarty.section.cnt.iteration}-->, 'mode', 'delete', '削除します。宜しいですか' )">削除</a>
        <!--{/if}-->
      </td>
      <td>
        <a href="#" onclick="lfnCheckSetItem('<!--{$smarty.section.cnt.iteration}-->'); return false;" target="_blank">
          <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->商品変更<!--{else}-->商品選択<!--{/if}--></a>
      </td>
      <td>
        <form name="form<!--{$smarty.section.cnt.iteration}-->" id="form<!--{$smarty.section.cnt.iteration}-->" method="post" action="?">
        <input type="hidden" name="mode" value="regist" />
        <input type="hidden" name="product_id" value="<!--{$arrItems[$smarty.section.cnt.iteration].product_id|escape}-->" />
        <input type="hidden" name="category_id" value="<!--{$category_id|escape}-->" />
        <input type="hidden" name="rank" value="<!--{$arrItems[$smarty.section.cnt.iteration].rank|escape}-->" />
        <span class="attention"><!--{$arrErr[$smarty.section.cnt.iteration].comment}--></span>
        <textarea name="comment" cols="45" rows="4" style="width: 337px; height: 82px; <!--{$arrErr[$smarty.section.cnt.iteration].comment|sfGetErrorColor}-->" <!--{$arrItems[$smarty.section.cnt.iteration].product_id|sfGetEnabled}-->><!--{$arrItems[$smarty.section.cnt.iteration].comment}--></textarea>
        <!--{if $arrItems[$smarty.section.cnt.iteration].product_id}-->
        <br /><a class="btn-normal" href="javascript:;" onclick="return lfnCheckSubmit(document.form<!--{$smarty.section.cnt.iteration}-->);"><span>登録する</span></a>
        <!--{/if}-->
        </form>
      </td>
    </tr>
  <!--▲おすすめ商品<!--{$smarty.section.cnt.iteration}-->-->
  <!--{/section}-->
  </table>
</div>
