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

namespace Eccube\Plugin\ProductReview\Page\Products;

use Eccube\Application;
use Eccube\Page\AbstractPage;

/**
 * お客様の声投稿完了 のページクラス.
 *
 * @package Page
 * @author LOCKON CO.,LTD.
 */
class ReviewComplete extends AbstractPage
{
    /**
     * Page を初期化する.
     *
     * @return void
     */
    public function init()
    {
        parent::init();
    }

    /**
     * Page のプロセス.
     *
     * @return void
     */
    public function process()
    {
        parent::process();
        $this->action();
        $this->sendResponse();
    }

    /**
     * Page のAction.
     *
     * @return void
     */
    public function action()
    {
        $this->setTemplate('products/review_complete.tpl');
    }
}
