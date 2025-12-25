-- 既存データのクリーンアップ（念のためID:1を削除）
DELETE FROM dtb_customer WHERE id = 1;

-- データの挿入
-- リファレンスの定義(id, name01, email, point, status_id等)に合わせて構成
INSERT INTO dtb_customer (
    id,
    name01, name02,
    kana01, kana02,
    company_name,
    postal_code,
    addr01, addr02,
    email,
    phone_number,
    birth,
    password,
    salt,
    secret_key,
    point,
    buy_times,
    buy_total,
    note,
    reset_key,
    reset_expire,
    create_date, update_date,
    customer_status_id,
    sex_id,
    job_id,
    country_id,
    pref_id
) VALUES (
    1,                  -- id
    'ZAP', 'Test',      -- name01, name02
    'ザップ', 'テスト',   -- kana01, kana02
    NULL,               -- company_name
    '1000001',          -- postal_code
    '東京都千代田区',     -- addr01
    '千代田1-1',        -- addr02
    'zap@example.com',  -- email
    '03-0000-0000',     -- phone_number
    NULL,               -- birth
    'password',         -- password (ダミー: ログイン不要ならこれで十分)
    'salt',             -- salt
    'secret-key-dummy', -- secret_key
    0,                  -- point
    0,                  -- buy_times (NULLだと計算エラーになる場合があるため0)
    0,                  -- buy_total
    NULL,               -- note
    NULL,               -- reset_key
    NULL,               -- reset_expire
    NOW(), NOW(),       -- create_date, update_date
    1,                  -- customer_status_id (1:仮会員, 2:本会員など。環境によるが1で登録)
    1,                  -- sex_id (1:男性)
    1,                  -- job_id (1:公務員など)
    1,                  -- country_id (1:日本)
    13                  -- pref_id (13:東京)
);

-- シーケンスの更新 (カラム名が id なので、通常シーケンス名は dtb_customer_id_seq となります)
SELECT setval('dtb_customer_id_seq', (SELECT MAX(id) FROM dtb_customer));