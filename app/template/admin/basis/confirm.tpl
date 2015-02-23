<div id="basis" class="contents-main">

<form name="form1" id="form1" method="post" action="?">
<input type="hidden" name="<!--{$smarty.const.TRANSACTION_ID_NAME}-->" value="<!--{$transactionid}-->" />
<input type="hidden" name="mode" value="complete" />

<!--{foreach key=key item=item from=$arrForm}-->
    <!--{if $key == 'regular_holiday_ids'}-->
        <!--{foreach key=holiday_key item=holiday_id from=$arrForm.regular_holiday_ids}-->
            <input type="hidden" name="regular_holiday_ids[<!--{$holiday_key}-->]" value="<!--{$holiday_id|h}-->" />
        <!--{/foreach}-->
    <!--{else}-->
        <input type="hidden" name="<!--{$key}-->" value="<!--{$item|h}-->" />
    <!--{/if}-->
<!--{/foreach}-->

	<h2>基本情報</h2>
    <table>
        <tr>
            <th>会社名</th>
            <td>
                <!--{$arrForm.company_name|h}-->
            </td>
        </tr>
        <tr>
            <th>会社名(フリガナ)</th>
            <td>
                <!--{$arrForm.company_kana|h}-->
            </td>
        </tr>
        <tr>
            <th>店名</th>
            <td>
                <!--{$arrForm.shop_name|h}-->
            </td>
        </tr>
        <tr>
            <th>店名(フリガナ)</th>
            <td>
                <!--{$arrForm.shop_kana|h}-->
            </td>
        </tr>
		<tr>
            <th>店名(英語表記)</th>
            <td>
                <!--{$arrForm.shop_name_eng|h}-->
            </td>
        </tr>
        <tr>
            <th>郵便番号</th>
            <td>
                <!--{$arrForm.zip01|h}--> - <!--{$arrForm.zip02|h}-->
            </td>
        </tr>
        <tr>
            <th>SHOP所在地</th>
            <td>
                <!--{$arrPref[$arrForm.pref]|h}--><!--{$arrForm.addr01|h}--><!--{$arrForm.addr02|h}-->
            </td>
        </tr>
		<tr>
            <th>TEL</th>
            <td><!--{if strlen($arrForm.tel01) > 0}--><!--{$arrForm.tel01|h}--> - <!--{$arrForm.tel02|h}--> - <!--{$arrForm.tel03|h}--><!--{else}-->未登録<!--{/if}--></td>
        </tr>
        <tr>
            <th>FAX</th>
            <td><!--{if strlen($arrForm.fax01) > 0}--><!--{$arrForm.fax01|h}--> - <!--{$arrForm.fax02|h}--> - <!--{$arrForm.fax03|h}--><!--{else}-->未登録<!--{/if}--></td>
        </tr>
        <tr>
            <th>店舗営業時間</th>
            <td>
            	<!--{$arrForm.business_hour|h}-->
            </td>
        </tr>
		<tr>
            <th>商品注文受付<br />メールアドレス</th>
            <td>
                <!--{$arrForm.email01|h}-->
            </td>
        </tr>
        <tr>
            <th>問い合わせ受付<br />メールアドレス</th>
            <td>
                <!--{$arrForm.email02|h}-->
 	        </td>
        </tr>
        <tr>
            <th>メール送信元<br />メールアドレス</th>
            <td>
                <!--{$arrForm.email03|h}-->
            </td>
        </tr>
        <tr>
            <th>送信エラー受付<br />メールアドレス</th>
            <td>
				<!--{$arrForm.email04|h}-->
            </td>
        </tr>
		<tr>
            <th>取扱商品</th>
            <td>
				<!--{$arrForm.good_traded|h}-->
            </td>
        </tr>
        <tr>
            <th>メッセージ</th>
            <td>
				<!--{$arrForm.message|h}-->
            </td>
        </tr>
    </table>

	<h2>定休日設定</h2>
	<table>
		<tr>
			<th>定休日</th>
			<td>
			    <!--{foreach item=item from=$arrForm.regular_holiday_ids}-->
    				<!--{$arrRegularHoliday[$item]|h}-->　
			    <!--{/foreach}-->
			</td>
		</tr>
	</table>

	<h2>SHOP機能</h2>
	<table>
		<tr>
			<th>送料無料条件</th>
			<td>
				<!--{$arrForm.free_rule|h}-->
			</td>
		</tr>
		<tr>
			<th>ダウンロード可能日数</th>
			<td>
				<!--{if $arrForm.downloadable_days_unlimited == 1}-->
				無制限
				<!--{else}-->
					<!--{$arrForm.downloadable_days|h}-->日間有効
				<!--{/if}-->
			</td>
		</tr>
	</table>

	<h2>地図設定</h2>
	<table>
		<tr>
			<th>緯度</th>
			<td>
				<!--{$arrForm.latitude|h}-->
			</td>
		</tr>
		<tr>
			<th>経度</th>
			<td>
				<!--{$arrForm.longitude|h}-->
			</td>
		</tr>
	</table>

    <div class="btn-area">
        <ul>
            <li><a class="btn-action" href="javascript:;" onclick="eccube.setModeAndSubmit('return','',''); return false;"><span class="btn-prev">前のページに戻る</span></a></li>
            <li><a class="btn-action" href="javascript:;" onclick="eccube.submitForm(); return false;"><span class="btn-next">この内容で登録する</span></a></li>
        </ul>
    </div>

</div>