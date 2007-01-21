<?php /* Smarty version 2.6.13, created on 2007-01-10 00:02:49
         compiled from mail_templates/customer_mail.tpl */ ?>
　※本メールは自動配信メールです。
　等幅フォント(MSゴシック12ポイント、Osaka-等幅など)で
　最適にご覧になれます。

┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
　※本メールは、
　<?php echo $this->_tpl_vars['CONF']['shop_name']; ?>
より会員登録を希望された方に
　お送りしています。
　もしお心当たりが無い場合はこのままこのメールを破棄していただ
　ければ会員登録はなされません。
　またその旨<?php echo $this->_tpl_vars['CONF']['email02']; ?>
まで
　ご連絡いただければ幸いです。
┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛

<?php echo $this->_tpl_vars['to_name01']; ?>
 <?php echo $this->_tpl_vars['to_name02']; ?>
 様

<?php echo $this->_tpl_vars['CONF']['shop_name']; ?>
でございます。

この度は会員登録依頼をいただきまして、有り難うございます。

現在は仮登録の状態です。
　　　~~~~~~
本会員登録を完了するには下記URLにアクセスしてください。
※入力されたお客様の情報はSSL暗号化通信により保護されます。

<?php echo @SSL_URL; ?>
regist/index.php?mode=regist&id=<?php echo $this->_tpl_vars['uniqid'];  echo $this->_tpl_vars['etc_value']; ?>


上記URLにて本会員登録が完了いたしましたら改めてご登録内容ご確認
メールをお送り致します。


