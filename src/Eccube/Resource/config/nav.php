<?php return [
  0 => 
  [
    'id' => 'product',
    'name' => '商品管理',
    'has_child' => true,
    'icon' => 'cb-tag',
    'child' => 
    [
      0 => 
      [
        'id' => 'product_master',
        'name' => '商品マスター',
        'url' => 'admin_product',
      ],
      1 => 
      [
        'id' => 'product_edit',
        'name' => '商品登録',
        'url' => 'admin_product_product_new',
      ],
      2 => 
      [
        'id' => 'class_name',
        'name' => '規格登録',
        'url' => 'admin_product_class_name',
      ],
      3 => 
      [
        'id' => 'class_category',
        'name' => 'カテゴリ登録',
        'url' => 'admin_product_category',
      ],
      4 => 
      [
        'id' => 'product_csv_import',
        'name' => '商品CSV登録',
        'url' => 'admin_product_csv_import',
      ],
      5 => 
      [
        'id' => 'category_csv_import',
        'name' => 'カテゴリCSV登録',
        'url' => 'admin_product_category_csv_import',
      ],
    ],
  ],
  1 => 
  [
    'id' => 'order',
    'name' => '受注管理',
    'has_child' => true,
    'icon' => 'cb-shopping-cart',
    'child' => 
    [
      0 => 
      [
        'id' => 'order_master',
        'name' => '受注マスター',
        'url' => 'admin_order',
      ],
      1 => 
      [
        'id' => 'order_edit',
        'name' => '受注登録',
        'url' => 'admin_order_new',
      ],
    ],
  ],
  2 => 
  [
    'id' => 'shipping',
    'name' => '出荷管理',
    'has_child' => true,
    'icon' => 'cb-shopping-cart',
    'child' => 
    [
      0 => 
      [
        'id' => 'shipping_master',
        'name' => '出荷マスター',
        'url' => 'admin/shipping',
      ],
      1 => 
      [
        'id' => 'shipping_edit',
        'name' => '出荷登録',
        'url' => 'admin/shipping/new',
      ],
    ],
  ],
  3 => 
  [
    'id' => 'customer',
    'name' => '会員管理',
    'has_child' => true,
    'icon' => 'cb-users',
    'child' => 
    [
      0 => 
      [
        'id' => 'customer_master',
        'name' => '会員マスター',
        'url' => 'admin_customer',
      ],
      1 => 
      [
        'id' => 'customer_edit',
        'name' => '会員登録',
        'url' => 'admin_customer_new',
      ],
    ],
  ],
  4 => 
  [
    'id' => 'content',
    'name' => 'コンテンツ管理',
    'has_child' => true,
    'icon' => 'cb-file-text',
    'child' => 
    [
      0 => 
      [
        'id' => 'news',
        'name' => '新着情報管理',
        'url' => 'admin_content_news',
      ],
      1 => 
      [
        'id' => 'file',
        'name' => 'ファイル管理',
        'url' => 'admin_content_file',
      ],
      2 => 
      [
        'id' => 'layout',
        'name' => 'レイアウト管理',
        'url' => 'admin_content_layout',
      ],
      3 => 
      [
        'id' => 'page',
        'name' => 'ページ管理',
        'url' => 'admin_content_page',
      ],
      4 => 
      [
        'id' => 'block',
        'name' => 'ブロック管理',
        'url' => 'admin_content_block',
      ],
      5 => 
      [
        'id' => 'cache',
        'name' => 'キャッシュ管理',
        'url' => 'admin_content_cache',
      ],
    ],
  ],
  5 => 
  [
    'id' => 'setting',
    'name' => '設定',
    'has_child' => true,
    'icon' => 'cb-cog',
    'child' => 
    [
      0 => 
      [
        'id' => 'shop',
        'name' => '基本情報設定',
        'has_child' => true,
        'child' => 
        [
          0 => 
          [
            'id' => 'shop_index',
            'name' => 'ショップマスター',
            'url' => 'admin_setting_shop',
          ],
          1 => 
          [
            'id' => 'tradelaw',
            'name' => '特定商取引法',
            'url' => 'admin_setting_shop_tradelaw',
          ],
          2 => 
          [
            'id' => 'customer_agreement',
            'name' => '利用規約設定',
            'url' => 'admin_setting_shop_customer_agreement',
          ],
          3 => 
          [
            'id' => 'shop_payment',
            'name' => '支払方法設定',
            'url' => 'admin_setting_shop_payment',
          ],
          4 => 
          [
            'id' => 'shop_delivery',
            'name' => '配送方法設定',
            'url' => 'admin_setting_shop_delivery',
          ],
          5 => 
          [
            'id' => 'shop_tax',
            'name' => '税率設定',
            'url' => 'admin_setting_shop_tax',
          ],
          6 => 
          [
            'id' => 'shop_mail',
            'name' => 'メール設定',
            'url' => 'admin_setting_shop_mail',
          ],
          7 => 
          [
            'id' => 'shop_csv',
            'name' => 'CSV出力項目設定',
            'url' => 'admin_setting_shop_csv',
          ],
        ],
      ],
      1 => 
      [
        'id' => 'system',
        'name' => 'システム情報設定',
        'has_child' => true,
        'child' => 
        [
          0 => 
          [
            'id' => 'system_index',
            'name' => 'システム情報',
            'url' => 'admin_setting_system_system',
          ],
          1 => 
          [
            'id' => 'member',
            'name' => 'メンバー管理',
            'url' => 'admin_setting_system_member',
          ],
          2 => 
          [
            'id' => 'authority',
            'name' => '権限管理',
            'url' => 'admin_setting_system_authority',
          ],
          3 => 
          [
            'id' => 'security',
            'name' => 'セキュリティ管理',
            'url' => 'admin_setting_system_security',
          ],
          4 => 
          [
            'id' => 'log',
            'name' => 'EC-CUBE ログ表示',
            'url' => 'admin_setting_system_log',
          ],
          5 => 
          [
            'id' => 'masterdata',
            'name' => 'マスターデータ管理',
            'url' => 'admin_setting_system_masterdata',
          ],
        ],
      ],
    ],
  ],
  6 => 
  [
    'id' => 'store',
    'name' => 'オーナーズストア',
    'has_child' => true,
    'icon' => 'cb-info-circle',
    'child' => 
    [
      0 => 
      [
        'id' => 'plugin',
        'name' => 'プラグイン',
        'has_child' => true,
        'child' => 
        [
          0 => 
          [
            'id' => 'plugin_list',
            'name' => 'プラグイン一覧',
            'url' => 'admin_store_plugin',
          ],
          1 => 
          [
            'id' => 'plugin_owners_install',
            'name' => '購入済プラグイン',
            'url' => 'admin_store_plugin_owners_install',
          ],
          2 => 
          [
            'id' => 'plugin_handler',
            'name' => '高度な設定',
            'url' => 'admin_store_plugin_handler',
          ],
        ],
      ],
      1 => 
      [
        'id' => 'template',
        'name' => 'テンプレート',
        'has_child' => true,
        'child' => 
        [
          0 => 
          [
            'id' => 'template_list',
            'name' => 'テンプレート一覧',
            'url' => 'admin_store_template',
          ],
          1 => 
          [
            'id' => 'template_install',
            'name' => 'アップロード',
            'url' => 'admin_store_template_install',
          ],
        ],
      ],
      2 => 
      [
        'id' => 'authentication_setting',
        'name' => '認証キー設定',
        'url' => 'admin_store_authentication_setting',
      ],
    ],
  ],
];
