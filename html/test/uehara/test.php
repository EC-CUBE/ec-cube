<?php
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

?>