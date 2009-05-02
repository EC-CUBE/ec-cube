<!--{*
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2008 LOCKON CO.,LTD. All Rights Reserved.
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
  function func_check() {
    res = confirm('登録します。宜しいですか？');
    if( res == true ) {
      return true;
    }
    return false;
  }
    
    
  function func_disp( no ){

    ml = document.form1.elements['question[' + no + '][kind]'];
    len = ml.length;

    optd = $("#Q" + no + "-option");
    nmth = $("#Q" + no + "-name").children("th");
    var flag = 0;
    
    for( i = 0; i < len ; i++) {
      
        if ( ml[i].checked ){
          if ( (ml[i].value == 3) || (ml[i].value == 4) ) {
            optd.show();
            nmth.attr("rowspan", "3");
          } else {
            optd.hide();
            nmth.attr("rowspan", "2");
          }
          flag = 1;
        } 
    
    }

    if ( flag == 0 ){
      optd.hide();
      nmth.attr("rowspan", "2");
    }
    
  }
  
  function delete_check() {
    res = confirm('アンケートを削除しても宜しいですか？');
    if(res == true) {
      return true;
    }
    return false;
  }
// -->
</script>

<div id="admin-contents" class="contents-main">
<form name="form1" method="post" action="?mode=regist" onSubmit="return func_check(); false;">
<input type="hidden" name="question_id" value="<!--{$QUESTION_ID}-->" />
  <h2><!--{if $QUESTION_ID}-->修正<!--{else}-->新規<!--{/if}-->登録</h2>
  <!--{if $MESSAGE != ""}-->
  <div class="message"><span class="attention"><!--{$MESSAGE}--></span></div>
  <!--{/if}-->

  <table>
    <tr>
      <th>稼働・非稼働<span class="attention">*</span></th>
      <td>
        <span <!--{if $ERROR.active}--><!--{sfSetErrorStyle}--><!--{/if}-->>
        <!--{html_radios name="active" options=$arrActive selected=$smarty.post.active}-->
        </span>
        <!--{if $ERROR.active}--><br /><span class="attention"><!--{$ERROR.active}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>アンケートタイトル<span class="attention">*</span></th>
      <td>
        <input type="text" name="title" size="70" class="box70"  maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.title|escape}-->" <!--{if $ERROR.title}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <!--{if $ERROR.title}--><br /><span class="attention"><!--{$ERROR.title}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>アンケート内容<span class="attention">*</span></th>
      <td>
        <textarea name="contents" cols="60" rows="4" class="area60" wrap="physical" <!--{if $ERROR.contents}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$smarty.post.contents}--></textarea>
        <!--{if $ERROR.contents}--><br /><span class="attention"><!--{$ERROR.contents}--></span><!--{/if}-->
      </td>
    </tr>
    <!--{section name=question loop=$cnt_question}-->
    <tr id="Q<!--{$smarty.section.question.index}-->-name">
      <th rowspan="3">質問<!--{$smarty.section.question.iteration}--><!--{if $smarty.section.question.iteration eq 1}--><span class="attention">*</span><!--{/if}--></th>
      <td>
        <input type="text" name="question[<!--{$smarty.section.question.index}-->][name]" size="70" class="box70" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].name|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].name}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <!--{if $ERROR.question[$smarty.section.question.index].name}--><br /><span class="attention"><!--{$ERROR.question[$smarty.section.question.index].name}--></span><!--{/if}-->
      </td>
    </tr>
    <tr id="Q<!--{$smarty.section.question.index}-->-kind">
      <td>
        <span<!--{if $ERROR_COLOR.question[$smarty.section.question.index].kind}--> style="background-color:<!--{$ERROR_COLOR.question[$smarty.section.question.index].kind}-->"<!--{/if}-->>
        <!--{html_radios_ex onClick="func_disp(`$smarty.section.question.index`)" name="question[`$smarty.section.question.index`][kind]" options="$arrQuestion" selected="`$smarty.post.question[$smarty.section.question.index].kind`"}-->
        </span>
        <!--{if $ERROR.question[$smarty.section.question.index].kind}--><br /><span class="attention"><!--{$ERROR.question[$smarty.section.question.index].kind}--></span><!--{/if}-->
      </td>
    </tr>
    <tr id="Q<!--{$smarty.section.question.index}-->-option">
      <td>
        1 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][0]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.0|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].kind}--><!--{sfSetErrorStyle}--><!--{/if}--> />　2 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][1]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.1|escape}-->" <!--{if $ERROR.question[$smarty.section.question.index].kind}--><!--{sfSetErrorStyle}--><!--{/if}--> /><br />
        3 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][2]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.2|escape}-->" />　4 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][3]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.3|escape}-->" /><br />
        5 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][4]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.4|escape}-->" />　6 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][5]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.5|escape}-->" /><br />
        7 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][6]" size="40" class="box40" maxlength="<!--{$smarty.const.STEXT_LEN}-->" value="<!--{$smarty.post.question[$smarty.section.question.index].option.6|escape}-->" />　8 <input type="text" name="question[<!--{$smarty.section.question.index}-->][option][7]" size="40" class="box40" value="<!--{$smarty.post.question[$smarty.section.question.index].option.7|escape}-->" />
      </td>
    </tr>
    <!--{/section}-->
  </table>

  <div class="btn">
    <button type="submit"><span>登録する</span></button>
    <button type="reset"><span>内容をクリア</span></button>
  </div>
</form>

<form name="form2" method="post" action="?">
  <h2>登録済みアンケート</h2>
  <table class="list center">
    <tr>
      <th>編集</th>
      <th>登録日</th>
      <th>アンケートタイトル</th>
      <th>ページ参照</th>
      <th>結果取得</th>
      <th>削除</th>
    </tr>
    <!--{section name=data loop=$list_data}-->
    <tr <!--{if $list_data[data].question_id eq $smarty.request.question_id}--><!--{sfSetErrorStyle}--><!--{/if}-->>
      <td class="main"><a href="<!--{$smarty.server.PHP_SELF|escape}-->?question_id=<!--{$list_data[data].question_id}-->">編集</a></td>
      <td><!--{$list_data[data].disp_date}--></td>
      <td class="left"><!--{$list_data[data].question_name|escape}--></td>
      <td><a href="<!--{$smarty.const.SITE_URL}-->inquiry/<!--{$smarty.const.DIR_INDEX_URL}-->?question_id=<!--{$list_data[data].question_id}-->" target="_blank">参照</a></td>
      <td><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=csv&amp;question_id=<!--{$list_data[data].question_id}-->">download</a></td>
      <td><a href="<!--{$smarty.server.PHP_SELF|escape}-->?mode=delete&amp;question_id=<!--{$list_data[data].question_id}-->" onClick="return delete_check()">削除</a></td>
    </tr>
    <!--{/section}-->
  </table>
</form>

</div>

<script type="text/javascript">
<!--
  <!--{section name=question loop=$cnt_question}-->
    func_disp(<!--{$smarty.section.question.index}-->);
  <!--{/section}-->  
//-->
</script>

