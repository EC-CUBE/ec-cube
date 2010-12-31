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
<form name="form1" id="form1" method="post" action="?" enctype="multipart/form-data">
  <!--{foreach key=key item=item from=$arrForm}-->
    <!--{if $key == 'product_status' || $key == 'payment_ids'}-->
      <!--{foreach item=statusVal from=$item}-->
      <input type="hidden" name="<!--{$key}-->[]" value="<!--{$statusVal|h}-->" />
      <!--{/foreach}-->
    <!--{else}-->
      <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
  <!--{/foreach}-->
  <div id="products" class="contents-main">

  <table>
    <tr>
      <th>商品名</th>
      <td>
      <!--{$arrForm.name|h}-->
      </td>
    </tr>
    <tr>
      <th>商品カテゴリ</th>
      <td>
      <!--{section name=cnt loop=$arrCategory_id}-->
        <!--{assign var=key value=$arrCategory_id[cnt]}-->
        <!--{$arrCatList[$key]|strip|sfTrim}--><br />
      <!--{/section}-->
      </td>
    </tr>
    <tr>
      <th>公開・非公開</th>
      <td>
      <!--{$arrDISP[$arrForm.status]}-->
      </td>
    </tr>
    <tr>
      <th>商品ステータス</th>
      <td>
      <!--{foreach from=$arrForm.product_status item=status}-->
        <img src="<!--{$TPL_DIR_DEFAULT}--><!--{$arrSTATUS_IMAGE[$status]}-->">
      <!--{/foreach}-->
      </td>
    </tr>

    <!--{if $tpl_nonclass == true}-->
    <tr>
      <th>商品種別</th>
      <td>
      <!--{$arrProductType[$arrForm.product_type_id]}-->
      </td>
    </tr>
    <tr>
      <th>ダウンロードファイル名</th>
      <td>
      <!--{$arrForm.down_filename|h}-->
      </td>
    </tr>
    <tr>
      <th>ダウンロード商品用<BR />ファイル</th>
      <td>
      <!--{if $arrForm.down_realfilename != ""}-->
          <!--{$arrForm.down_realfilename|h}-->
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>商品コード</th>
      <td>
      <!--{$arrForm.product_code|h}-->
      </td>
    </tr>
    <tr>
      <th><!--{$smarty.const.NORMAL_PRICE_TITLE}--></th>
      <td>
      <!--{$arrForm.price01|h}-->
      円</td>
    </tr>
    <tr>
      <th><!--{$smarty.const.SALE_PRICE_TITLE}--></th>
      <td>
      <!--{$arrForm.price02|h}-->
      円</td>
    </tr>
    <tr>
      <th>在庫数</th>
      <td>
      <!--{if $arrForm.stock_unlimited == 1}-->
        無制限
      <!--{else}-->
        <!--{$arrForm.stock|h}-->
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>支払方法</th>
      <td>
      <!--{foreach from=$arrForm.payment_ids item=payment_id}-->
        <!--{$arrPayments[$payment_id]|h}-->&nbsp;
      <!--{/foreach}-->
      </td>
    </tr>
    <!--{/if}-->

    <tr>
      <th>商品送料</th>
      <td>
        <!--{$arrForm.deliv_fee|h}-->
        円</td>
    </tr>
    <tr>
      <th>ポイント付与率</th>
      <td>
      <!--{$arrForm.point_rate|h}-->
      ％</td>
    </tr>
    <tr>
      <th>発送日目安</th>
      <td>
      <!--{$arrDELIVERYDATE[$arrForm.deliv_date_id]|h}-->
      </td>
    </tr>
    <tr>
      <th>購入制限</th>
      <td>
        <!--{$arrForm.sale_limit|default:'無制限'|h}-->
      </td>
    </tr>
    <tr>
      <th>メーカー</th>
      <td>
        <!--{$arrMaker[$arrForm.maker_id]|h}-->
      </td>
    </tr>
    <tr>
      <th>メーカーURL</th>
      <td>
      <!--{$arrForm.comment1|h|sfPutBR:$smarty.const.LINE_LIMIT_SIZE}-->
      </td>
    </tr>
    <!--{*
    <tr>
      <th>成分</th>
      <td>
      <!--{$arrForm.comment2|h}-->
      </td>
    </tr>
    *}-->
    <tr>
      <th>検索ワード</th>
      <td>
      <!--{$arrForm.comment3|h}-->
      </td>
    </tr>
    <tr>
      <th>備考欄(SHOP専用)</th>
      <td>
        <!--{$arrForm.note|h|nl2br}-->
      </td>
    </tr>
    <tr>
      <th>一覧-メインコメント</th>
      <td>
      <!--{$arrForm.main_list_comment|h|nl2br}-->
      </td>
    </tr>
    <tr>
      <th>詳細-メインコメント</th>
      <td>
      <!--{$arrForm.main_comment|nl2br_html}-->
      </td>
    </tr>
    <tr>
      <th>一覧-メイン画像</th>
      <td>
      <!--{assign var=key value="main_list_image"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>詳細-メイン画像</th>
      <td>
      <!--{assign var=key value="main_image"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>詳細-メイン拡大画像</th>
      <td>
      <!--{assign var=key value="main_large_image"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
      <!--{/if}-->
      </td>
    </tr>

    <!--{* オペビルダー用 *}-->
    <!--{if "sfViewAdminOpe"|function_exists === TRUE}-->
    <!--{include file=`$smarty.const.MODULE_REALDIR`mdl_opebuilder/admin_ope_view.tpl}-->
    <!--{/if}-->

    <!--{section name=cnt loop=$smarty.const.PRODUCTSUB_MAX}-->
    <!--▼商品<!--{$smarty.section.cnt.iteration}-->-->
    <tr>
      <th>詳細-サブタイトル（<!--{$smarty.section.cnt.iteration}-->）</th>
      <td>
      <!--{assign var=key value="sub_title`$smarty.section.cnt.iteration`"}-->
      <!--{$arrForm[$key]|h}-->
      </td>
    </tr>
    <tr>
      <th>詳細-サブコメント（<!--{$smarty.section.cnt.iteration}-->）</th>
      <td>
      <!--{assign var=key value="sub_comment`$smarty.section.cnt.iteration`"}-->
      <!--{$arrForm[$key]|nl2br_html}-->
      </td>
    </tr>
    <tr>
      <th>詳細-サブ画像（<!--{$smarty.section.cnt.iteration}-->）</th>
      <td>
      <!--{assign var=key value="sub_image`$smarty.section.cnt.iteration`"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
      <!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>詳細-サブ拡大画像（<!--{$smarty.section.cnt.iteration}-->）</th>
      <td>
      <!--{assign var=key value="sub_large_image`$smarty.section.cnt.iteration`"}-->
      <!--{if $arrFile[$key].filepath != ""}-->
      <img src="<!--{$arrFile[$key].filepath}-->" alt="<!--{$arrForm.name|h}-->" /><br />
      <!--{/if}-->
      </td>
    </tr>
    <!--▲商品<!--{$smarty.section.cnt.iteration}-->-->
    <!--{/section}-->

    <!--{if $smarty.const.OPTION_RECOMMEND == 1}-->
    <!--▼関連商品-->
    <!--{section name=cnt loop=$smarty.const.RECOMMEND_PRODUCT_MAX}-->
    <!--{assign var=recommend_no value="`$smarty.section.cnt.iteration`"}-->
    <tr>
      <th>関連商品(<!--{$smarty.section.cnt.iteration}-->)<br />
      <!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
        <img src="<!--{$smarty.const.URL_PATH}-->resize_image.php?image=<!--{$arrRecommend[$recommend_no].main_list_image|sfNoImageMainList|h}-->&amp;width=65&amp;height=65" alt="<!--{$arrRecommend[$recommend_no].name|h}-->">
      <!--{/if}-->
      </th>
      <td>
      <!--{if $arrRecommend[$recommend_no].product_id|strlen >= 1}-->
        商品コード:<!--{$arrRecommend[$recommend_no].product_code_min}--><br />
        商品名:<!--{$arrRecommend[$recommend_no].name|h}--><br />
        コメント:<br />
        <!--{$arrRecommend[$recommend_no].comment|h|nl2br}-->
      <!--{/if}-->
      </td>
    </tr>
    <!--{/section}-->
    <!--▲関連商品-->
    <!--{/if}-->
  </table>

  <div class="btn">
    <a class="btn-normal" href="javascript:;" onclick="fnModeSubmit('confirm_return','',''); return false;"><span>前のページに戻る</span></a>
    <a class="btn-normal" href="javascript:;" onclick="document.form1.submit();"><span>この内容で登録する</span></a>
  </div>
</div>
</form>
