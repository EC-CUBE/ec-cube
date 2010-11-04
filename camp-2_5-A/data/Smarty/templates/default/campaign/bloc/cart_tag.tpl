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
<div id="cart_tag_<{assign_product_id}>">
<!--{assign var=id value=$arrProducts[<{assign_product_id}>].product_id}-->
<!--▼買い物かご-->
<div class="listarea">
<div class="in_cart">
             <dl>
         <!--{if $tpl_classcat_find1[$id]}-->
           <!--{assign var=class1 value=classcategory_id`$id`_1}-->
           <!--{assign var=class2 value=classcategory_id`$id`_2}-->
           <dt><!--{$tpl_class_name1[$id]|escape}-->：</dt>
           <dd><select name="<!--{$class1}-->" style="<!--{$arrErr[$class1]|sfGetErrorColor}-->" onchange="lnSetSelect('<!--{$class1}-->', '<!--{$class2}-->', '<!--{$id}-->','');">
             <option value="">選択してください</option>
             <!--{html_options options=$arrClassCat1[$id] selected=$arrForm[$class1]}-->
           </select>
             <!--{if $arrErr[$class1] != ""}-->
             <br /><span class="attention">※ <!--{$tpl_class_name1[$id]}-->を入力して下さい。</span>
             <!--{/if}-->
           </dd>

           <!--{/if}-->
           <!--{if $tpl_classcat_find2[$id]}-->
             <dt><!--{$tpl_class_name2[$id]|escape}-->：</dt>
             <dd><select name="<!--{$class2}-->" style="<!--{$arrErr[$class2]|sfGetErrorColor}-->">
               <option value="">選択してください</option>
             </select>

             <!--{if $arrErr[$class2] != ""}-->
             <br /><span class="attention">※ <!--{$tpl_class_name2[$id]}-->を入力して下さい。</span>
             <!--{/if}-->
             </dd>

           <!--{/if}-->
           <!--{assign var=quantity value=quantity`$id`}-->

           <dt>数量：</dt>
           <dd><input type="text" name="<!--{$quantity}-->" size="3" class="box54" value="<!--{$arrForm[$quantity]|default:1}-->" maxlength="<!--{$smarty.const.INT_LEN}-->" style="<!--{$arrErr[$quantity]|sfGetErrorColor}-->" />
             <!--{if $arrErr[$quantity] != ""}-->
             <br /><span class="attention"><!--{$arrErr[$quantity]}--></span>
             <!--{/if}-->
           </dd>
         </dl>
             <div class="cartbtn">
             <a href="<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->" onclick="fnChangeAction('<!--{$smarty.server.REQUEST_URI|escape}-->#product<!--{$id}-->'); fnModeSubmit('cart','product_id','<!--{$id}-->'); return false;" onmouseover="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin_on.gif','cart<!--{$id}-->');" onmouseout="chgImg('<!--{$TPL_DIR}-->img/products/b_cartin.gif','cart<!--{$id}-->');">
               <img src="<!--{$TPL_DIR}-->img/products/b_cartin.gif" width="115" height="25" alt="カゴに入れる" name="cart<!--{$id}-->" id="cart<!--{$id}-->" /></a>
             </div>
           </div>
             <!--▲買い物かご-->
</div>


</div>
