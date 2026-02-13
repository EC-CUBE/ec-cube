#!/bin/bash
set -euo pipefail

psql -v ON_ERROR_STOP=1 <<'SQL'
-- ★CI安定化：データを“毎回同じ状態”に戻す
TRUNCATE
  dtb_customer,
  dtb_customer_address,
  dtb_order,
  dtb_order_item
RESTART IDENTITY CASCADE;
SQL