<?php /* Smarty version 2.6.13, created on 2007-01-10 00:22:15
         compiled from contact/complete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'escape', 'contact/complete.tpl', 42, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr valign="top">
		<td align="right" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<table width="580" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/contact/title.jpg" width="580" height="40" alt="お問い合わせ"></td>
			</tr>
			<tr><td height="20"></td></tr>
			<tr>
				<td align="center">
				<table width="520" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr>
						<td align="center" bgcolor="#cccccc">
						<table width="510" border="0" cellspacing="0" cellpadding="0" summary=" ">
							<tr><td height="5"></td></tr>
							<tr>
								<td align="center" bgcolor="#ffffff">
								<!--お問い合わせ完了の文章ここから-->
								<table width="470" border="0" cellspacing="0" cellpadding="0" summary=" ">
									<tr><td height="25"></td></tr>
									<tr>
										<td class="fs14"><span class="redst">お問い合わせ内容の送信が完了いたしました。</span></td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td class="fs12">万一、ご回答メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせください。</td>
									</tr>
									<tr><td height="15"></td></tr>
									<tr>
										<td class="fs12">今後ともご愛顧賜りますようよろしくお願い申し上げます。</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr>
										<td class="fs12">
											<?php echo ((is_array($_tmp=$this->_tpl_vars['arrSiteInfo']['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
											TEL：<?php echo $this->_tpl_vars['arrSiteInfo']['tel01']; ?>
-<?php echo $this->_tpl_vars['arrSiteInfo']['tel02']; ?>
-<?php echo $this->_tpl_vars['arrSiteInfo']['tel03']; ?>
 <?php if ($this->_tpl_vars['arrSiteInfo']['business_hour'] != ""): ?>（受付時間/<?php echo $this->_tpl_vars['arrSiteInfo']['business_hour']; ?>
）<?php endif; ?><br>
											E-mail：<a href="mailto:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrSiteInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrSiteInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a>
										</td>
									</tr>
									<tr><td height="20"></td></tr>
									<tr align="center">
										<td>
											<?php if ($this->_tpl_vars['is_campaign']): ?>
											<a href="<?php echo @CAMPAIGN_URL;  echo $this->_tpl_vars['campaign_dir']; ?>
/index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage.gif','b_toppage');"><img src="<?php echo @URL_DIR; ?>
img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
											<?php else: ?>
											<a href="<?php echo @URL_DIR; ?>
index.php" onmouseover="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage_on.gif','b_toppage');" onmouseout="chgImg('<?php echo @URL_DIR; ?>
img/common/b_toppage.gif','b_toppage');"><img src="<?php echo @URL_DIR; ?>
img/common/b_toppage.gif" width="150" height="30" alt="トップページへ" border="0" name="b_toppage"></a>
											<?php endif; ?>
										</td>
									</tr>
									<tr><td height="25"></td></tr>
								</table>
								<!--お問い合わせ完了の文章ここまで-->
								</td>
							</tr>
							<tr><td height="5"></td></tr>
						</table>
						</td>
					</tr>
					
				</table>
				</td>
			</tr>
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->




