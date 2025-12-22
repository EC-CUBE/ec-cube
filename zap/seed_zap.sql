-- 既に空の前提（truncate後）
-- dtb_customer の必須カラムに合わせて INSERT を調整すること
INSERT INTO dtb_customer (id, name01, name02, kana01, kana02, email, password, salt, status, create_date, update_date)
VALUES (
  1,
  '姓',
  '名',
  'セイ',
  'メイ',
  'user@example.com',
  'dummy',  -- ここは環境の必須に合わせる
  'dummy',
  2,
  now(),
  now()
);

-- シーケンスを 1 の次に合わせる（テーブル/シーケンス名は実環境に合わせて）
SELECT setval('dtb_customer_id_seq', (SELECT max(id) FROM dtb_customer));
