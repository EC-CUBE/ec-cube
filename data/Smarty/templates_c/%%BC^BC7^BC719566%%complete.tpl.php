<?php /* Smarty version 2.6.13, created on 2007-01-10 00:06:20
         compiled from shopping/complete.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'nl2br', 'shopping/complete.tpl', 44, false),array('modifier', 'escape', 'shopping/complete.tpl', 63, false),)), $this); ?>
<!--▼CONTENTS-->
<table width="760" border="0" cellspacing="0" cellpadding="0" summary=" ">
	<tr>
		<td align="center" bgcolor="#ffffff">
		<!--▼MAIN ONTENTS-->
		<!--購入手続きの流れ-->
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/shopping/flow04.gif" width="700" height="36" alt="購入手続きの流れ"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		<!--購入手続きの流れ-->
			
		<table width="700" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td><img src="<?php echo @URL_DIR; ?>
img/shopping/complete_title.jpg" width="700" height="40" alt="ご注文完了"></td>
			</tr>
			<tr><td height="15"></td></tr>
		</table>
		
		<table width="640" border="0" cellspacing="0" cellpadding="0" summary=" ">
			<tr>
				<td align="center" bgcolor="#cccccc">
				<table width="630" border="0" cellspacing="0" cellpadding="0" summary=" ">
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<!-- ▼その他決済情報を表示する場合は表示 -->
							<?php if ($this->_tpl_vars['arrOther']['title']['value']): ?>
							<table  width="590" cellspacing="0" cellpadding="0" summary=" ">
								<tr>
									<td>
									<table cellspacing="0" cellpadding="0" summary=" " id="comp">
										<tr><td height="20"></td></tr>
										<tr>
											<td class="fs12">■<?php echo $this->_tpl_vars['arrOther']['title']['name']; ?>
情報<br />
											<?php $_from = $this->_tpl_vars['arrOther']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
											<?php if ($this->_tpl_vars['key'] != 'title'):  if ($this->_tpl_vars['item']['name'] != ""):  echo $this->_tpl_vars['item']['name']; ?>
：<?php endif;  echo ((is_array($_tmp=$this->_tpl_vars['item']['value'])) ? $this->_run_mod_handler('nl2br', true, $_tmp) : smarty_modifier_nl2br($_tmp)); ?>
<br/><?php endif; ?>
											<?php endforeach; endif; unset($_from); ?>
										</tr>
									</table>
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr><td height="5"></td></tr>
					<tr>
						<td align="center" bgcolor="#ffffff">
							<?php endif; ?>						
							<!-- ▲コンビに決済の場合には表示 -->
						
							<!--ご注文完了の文章ここから-->
							<table width="590" border="0" cellspacing="0" cellpadding="0" summary=" ">
								<tr><td height="25"></td></tr>
								<tr>
									<td class="fs12"><span class="redst"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrInfo']['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
の商品をご購入いただき、ありがとうございました。</span></td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12">ただいま、ご注文の確認メールをお送りさせていただきました。 <br>
									万一、ご確認メールが届かない場合は、トラブルの可能性もありますので大変お手数ではございますがもう一度お問い合わせいただくか、お電話にてお問い合わせくださいませ。 </td>
								</tr>
								<tr><td height="15"></td></tr>
								<tr>
									<td class="fs12">今後ともご愛顧賜りますようよろしくお願い申し上げます。</td>
								</tr>
								<tr><td height="20"></td></tr>
								<tr>
									<td class="fs12"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrInfo']['company_name'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
<br>
									TEL：<?php echo $this->_tpl_vars['arrInfo']['tel01']; ?>
-<?php echo $this->_tpl_vars['arrInfo']['tel02']; ?>
-<?php echo $this->_tpl_vars['arrInfo']['tel03']; ?>
 <?php if ($this->_tpl_vars['arrInfo']['business_hour'] != ""): ?>（受付時間/<?php echo $this->_tpl_vars['arrInfo']['business_hour']; ?>
）<?php endif; ?><br>
									E-mail：<a href="mailto:<?php echo ((is_array($_tmp=$this->_tpl_vars['arrInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
"><?php echo ((is_array($_tmp=$this->_tpl_vars['arrInfo']['email02'])) ? $this->_run_mod_handler('escape', true, $_tmp) : smarty_modifier_escape($_tmp)); ?>
</a></td>
								</tr>
								<tr><td height="25"></td></tr>
							</table>
							<!--ご注文完了の文章ここまで-->
						</td>
					</tr>
					<tr><td height="5"></td></tr>
				</table>
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
		</table>
		<!--▲MAIN ONTENTS-->
		</td>
	</tr>
</table>
<!--▲CONTENTS-->