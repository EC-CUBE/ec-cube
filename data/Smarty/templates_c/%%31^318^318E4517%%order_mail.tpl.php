<?php /* Smarty version 2.6.13, created on 2007-01-10 00:06:20
         compiled from mail_templates/order_mail.tpl */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'number_format', 'mail_templates/order_mail.tpl', 15, false),array('modifier', 'default', 'mail_templates/order_mail.tpl', 15, false),array('modifier', 'sfPreTax', 'mail_templates/order_mail.tpl', 46, false),)), $this); ?>
<?php echo $this->_tpl_vars['arrOrder']['order_name01']; ?>
 <?php echo $this->_tpl_vars['arrOrder']['order_name02']; ?>
 様

<?php echo $this->_tpl_vars['tpl_header']; ?>


******************************************************************
　配送情報とご請求金額
******************************************************************

ご注文番号：<?php echo $this->_tpl_vars['arrOrder']['order_id']; ?>

お支払合計：￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['payment_total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

ご決済方法：<?php echo $this->_tpl_vars['arrOrder']['payment_method']; ?>

　お届け日：<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrOrder']['deliv_date'])) ? $this->_run_mod_handler('default', true, $_tmp, "指定なし") : smarty_modifier_default($_tmp, "指定なし")); ?>

お届け時間：<?php echo ((is_array($_tmp=@$this->_tpl_vars['arrOrder']['deliv_time'])) ? $this->_run_mod_handler('default', true, $_tmp, "指定なし") : smarty_modifier_default($_tmp, "指定なし")); ?>

メッセージ：<?php echo $this->_tpl_vars['Message_tmp']; ?>

◎お届け先
　お名前　：<?php echo $this->_tpl_vars['arrOrder']['deliv_name01']; ?>
 <?php echo $this->_tpl_vars['arrOrder']['deliv_name02']; ?>
　様
　郵便番号：〒<?php echo $this->_tpl_vars['arrOrder']['deliv_zip01']; ?>
-<?php echo $this->_tpl_vars['arrOrder']['deliv_zip02']; ?>

　ご住所　：<?php echo $this->_tpl_vars['arrOrder']['deliv_pref'];  echo $this->_tpl_vars['arrOrder']['deliv_addr01'];  echo $this->_tpl_vars['arrOrder']['deliv_addr02']; ?>

　電話番号：<?php echo $this->_tpl_vars['arrOrder']['deliv_tel01']; ?>
-<?php echo $this->_tpl_vars['arrOrder']['deliv_tel02']; ?>
-<?php echo $this->_tpl_vars['arrOrder']['deliv_tel03']; ?>


<?php if ($this->_tpl_vars['arrOther']['title']['value']): ?>
******************************************************************
　<?php echo $this->_tpl_vars['arrOther']['title']['name']; ?>
情報
******************************************************************

<?php $_from = $this->_tpl_vars['arrOther']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['key'] => $this->_tpl_vars['item']):
?>
<?php if ($this->_tpl_vars['key'] != 'title'): ?>
<?php if ($this->_tpl_vars['item']['name'] != ""):  echo $this->_tpl_vars['item']['name']; ?>
：<?php endif;  echo $this->_tpl_vars['item']['value']; ?>

<?php endif; ?>
<?php endforeach; endif; unset($_from); ?>
<?php endif; ?>

******************************************************************
　ご注文商品明細
******************************************************************

<?php unset($this->_sections['cnt']);
$this->_sections['cnt']['name'] = 'cnt';
$this->_sections['cnt']['loop'] = is_array($_loop=$this->_tpl_vars['arrOrderDetail']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['cnt']['show'] = true;
$this->_sections['cnt']['max'] = $this->_sections['cnt']['loop'];
$this->_sections['cnt']['step'] = 1;
$this->_sections['cnt']['start'] = $this->_sections['cnt']['step'] > 0 ? 0 : $this->_sections['cnt']['loop']-1;
if ($this->_sections['cnt']['show']) {
    $this->_sections['cnt']['total'] = $this->_sections['cnt']['loop'];
    if ($this->_sections['cnt']['total'] == 0)
        $this->_sections['cnt']['show'] = false;
} else
    $this->_sections['cnt']['total'] = 0;
if ($this->_sections['cnt']['show']):

            for ($this->_sections['cnt']['index'] = $this->_sections['cnt']['start'], $this->_sections['cnt']['iteration'] = 1;
                 $this->_sections['cnt']['iteration'] <= $this->_sections['cnt']['total'];
                 $this->_sections['cnt']['index'] += $this->_sections['cnt']['step'], $this->_sections['cnt']['iteration']++):
$this->_sections['cnt']['rownum'] = $this->_sections['cnt']['iteration'];
$this->_sections['cnt']['index_prev'] = $this->_sections['cnt']['index'] - $this->_sections['cnt']['step'];
$this->_sections['cnt']['index_next'] = $this->_sections['cnt']['index'] + $this->_sections['cnt']['step'];
$this->_sections['cnt']['first']      = ($this->_sections['cnt']['iteration'] == 1);
$this->_sections['cnt']['last']       = ($this->_sections['cnt']['iteration'] == $this->_sections['cnt']['total']);
?>
商品名: <?php echo $this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['product_name']; ?>
 <?php echo $this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['classcategory_name1']; ?>
 <?php echo $this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['classcategory_name2']; ?>

商品コード: <?php echo $this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['product_code']; ?>

数量：<?php echo $this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['quantity']; ?>
 個
金額：￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrderDetail'][$this->_sections['cnt']['index']]['price'])) ? $this->_run_mod_handler('sfPreTax', true, $_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule']) : sfPreTax($_tmp, $this->_tpl_vars['arrInfo']['tax'], $this->_tpl_vars['arrInfo']['tax_rule'])))) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)); ?>


<?php endfor; endif; ?>
-----------------------------------------------------------
小　計 ￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['subtotal'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 (うち消費税 ￥<?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['tax'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
）
値引き ￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['use_point']+$this->_tpl_vars['arrOrder']['discount'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

送　料 ￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['deliv_fee'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

手数料 ￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['charge'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

===============================================================
合　計 ￥ <?php echo ((is_array($_tmp=((is_array($_tmp=$this->_tpl_vars['arrOrder']['payment_total'])) ? $this->_run_mod_handler('number_format', true, $_tmp) : number_format($_tmp)))) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>

===============================================================

ご使用ポイント <?php echo ((is_array($_tmp=@$this->_tpl_vars['arrOrder']['use_point'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 pt
今回加算される加算ポイント <?php echo ((is_array($_tmp=@$this->_tpl_vars['arrOrder']['add_point'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 pt
保持ポイント <?php echo ((is_array($_tmp=@$this->_tpl_vars['arrCustomer']['point'])) ? $this->_run_mod_handler('default', true, $_tmp, 0) : smarty_modifier_default($_tmp, 0)); ?>
 pt

<?php echo $this->_tpl_vars['tpl_footer']; ?>