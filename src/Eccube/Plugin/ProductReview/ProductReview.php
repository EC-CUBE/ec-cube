<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Plugin\ProductReview;

use Eccube\Application;
use Eccube\Framework\Helper\PluginHelper;
use Eccube\Plugin\ProductReview\Helper\ReviewHelper;
use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ProductReview implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        $app->match('/products/review.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Products\\Review')->bind('products_review');
        $app->match('/products/review_complete.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Products\\ReviewComplete')->bind('products_review_complete');

        $app->match('/admin/products/review.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Admin\\Products\\Review')->bind('admin_products_review');
        $app->match('/admin/products/review_edit.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Admin\\Products\\ReviewEdit')->bind('admin_products_review_edit');

        $app['eccube.helper.review'] = $app->share(function () use ($app) {
            return new ReviewHelper();
        });

        $app['eccube.helper.plugin'] = $app->extend('eccube.helper.plugin', function (PluginHelper $objHelperPlugin, $app) {
            $objHelperPlugin->addAction("Eccube.Page.Products.Detail.action_after", function ($objPage) use ($app) {
                //レビュー情報の取得
                $objReview = Application::alias('eccube.helper.review');
                $objPage->arrReview = $objReview->getListByProductId($objPage->tpl_product_id);
            });

            return $objHelperPlugin;
        });

        // TODO: 管理画面メニューに追加
    }

    public function boot(BaseApplication $app)
    {
    }
}
