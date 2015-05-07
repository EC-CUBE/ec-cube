<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
        $app->match('/products/review.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Products\\Review')->bind('product_review');
        $app->match('/products/review_complete.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Products\\ReviewComplete')->bind('product_review_complete');

        $app->match('/admin/products/review.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Admin\\Products\\Review')->bind('admin_product_review');
        $app->match('/admin/products/review_edit.php', '\\Eccube\\Plugin\\ProductReview\\Page\\Admin\\Products\\ReviewEdit')->bind('admin_product_review_edit');

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
