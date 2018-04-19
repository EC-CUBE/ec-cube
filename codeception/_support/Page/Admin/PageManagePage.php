<?php


namespace Page\Admin;


class PageManagePage extends AbstractAdminPageStyleGuide
{

    /**
     * PageManagePage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        return $page->goPage('/content/page', 'ページ管理コンテンツ管理');
    }

    public function 新規入力()
    {
        $this->tester->click(['xpath' => '//a[text()="新規追加"]']);
    }

    public function ページ編集($pageName)
    {
        $this->tester->click(['xpath' => sprintf('//a[text()="%s"]', $pageName)]);
    }

    public function 削除($pageName)
    {
        $this->tester->click(['xpath'=> "//*[@id='list_page_tbl']/tbody/tr/td/a[contains(text(), '${pageName}')]/parent::node()/parent::node()/td[@class='align-middle pr-3']/div/div/a"]);
        $this->accept_削除($pageName);
    }

    public function accept_削除($pageName) {
        $this->tester->waitForElementVisible(['xpath' => "//*[@id='list_page_tbl']/tbody/tr/td/a[contains(text(), '${pageName}')]/parent::node()/parent::node()/td[@class='align-middle pr-3']/div/div/div[contains(@class, 'modal')]"]);
        $this->tester->click(['xpath' => "//*[@id='list_page_tbl']/tbody/tr/td/a[contains(text(), '${pageName}')]/parent::node()/parent::node()/td[@class='align-middle pr-3']/div/div/div[contains(@class, 'modal')]/div/div/div/a[contains(@class, 'btn-ec-delete')]"]);
    }
}