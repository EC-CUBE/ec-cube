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
function fnMoveOption(sel , moveflg) {
  var fm = document.form1;
  var arrChoice = new Array();  // 選択されている項目
  var arrNotChoice = new Array();  // 選択されていない項目
  var arrNew = new Array();    // 移動後のリスト
  var arrTmp = new Array();
  var arrRev = new Array();
  
  if(fm[sel].selectedIndex == -1) alert("何も選択されていません。");
  else {
    // 下に移動する場合にはまずOPTIONを逆にする
    if (moveflg == 'bottom') {
      for(i=fm[sel].length-1, j=0; i >= 0; i--, j++){
        fm[sel].options[i].label=i;    // 順番をlabelに退避
        arrRev[j] = fm[sel].options[i];
      }
      for(i=0; i < arrRev.length; i++){
        fm[sel].options[i] = new Option(arrRev[i].text, arrRev[i].value);
        fm[sel].options[i].selected = arrRev[i].selected;
      }
    }

    // 一番下に空白を追加
    fm[sel].options[fm[sel].length] = new Option('', '');
    
    for(i = 0, choiceCnt = 0, notCnt = 0; i < fm[sel].length; i++) {
      if(!fm[sel].options[i].selected) {
        // 選択されていない項目配列を生成
        fm[sel].options[i].label=i;    // 順番をlabelに退避
        arrNotChoice[choiceCnt] = fm[sel].options[i];
        choiceCnt++;
      }else{
        // 選択されている項目配列を生成
        fm[sel].options[i].label=i;    // 順番をlabelに退避
        arrChoice[notCnt] = fm[sel].options[i];
        notCnt++;
      }
    }
    
    // 選択項目を上に移動
    for(i = arrChoice.length; i < 1; i--){
      arrChoice[i].label = arrChoice[i-1].label+1;
    }

    // 非選択項目を下に移動
    for(i = 0; i < arrNotChoice.length - 1; i++){
      arrNotChoice[i].label = arrNotChoice[i+1].label-1;
    }  

    // 選択項目と非選択項目をマージする
    for(choiceCnt = 0, notCnt = 0, cnt = 0; cnt < fm[sel].length; cnt++){
      if (choiceCnt >= arrChoice.length) {
        arrNew[cnt] = arrNotChoice[notCnt];
        notCnt++;
      }else if (notCnt >= arrNotChoice.length) {
        arrNew[cnt] = arrChoice[choiceCnt];
        choiceCnt++;
      }else{
        if(arrChoice[choiceCnt].label-1 <= arrNotChoice[notCnt].label){
          arrNew[cnt] = arrChoice[choiceCnt];
          choiceCnt++;
        }else{
          arrNew[cnt] = arrNotChoice[notCnt];
          notCnt++;
        }
      }
    }

    // 下に移動する場合には逆にしたものを元に戻す
    if (moveflg == 'bottom') {
      for(i=arrNew.length-2, j=0; i >= 0; i--, j++){
        arrTmp[j] = arrNew[i];
      }
      arrTmp[j]="";
      arrNew = arrTmp;
    }

    // optionを再作成
    fm[sel].length = arrNew.length - 1;
    for(i=0; i < arrNew.length - 1; i++){
      fm[sel].options[i] = new Option(arrNew[i].text, arrNew[i].value);
      fm[sel].options[i].selected = arrNew[i].selected;
    }
  }
}

function fnReplaceOption(restSel, addSel) {
  var fm = document.form1;
  var arrRest = new Array();  // 残りのリスト
  var arrAdd  = new Array();  // 追加のリスト
  
  if(fm[restSel].selectedIndex == -1) alert("何も選択されていません。");
  else {
    for(i = 0, restCnt = 0, addCnt = 0; i < fm[restSel].length; i++) {
      if(!fm[restSel].options[i].selected) {
        // 残要素の配列を生成
        arrRest[restCnt] = fm[restSel].options[i];
        restCnt++;
      }else{
        // 追加要素の配列を生成
        arrAdd[addCnt] = fm[restSel].options[i];
        addCnt++;
      }
    }

    // 残リスト生成
    fm[restSel].length = arrRest.length;
    for(i=0; i < arrRest.length; i++)
    {
      fm[restSel].options[i] = new Option(arrRest[i].text, arrRest[i].value);
    }

    // 追加先に項目を追加
    //fm[addSel].options[fm[addSel].length] = new Option(fm[sel2].value, fm[sel2].value);
    
    for(i=0; i < arrAdd.length; i++)
    {
      fm[addSel].options[fm[addSel].length] = new Option(arrAdd[i].text, arrAdd[i].value);
      fm[addSel].options[fm[addSel].length-1].selected = true;
    }
  }
}

// submitした場合に、出力項目一覧を選択状態にする
function lfnCheckList(sel) {
  var fm = document.form1;
  for(i = 0; i < fm[sel].length; i++) {
    fm[sel].options[i].selected = true;
  }
}

// リストボックスのサイズ変更
function ChangeSize(button, TextArea, Max, Min, row_tmp){
  if(TextArea.rows <= Min){
    TextArea.rows=Max; button.value="小さくする"; row_tmp.value=Max;
  }else{
    TextArea.rows =Min; button.value="大きくする"; row_tmp.value=Min;
  }
}

//-->
</script>



<form name="form1" id="form1" method="post" action="?" onsubmit="lfnCheckList('output_list[]')">
<input type="hidden" name="mode" value="confirm" />
<input type="hidden" name="tpl_subno_csv" value="<!--{$tpl_subno_csv}-->" />
<div id="admin-contents" class="contents-main">
  <h2><!--{$SubnaviName}--></h2>
  <table id="contents-csv-select">
    <tr>
      <td>
        <div class="btn">
          <button type="button" onClick="fnMoveOption('output_list[]', 'top');"><span> ▲ </span></button><br/>
          <button type="button" onClick="fnMoveOption('output_list[]', 'bottom');"><span> ▼ </span></button>
        </div>
      </td>
      <td>
        <h3>出力項目一覧</h3>
        <span class="attention"><!--{$arrErr.output_list}--></span>
        <select multiple name="output_list[]"<!--{if $arrErr.output_list}--> style="<!--{$arrErr.output_list|sfGetErrorColor}-->;"<!--{/if}-->>
          <!--{html_options options=$arrOutput}-->
        </select>
      </td>
      <td>
        <div class="btn">
          <button type="button" onClick="fnReplaceOption('choice_list[]', 'output_list[]');"><< 追加</button><br/>
          <button type="button" onClick="fnReplaceOption('output_list[]', 'choice_list[]');">削除 &gt;&gt;</button>
        </div>
      </td>
      <td>
        <h3>出力可能項目一覧</h3>
        <select multiple name="choice_list[]">
          <!--{html_options options=$arrChoice}-->
        </select>
      </td>
    </tr>
  </table>

  <div class="btn"><button type="submit"><span>この内容で登録する</span></button></div>

</div>
</form>
