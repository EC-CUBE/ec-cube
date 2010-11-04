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
<script language="JavaScript">
<!--
function lfnCheckSubmit(){
  
  fm = document.form1;
  var err = '';
  
  if ( ! fm["subject"].value ){
    err += 'Subjectを入力して下さい。';
  }
  if ( ! fm["body"].value ){
    if ( err ) err += '
';
    err += '本文を入力して下さい。';
  }
  if ( err ){
    alert(err);
    return false;
  } else {
    if(window.confirm('内容を登録しても宜しいですか')){
      return true;
    }else{
      return false;
    }
  }
}
//-->
</script>


<form name="form1" id="form1" method="post" action="?" onSubmit="return lfnCheckSubmit();">
<input type="hidden" name="mode" value="<!--{$mode}-->" />
<input type="hidden" name="template_id" value="<!--{$arrForm.template_id}-->" />
<div id="mail" class="contents-main">
  <h2>配信内容設定：<!--{$title}--></h2>
  <table class="form">
    <tr>
      <th>メール形式<span class="attention"> *</span></th>
      <td>
        <span <!--{if $arrErr.mail_method}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{html_radios name="mail_method" options=$arrMagazineType separator="&nbsp;" selected=$arrForm.mail_method}--></span>
        <!--{if $arrErr.mail_method}--><br /><span class="attention"><!--{$arrErr.mail_method}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>Subject<span class="attention"> *</span></th>
      <td>
        <input type="text" name="subject" size="65" class="box65" <!--{if $arrErr.subject}--><!--{sfSetErrorStyle}--><!--{/if}--> value="<!--{$arrForm.subject|escape}-->" />
        <!--{if $arrErr.subject}--><br /><span class="attention"><!--{$arrErr.subject}--></span><!--{/if}-->
      </td>
    </tr>
    <tr>
      <th>本文<span class="attention"> *</span><br />（名前差し込み時は {name} といれてください）</th>
      <td>
        <textarea name="body" cols="90" rows="40" class="area90" <!--{if $arrErr.body}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.body|escape}--></textarea>
        <!--{if $arrErr.body}--><br /><span class="attention"><!--{$arrErr.body}--></span><!--{/if}-->
      </td>
    </tr>
  </table>
  <div class="btn">
    <button type="submit"><span>この内容で登録する</span></button>
    <button type="button" onclick="fnCharCount('form1','body','cnt_footer');" name="next" id="next"><span>文字数カウント</span></button>
    <span>今までに入力したのは<input type="text" name="cnt_footer" size="4" class="box4" readonly = true style="text-align:right" />文字です。</span>
  </div>
</table>

