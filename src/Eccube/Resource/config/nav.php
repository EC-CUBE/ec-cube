<?php return [
    [
        'id' => 'product',
        'name' => '商品管理',
        'has_child' => true,
        'icon' => 'cb-tag',
        'child' =>
            [
                [
                    'id' => 'product_master',
                    'name' => '商品マスター',
                    'url' => 'admin_product',
                ],
                [
                    'id' => 'product_edit',
                    'name' => '商品登録',
                    'url' => 'admin_product_product_new',
                ],
                [
                    'id' => 'class_name',
                    'name' => '規格登録',
                    'url' => 'admin_product_class_name',
                ],
                [
                    'id' => 'class_category',
                    'name' => 'カテゴリ登録',
                    'url' => 'admin_product_category',
                ],
                [
                    'id' => 'product_csv_import',
                    'name' => '商品CSV登録',
                    'url' => 'admin_product_csv_import',
                ],
                [
                    'id' => 'category_csv_import',
                    'name' => 'カテゴリCSV登録',
                    'url' => 'admin_product_category_csv_import',
                ],
            ],
    ],
    [
        'id' => 'order',
        'name' => '受注管理',
        'has_child' => true,
        'icon' => 'cb-shopping-cart',
        'child' =>
            [
                [
                    'id' => 'order_master',
                    'name' => '受注マスター',
                    'url' => 'admin_order',
                ],
                [
                    'id' => 'order_edit',
                    'name' => '受注登録',
                    'url' => 'admin_order_new',
                ],
            ],
    ],
    [
        'id' => 'shipping',
        'name' => '出荷管理',
        'has_child' => true,
        'icon' => 'cb-shopping-cart',
        'child' =>
            [
                [
                    'id' => 'shipping_master',
                    'name' => '出荷マスター',
                    'url' => 'admin/shipping',
                ],
                [
                    'id' => 'shipping_edit',
                    'name' => '出荷登録',
                    'url' => 'admin/shipping/new',
                ],
            ],
    ],
    [
        'id' => 'customer',
        'name' => '会員管理',
        'has_child' => true,
        'icon' => 'cb-users',
        'child' =>
            [
                [
                    'id' => 'customer_master',
                    'name' => '会員マスター',
                    'url' => 'admin_customer',
                ],
                [
                    'id' => 'customer_edit',
                    'name' => '会員登録',
                    'url' => 'admin_customer_new',
                ],
            ],
    ],
    [
        'id' => 'content',
        'name' => 'コンテンツ管理',
        'has_child' => true,
        'icon' => 'cb-file-text',
        'child' =>
            [
                [
                    'id' => 'news',
                    'name' => '新着情報管理',
                    'url' => 'admin_content_news',
                ],
                [
                    'id' => 'file',
                    'name' => 'ファイル管理',
                    'url' => 'admin_content_file',
                ],
                [
                    'id' => 'layout',
                    'name' => 'レイアウト管理',
                    'url' => 'admin_content_layout',
                ],
                [
                    'id' => 'page',
                    'name' => 'ページ管理',
                    'url' => 'admin_content_page',
                ],
                [
                    'id' => 'block',
                    'name' => 'ブロック管理',
                    'url' => 'admin_content_block',
                ],
                [
                    'id' => 'cache',
                    'name' => 'キャッシュ管理',
                    'url' => 'admin_content_cache',
                ],
            ],
    ],
    [
        'id' => 'setting',
        'name' => 'admin.nav.setting',
        'has_child' => true,
        'icon' => 'cb-cog',
        'child' =>
            [
                [
                    'id' => 'shop',
                    'name' => 'admin.nav.setting_shop',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'shop_index',
                                'name' => 'admin.nav.setting_shop_shop_master',
                                'url' => 'admin_setting_shop',
                            ],
                            [
                                'id' => 'tradelaw',
                                'name' => '特定商取引法',
                                'url' => 'admin_setting_shop_tradelaw',
                            ],
                            [
                                'id' => 'customer_agreement',
                                'name' => '利用規約設定',
                                'url' => 'admin_setting_shop_customer_agreement',
                            ],
                            [
                                'id' => 'shop_payment',
                                'name' => '支払方法設定',
                                'url' => 'admin_setting_shop_payment',
                            ],
                            [
                                'id' => 'shop_delivery',
                                'name' => '配送方法設定',
                                'url' => 'admin_setting_shop_delivery',
                            ],
                            [
                                'id' => 'shop_tax',
                                'name' => '税率設定',
                                'url' => 'admin_setting_shop_tax',
                            ],
                            [
                                'id' => 'shop_mail',
                                'name' => 'メール設定',
                                'url' => 'admin_setting_shop_mail',
                            ],
                            [
                                'id' => 'shop_csv',
                                'name' => 'CSV出力項目設定',
                                'url' => 'admin_setting_shop_csv',
                            ],
                        ],
                ],
                [
                    'id' => 'system',
                    'name' => 'システム情報設定',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'system_index',
                                'name' => 'システム情報',
                                'url' => 'admin_setting_system_system',
                            ],
                            [
                                'id' => 'member',
                                'name' => 'メンバー管理',
                                'url' => 'admin_setting_system_member',
                            ],
                            [
                                'id' => 'authority',
                                'name' => '権限管理',
                                'url' => 'admin_setting_system_authority',
                            ],
                            [
                                'id' => 'security',
                                'name' => 'セキュリティ管理',
                                'url' => 'admin_setting_system_security',
                            ],
                            [
                                'id' => 'log',
                                'name' => 'EC-CUBE ログ表示',
                                'url' => 'admin_setting_system_log',
                            ],
                            [
                                'id' => 'masterdata',
                                'name' => 'マスターデータ管理',
                                'url' => 'admin_setting_system_masterdata',
                            ],
                        ],
                ],
            ],
    ],
    [
        'id' => 'store',
        'name' => 'オーナーズストア',
        'has_child' => true,
        'icon' => 'cb-info-circle',
        'child' =>
            [
                [
                    'id' => 'plugin',
                    'name' => 'プラグイン',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'plugin_list',
                                'name' => 'プラグイン一覧',
                                'url' => 'admin_store_plugin',
                            ],
//          [
//            'id' => 'plugin_owners_install',
//            'name' => '購入済プラグイン',
//            'url' => 'admin_store_plugin_owners_install',
//          ],
                            [
                                'id' => 'plugin_search',
                                'name' => 'プラグインを探す',
                                'url' => 'admin_store_plugin_owners_search',
                            ],
                            [
                                'id' => 'plugin_handler',
                                'name' => '高度な設定',
                                'url' => 'admin_store_plugin_handler',
                            ],
                        ],
                ],
                [
                    'id' => 'template',
                    'name' => 'テンプレート',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'template_list',
                                'name' => 'テンプレート一覧',
                                'url' => 'admin_store_template',
                            ],
                            [
                                'id' => 'template_install',
                                'name' => 'アップロード',
                                'url' => 'admin_store_template_install',
                            ],
                        ],
                ],
                [
                    'id' => 'authentication_setting',
                    'name' => '認証キー設定',
                    'url' => 'admin_store_authentication_setting',
                ],
            ],
    ],
];
