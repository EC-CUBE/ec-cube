<!--{section name=question loop=$QUESTION.question}-->
  <!--{if $QUESTION.question[question].kind }-->
    <tr>
      <th>質問<!--{$smarty.section.question.iteration}-->：<!--{$QUESTION.question[question].name|escape}--></th>
    </tr>
    <!--{if $QUESTION.question[question].kind eq 1}-->
    <tr>
      <td>
        <textarea name="option[<!--{$smarty.section.question.index}-->]" cols="55" rows="8" class="area55" <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->><!--{$arrForm.option[$smarty.section.question.index]|escape}--></textarea>
        <!--{if $arrErr.option[$smarty.section.question.index]}--><br /><span class="attention">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
      </td>
    </tr>
    <!--{elseif $QUESTION.question[question].kind eq 2}-->
    <tr>
      <td>
        <input type="text" name="option[<!--{$smarty.section.question.index}-->]" size="55" class="box50" value="<!--{$arrForm.option[$smarty.section.question.index]|escape}-->" <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}--> />
        <!--{if $arrErr.option[$smarty.section.question.index]}--><br /><span class="attention">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
      </td>
    </tr>
    <!--{elseif $QUESTION.question[question].kind eq 3}-->
    <tr>
      <td>
        <input type="hidden" name="option[<!--{$smarty.section.question.index}-->][0]" value="" />
        <span <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <!--{html_checkboxes name="option[`$smarty.section.question.index`]" options=$QUESTION.question[question].option selected=$arrForm.option[question] separator="<br />"}-->
        </span>
        <!--{if $arrErr.option[$smarty.section.question.index]}--><br /><span class="attention">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
      </td>
    </tr>
    <!--{elseif $QUESTION.question[question].kind eq 4}-->
    <tr>
      <td>
        <input type="hidden" name="option[<!--{$smarty.section.question.index}-->][0]" value="" />
        <span <!--{if $arrErr.option[$smarty.section.question.index]}--><!--{sfSetErrorStyle}--><!--{/if}-->>
          <!--{html_radios name="option[`$smarty.section.question.index`]" options=$QUESTION.question[question].option selected=$arrForm.option[question] separator="<br />"}-->
        </span>
        <!--{if $arrErr.option[$smarty.section.question.index]}--><br /><span class="attention">質問<!--{$smarty.section.question.iteration}-->を入力して下さい</sapn><!--{/if}-->
      </td>
    </tr>
    <!--{/if}-->
  <!--{/if}-->
<!--{/section}-->
