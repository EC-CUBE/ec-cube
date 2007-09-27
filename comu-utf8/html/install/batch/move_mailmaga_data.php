<?php
/*
 * Copyright(c) 2000-2007 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 */
require_once("../../require.php");

$objQuery = new SC_Query();

$objQuery->begin();
$arrCustomerMail = $objQuery->getAll("
UPDATE dtb_customer
SET mailmaga_flg = (
SELECT mail_flag
FROM dtb_customer_mail
WHERE dtb_customer.email = dtb_customer_mail.email
)");
$objQuery->commit();

echo "正常に移行が完了致しました。";

?>