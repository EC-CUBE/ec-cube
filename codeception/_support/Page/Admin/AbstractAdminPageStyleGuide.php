<?php


namespace Page\Admin;

use Codeception\Util\Fixtures;

abstract class AbstractAdminPageStyleGuide extends AbstractAdminPage
{
    /**
     * ページに移動しているかどうか確認。
     * @param $pageTitle string ページタイトル
     * @return $this
     */
    protected function atPage($pageTitle)
    {
        $this->tester->see($pageTitle, '.c-pageTitle');
        return $this;
    }
}