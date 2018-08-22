<?php


namespace Page\Admin;


class NewsEditPage extends AbstractAdminPage
{

    /**
     * NewsRegisterPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function of($I)
    {
        $page = new self($I);
        $page->atPage('コンテンツ管理新着情報管理');
        $page->tester->see('新着情報登録・編集', '#aside_wrap > div.col-md-9 > div.box > div > h3');
        return $page;
    }

    public function 入力_日付($value)
    {
        $this->tester->executeJS("$('#admin_news_publish_date').val('".$value."').change();");
        return $this;
    }

    public function 入力_タイトル($value)
    {
        $this->tester->fillField(['id' => 'admin_news_title'], $value);
        return $this;
    }

    public function 入力_本文($value)
    {
        $this->tester->fillField(['id' => 'admin_news_description'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click('#aside_column > div > div > div > div > div > button');
        return $this;
    }
}