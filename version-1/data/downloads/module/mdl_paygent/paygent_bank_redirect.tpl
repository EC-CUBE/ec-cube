<script type="text/javascript">
<!--
function next(now, next) {
	if (now.value.length >= now.getAttribute('maxlength')) {
	next.focus();
	}
}
//-->
</script>

<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="/img/shopping/flow03.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
		
		<!--▼MAIN CONTENTS-->				
				<table width="666" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td colspan="3"><img src="/img/contents/contents_title_top.gif" width="666" height="7" alt=""></td>
					</tr>
					<tr>
						<td background="/img/contents/contents_title_left_bg.gif"><img src="/img/contents/contents_title_left.gif" width="22" height="12" alt=""></td>
						<td bgcolor="#636469" width="638" class="fs14n"><strong><span class="white">銀行ネット決済</span><strong></td>
						<td background="/img/contents/contents_title_right_bg.gif"><img src="/img/common/_.gif" width="18" height="1" alt=""></td>
					</tr>
					<tr>
						<td colspan="3"><img src="/img/contents/contents_title_bottom.gif" width="666" height="7" alt=""></td>
					</tr>
					<tr><td height="15"></td></tr>
				</table>

				<table width="666" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td bgcolor="#ffffff">
						<table width="666" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5" class="fs12"><span class="red"><!--{$tpl_error}--></span></td></tr>
							<tr>
								<td bgcolor="#cccccc">
								<table width="666" border="0" cellspacing="1" cellpadding="10" summary=" ">
									<tr>
										<td class="fs12st" bgcolor="#ffffff" align="center">
											「<!--{$tpl_bank}-->」の決済ページへお進み下さい。<!--{$tpl_form}-->
										</td>
									</tr>	
								</table>
								</td>
							</tr>
						</table>
						
						<table width="666" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td align="center" bgcolor="#f7f5f4">
								<table width="666" border="0" cellspacing="0" cellpadding="6" summary=" ">
									<tr>
										<td align="center" height="40" bgcolor="#f7f5f4">
											<a href="#" onclick="document.form2.submit(); return false;" onmouseover="chgImgImageSubmit('/img/common/b_back_on.gif',back03)" onmouseout="chgImgImageSubmit('/img/common/b_back.gif',back03)"><img src="/img/common/b_back.gif" width="150" height="30" alt="戻る" border="0" name="back03" id="back03"/></a>
										</td>
									</tr>
								</table>

								</td>
							</tr>
						</table>
						</td>
					</tr>
				</table>
				<form name="form2" id="form2" method="post" action="./load_payment_module.php" autocomplete="off">
				<input type="hidden" name="mode" value="return">			
				</form>
				<!--▲MAIN CONTENTS-->
		</td>
	</tr>

</table>
<!--▲CONTENTS-->