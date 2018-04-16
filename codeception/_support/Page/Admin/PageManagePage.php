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
        $this->tester->click(['xpath' => "//*[@id='sortable_list_box__list']//div[@class='item_box tr']/div[@class='item_pattern td']/a[contains(text(),'${pageName}')]/parent::node()/parent::node()/div[@class='icon_edit td']/div/a"]);
        $this->tester->click(['xpath' => "//*[@id='sortable_list_box__list']//div[@class='item_box tr']/div[@class='item_pattern td']/a[contains(text(),'${pageName}')]/parent::node()/parent::node()/div[@class='icon_edit td']/div/ul/li[2]/a"]);
    }
}