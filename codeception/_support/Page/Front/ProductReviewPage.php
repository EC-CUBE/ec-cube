<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Page\Front;

class ProductReviewPage extends AbstractFrontPage
{
    public static function at($I)
    {
        $page = new self($I);
        $page->tester->seeInCurrentUrl('/product_review');

        return $page;
    }

    public function 入力_投稿者名($value)
    {
        $this->tester->fillField(['id' => 'product_review_reviewer_name'], $value);
        return $this;
    }

    public function 入力_URL($value)
    {
        $this->tester->fillField(['id' => 'product_review_reviewer_url'], $value);
        return $this;
    }

    public function 入力_性別()
    {
        $this->tester->scrollTo(['id' => 'product_review_sex_1']);
        $this->tester->click(['id' => 'product_review_sex_1']);
        return $this;
    }

    public function 入力_おすすめレベル()
    {
        $this->tester->scrollTo(['id' => 'product_review_recommend_level_0']);
        $this->tester->click(['id' => 'product_review_recommend_level_0']);
        return $this;
    }

    public function 入力_タイトル($value)
    {
        $this->tester->fillField(['id' => 'product_review_title'], $value);
        return $this;
    }

    public function 入力_コメント($value)
    {
        $this->tester->fillField(['id' => 'product_review_comment'], $value);
        return $this;
    }

    public function 確認ページへ()
    {
        $this->tester->scrollTo(['css' => '#page_product_review_index > div.ec-layoutRole > div.ec-layoutRole__contents > div > div > div.ec-off1Grid > div > form > div.ec-registerRole__actions > div > div > button']);
        $this->tester->click(['css' => '#page_product_review_index > div.ec-layoutRole > div.ec-layoutRole__contents > div > div > div.ec-off1Grid > div > form > div.ec-registerRole__actions > div > div > button']);
        return $this;
    }


    public function 投稿する()
    {
        $this->tester->wait(3);
        $this->tester->scrollTo(['css' => '#page_product_review_index > div.ec-layoutRole > div.ec-layoutRole__contents > div > div > div.ec-off1Grid > div > form > div.ec-registerRole__actions > div > div > button']);
        $this->tester->click(['css' => '#page_product_review_index > div.ec-layoutRole > div.ec-layoutRole__contents > div > div > div.ec-off1Grid > div > form > div.ec-registerRole__actions > div > div > button']);
        return $this;
    }
}
