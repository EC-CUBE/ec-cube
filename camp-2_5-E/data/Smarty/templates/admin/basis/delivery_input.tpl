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
<form name="form1" id="form1" method="post" action="">
<input type="hidden" name="mode" value="edit" />
<!--{assign var=key value="deliv_id"}-->
<input type="hidden" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" />
<div id="basis" class="contents-main">
  <h2>配送業者登録</h2>

  <table>
    <tr>
      <th>配送業者名<span class="attention"> *</span></td>
      <td colspan="3">
      <!--{assign var=key value="name"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>名称<span class="attention"> *</span></td>
      <td colspan="3">
      <!--{assign var=key value="service_name"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
    </tr>
    <tr>
      <th>伝票No.URL</td>
      <td colspan="3">
      <!--{assign var=key value="confirm_url"}-->
      <span class="attention"><!--{$arrErr[$key]}--></span>
      <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="30" class="box30" /></td>
    </tr>
    <!--{section name=cnt loop=$smarty.const.DELIVTIME_MAX}-->
    <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
    <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
    <!--{assign var=key value="deliv_time`$smarty.section.cnt.iteration`"}-->
    <!--{assign var=key_next value="deliv_time`$smarty.section.cnt.iteration+1`"}-->
    <!--{if $type == 0}-->
      <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
      <tr>
        <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
      </tr>    
      <!--{/if}-->
      <tr>
      <th>お届け時間<!--{$keyno}--></td>
      <!--{if $smarty.section.cnt.last}-->
      <!--{assign var=colspan value="3"}-->  
      <!--{else}-->
      <!--{assign var=colspan value="1"}-->
      <!--{/if}-->
      <td colspan="<!--{$colspan}-->">
      <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" size="20" class="box20" /></td>
    <!--{else}-->
      <th>お届け時間<!--{$keyno}--></td>
      <td><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> </td>
      </tr>
    <!--{/if}-->
    <!--{/section}-->

  </table>
  
  <!--{if $smarty.const.INPUT_DELIV_FEE}-->
  <h2>配送料登録</h2>
  <p>※全国一律送料 <input type="text" name="fee_all" class="box10" /> 円に設定する　<button type="button" onclick="fnSetDelivFee(<!--{$smarty.const.DELIVFEE_MAX}-->);"><span>反映</span></button></p>
  <table>
    <!--{section name=cnt loop=$smarty.const.DELIVFEE_MAX}-->
    <!--{assign var=type value="`$smarty.section.cnt.index%2`"}-->
    <!--{assign var=keyno value="`$smarty.section.cnt.iteration`"}-->
    <!--{assign var=key value="fee`$smarty.section.cnt.iteration`"}-->
    <!--{assign var=key_next value="fee`$smarty.section.cnt.iteration+1`"}-->
  
    <!--{if $type == 0}-->
      <!--{if $arrErr[$key] != "" || $arrErr[$key_next] != ""}-->
      <tr>
        <td colspan="4"><span class="attention"><!--{$arrErr[$key]}--><!--{$arrErr[$key_next]}--></span></td>
      </tr>    
      <!--{/if}-->
      <tr>
      <th><!--{$arrPref[$keyno]}--></td>
      <!--{if $smarty.section.cnt.last}-->
      <!--{assign var=colspan value="3"}-->  
      <!--{else}-->
      <!--{assign var=colspan value="1"}-->
      <!--{/if}-->
      <td width="247" colspan="<!--{$colspan}-->">
      <input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
    <!--{else}-->
      <th><!--{$arrPref[$keyno]}--></td>
      <td width="248"><input type="text" name="<!--{$arrForm[$key].keyname}-->" value="<!--{$arrForm[$key].value|escape}-->" maxlength="<!--{$arrForm[$key].length}-->" size="20" class="box20" style="<!--{$arrErr[$key]|sfGetErrorColor}-->" /> 円</td>
      </tr>
    <!--{/if}-->
    <!--{/section}-->
  </table>
  <!--{/if}-->

  <div class="btn">
    <button type="button" onclick="location.href='./delivery.php';"><span>前のページに戻る</span></button>
    <button type="submit"><span>この内容で登録する</span></button>
  </div>
</div>
</form>
