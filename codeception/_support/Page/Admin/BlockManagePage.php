<?php


namespace Page\Admin;


class BlockManagePage extends AbstractAdminPageStyleGuide
{

    /**
     * BlockManagePage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        return $page->goPage('/content/block', 'ブロック管理コンテンツ管理');
    }

    public function 新規入力()
    {
        $this->tester->click('#page_admin_content_block > div > div.c-contentsArea > div.c-contentsArea__cols > div > div.card.rounded.border-0 > div > div > a');
    }

    public function 編集($rowNum)
    {
        $this->tester->click("#page_admin_content_block > div > div.c-contentsArea > div.c-contentsArea__cols > div > div.c-primaryCol > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a:nth-child(1)");
    }

    public function 削除($rowNum)
    {
        $this->tester->click("#page_admin_content_block > div > div.c-contentsArea > div.c-contentsArea__cols > div > div.c-primaryCol > div > div > div > ul > li:nth-child(${rowNum}) > div > div.col-auto.text-right > a.btn.btn-ec-actionIcon.mr-3.disabled");
    }

}
