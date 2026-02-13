#!/bin/bash
set -euo pipefail

psql -v ON_ERROR_STOP=1 <<'SQL'
-- ★CI安定化：データを“毎回同じ状態”に戻す
TRUNCATE
  dtb_customer,
  dtb_customer_address,
  dtb_order,
  dtb_order_item,
  dtb_page_layout,
  dtb_page,
  dtb_delivery_fee,
  dtb_payment_option,
  dtb_delivery,
  dtb_payment,
  dtb_block_position,
  dtb_block,
  dtb_layout,
  dtb_category,
  dtb_class_category
RESTART IDENTITY CASCADE;
SQL