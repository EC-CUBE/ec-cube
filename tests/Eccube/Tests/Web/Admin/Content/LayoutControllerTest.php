<?php

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Repository\PageLayoutRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class LayoutControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var PageLayoutRepository
     */
    private $PageLayoutRepo;

    public function setUp()
    {
        parent::setUp();
        $this->PageLayoutRepo = $this->container->get(PageLayoutRepository::class);
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_layout'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMoveSortNo()
    {
        $dataSortNo = [
            '1-1' => 5,
            '1-2' => 6,
            '2-3' => 7,
        ];
        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_sort_no_move'),
            $dataSortNo,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->expected = 7;
        $this->actual = $this->PageLayoutRepo->findOneBy(['page_id' => 2, 'layout_id' => 3])->getSortNo();
        $this->verify();
    }

    public function testIndexWithPost()
    {
        $crawler = $this->client->request(
            'POST',
            $this->generateUrl(
                'admin_content_layout_edit',
                array('id' => 1)
            ),
            array(
                'form' => array(
                    '_token' => 'dummy',
                    'name' => 'テストレイアウト',
                    'DeviceType' => 10
                ),
                'name_1' => 'カゴの中',
                'block_id_1' => 2,
                'section_1' => 2,
                'block_row_1' => 2,
                'name_2' => '商品検索',
                'block_id_2' => 3,
                'section_2' => 3,
                'block_row_2' => 3,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->generateUrl('admin_content_layout_edit', array('id' => 1))
        ));
    }

    public function testIndexWithPostPreview()
    {
        // FIXME プレビュー機能が実装されたら有効にする
        $this->markTestIncomplete('Layout Preview is not implemented.');

        $crawler = $this->client->request(
            'POST',
            $this->app->url(
                'admin_content_layout_preview',
                array('id' => 1)
            ),
            array(
                'form' => array(
                    '_token' => 'dummy'
                ),
                'name_1' => 'カゴの中',
                'id_1' => 2,
                'section_1' => 2,
                'top_1' => 2,
                'name_2' => '商品検索',
                'id_2' => 3,
                'section_2' => 3,
                'top_2' => 3,
            )
        );
        $this->assertTrue($this->client->getResponse()->isRedirect(
            $this->app->url('homepage').'?preview=1'
        ));
    }
}
