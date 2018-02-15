<?php return [
    [
        'id' => 'product',
        'name' => 'nav.label.product',
        'has_child' => true,
        'icon' => 'cb-tag',
        'child' =>
            [
                [
                    'id' => 'product_master',
                    'name' => 'nav.label.product_master',
                    'url' => 'admin_product',
                ],
                [
                    'id' => 'product_edit',
                    'name' => 'nav.label.add_product',
                    'url' => 'admin_product_product_new',
                ],
                [
                    'id' => 'class_name',
                    'name' => 'nav.label.add_option',
                    'url' => 'admin_product_class_name',
                ],
                [
                    'id' => 'class_category',
                    'name' => 'nav.label.add_categories',
                    'url' => 'admin_product_category',
                ],
                [
                    'id' => 'product_csv_import',
                    'name' => 'nav.label.import_product_csv',
                    'url' => 'admin_product_csv_import',
                ],
                [
                    'id' => 'category_csv_import',
                    'name' => 'nav.label.import_category_csv',
                    'url' => 'admin_product_category_csv_import',
                ],
            ],
    ],
    [
        'id' => 'order',
        'name' => 'nav.label.orders',
        'has_child' => true,
        'icon' => 'cb-shopping-cart',
        'child' =>
            [
                [
                    'id' => 'order_master',
                    'name' => 'nav.label.order_master',
                    'url' => 'admin_order',
                ],
                [
                    'id' => 'order_edit',
                    'name' => 'nav.label.add_order',
                    'url' => 'admin_order_new',
                ],
            ],
    ],
    [
        'id' => 'shipping',
        'name' => 'nav.label.shipments',
        'has_child' => true,
        'icon' => 'cb-shopping-cart',
        'child' =>
            [
                [
                    'id' => 'shipping_master',
                    'name' => 'nav.label.shipping_master',
                    'url' => 'admin/shipping',
                ],
                [
                    'id' => 'shipping_edit',
                    'name' => 'nav.label.add_shipment',
                    'url' => 'admin/shipping/new',
                ],
            ],
    ],
    [
        'id' => 'customer',
        'name' => 'nav.label.customers',
        'has_child' => true,
        'icon' => 'cb-users',
        'child' =>
            [
                [
                    'id' => 'customer_master',
                    'name' => 'nav.label.customer_master',
                    'url' => 'admin_customer',
                ],
                [
                    'id' => 'customer_edit',
                    'name' => 'nav.label.add_customer',
                    'url' => 'admin_customer_new',
                ],
            ],
    ],
    [
        'id' => 'content',
        'name' => 'nav.label.contents',
        'has_child' => true,
        'icon' => 'cb-file-text',
        'child' =>
            [
                [
                    'id' => 'news',
                    'name' => 'nav.label.whats_new',
                    'url' => 'admin_content_news',
                ],
                [
                    'id' => 'file',
                    'name' => 'nav.label.files',
                    'url' => 'admin_content_file',
                ],
                [
                    'id' => 'layout',
                    'name' => 'nav.label.laytouts',
                    'url' => 'admin_content_layout',
                ],
                [
                    'id' => 'page',
                    'name' => 'nav.label.pages',
                    'url' => 'admin_content_page',
                ],
                [
                    'id' => 'block',
                    'name' => 'nav.label.blocks',
                    'url' => 'admin_content_block',
                ],
                [
                    'id' => 'cache',
                    'name' => 'nav.label.caches',
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
                                'name' => 'nav.label.company_info_shopping-guide',
                                'url' => 'admin_setting_shop_tradelaw',
                            ],
                            [
                                'id' => 'customer_agreement',
                                'name' => 'nav.label.terms_conditions',
                                'url' => 'admin_setting_shop_customer_agreement',
                            ],
                            [
                                'id' => 'shop_payment',
                                'name' => 'nav.label.payment_method',
                                'url' => 'admin_setting_shop_payment',
                            ],
                            [
                                'id' => 'shop_delivery',
                                'name' => 'nav.label.delivery_methods',
                                'url' => 'admin_setting_shop_delivery',
                            ],
                            [
                                'id' => 'shop_tax',
                                'name' => 'nav.label.tax_rates',
                                'url' => 'admin_setting_shop_tax',
                            ],
                            [
                                'id' => 'shop_mail',
                                'name' => 'nav.label.email',
                                'url' => 'admin_setting_shop_mail',
                            ],
                            [
                                'id' => 'shop_csv',
                                'name' => 'nav.label.csv_export_settings',
                                'url' => 'admin_setting_shop_csv',
                            ],
                        ],
                ],
                [
                    'id' => 'system',
                    'name' => 'nav.label.system_info_settings',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'system_index',
                                'name' => 'nav.label.system_info',
                                'url' => 'admin_setting_system_system',
                            ],
                            [
                                'id' => 'member',
                                'name' => 'nav.label.member_management',
                                'url' => 'admin_setting_system_member',
                            ],
                            [
                                'id' => 'authority',
                                'name' => 'nav.label.authorizationgs',
                                'url' => 'admin_setting_system_authority',
                            ],
                            [
                                'id' => 'security',
                                'name' => 'nav.label.security',
                                'url' => 'admin_setting_system_security',
                            ],
                            [
                                'id' => 'log',
                                'name' => 'nav.label.eccube_logs',
                                'url' => 'admin_setting_system_log',
                            ],
                            [
                                'id' => 'masterdata',
                                'name' => 'nav.label.master',
                                'url' => 'admin_setting_system_masterdata',
                            ],
                        ],
                ],
            ],
    ],
    [
        'id' => 'store',
        'name' => 'nav.label.owner_store',
        'has_child' => true,
        'icon' => 'cb-info-circle',
        'child' =>
            [
                [
                    'id' => 'plugin',
                    'name' => 'nav.label.plugins',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'plugin_list',
                                'name' => 'nav.label.all_plugins',
                                'url' => 'admin_store_plugin',
                            ],
//          [
//            'id' => 'plugin_owners_install',
//            'name' => '購入済プラグイン',
//            'url' => 'admin_store_plugin_owners_install',
//          ],
                            [
                                'id' => 'plugin_search',
                                'name' => 'nav.label.search_plugins',
                                'url' => 'admin_store_plugin_owners_search',
                            ],
                            [
                                'id' => 'plugin_handler',
                                'name' => 'nav.label.advanced_setting',
                                'url' => 'admin_store_plugin_handler',
                            ],
                        ],
                ],
                [
                    'id' => 'template',
                    'name' => 'nav.label.templates',
                    'has_child' => true,
                    'child' =>
                        [
                            [
                                'id' => 'template_list',
                                'name' => 'nav.label.all_templates',
                                'url' => 'admin_store_template',
                            ],
                            [
                                'id' => 'template_install',
                                'name' => 'nav.label.uploads',
                                'url' => 'admin_store_template_install',
                            ],
                        ],
                ],
                [
                    'id' => 'authentication_setting',
                    'name' => 'nav.label.auth_key',
                    'url' => 'admin_store_authentication_setting',
                ],
            ],
    ],
];
