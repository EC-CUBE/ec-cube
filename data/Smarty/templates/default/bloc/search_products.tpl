<!--{*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *}-->
<!--▼検索条件ここから-->
<h2>
  <img src="<!--{$TPL_DIR}-->img/side/title_search.jpg" width="166" height="35" alt="検索条件" />
</h2>
<div id="searcharea">
  <!--検索フォーム-->
    <form name="search_form" id="search_form" method="get" action="<!--{$smarty.const.URL_DIR}-->products/list.php">

      <p><img src="<!--{$TPL_DIR}-->img/side/search_cat.gif" width="104" height="10" alt="商品カテゴリから選ぶ" />
        <input type="hidden" name="mode" value="search" />
        <select name="category_id" class="box142">
          <option label="すべての商品" value="">全ての商品</option>
          <!--{html_options options=$arrCatList selected=$category_id}-->
        </select>
      </p>
      <p><img src="<!--{$TPL_DIR}-->img/side/search_name.gif" width="66" height="10" alt="商品名を入力" /></p>
      <p><input type="text" name="name" class="box142" maxlength="50" value="<!--{$smarty.get.name|escape}-->" /></p>
      <p class="btn"><input type="image" onmouseover="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_search_on.gif',this)" onmouseout="chgImgImageSubmit('<!--{$TPL_DIR}-->img/side/button_search.gif',this)" src="<!--{$TPL_DIR}-->img/side/button_search.gif" style="width: 51px; height: 22px; border: none" alt="検索" name="search" /></p>
    </form>
</div>
<!--▲検索条件ここまで-->
