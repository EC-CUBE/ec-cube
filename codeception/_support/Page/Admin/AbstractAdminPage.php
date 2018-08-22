<?php


namespace Page\Admin;

use Codeception\Util\Fixtures;

abstract class AbstractAdminPage
{
    /** @var \AcceptanceTester $tester */
    protected $tester;

    /**
     * AbstractAdminPage constructor.
     */
    public function __construct(\AcceptanceTester $I)
    {
        $this->tester = $I;
    }

    /**
     * ページに移動。
     * @param $url string URL
     * @param $pageTitle string ページタイトル
     * @return $this
     */
    protected function goPage($url, $pageTitle)
    {
        $config = Fixtures::get('config');
        $this->tester->amOnPage('/'.$config['eccube_admin_route'].$url);
        return $this->atPage($pageTitle);
    }

    /**
     * ページに移動しているかどうか確認。
     * @param $pageTitle string ページタイトル
     * @return $this
     */
    protected function atPage($pageTitle)
    {
        $this->tester->see($pageTitle, '#main .page-header');
        return $this;
    }
}