<?php

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\PageLayoutRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Eccube\Repository\LayoutRepository;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Entity\Layout;
use Eccube\Entity\Page;
use Eccube\Entity\PageLayout;
use Eccube\Repository\PageRepository;

class LayoutControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var PageLayoutRepository
     */
    private $PageLayoutRepo;

    /**
     * @var LayoutRepository
     */
    protected $layoutRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;


    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->PageLayoutRepo = $this->container->get(PageLayoutRepository::class);
        $this->layoutRepository = $this->container->get(LayoutRepository::class);
        $this->deviceTypeRepository = $this->container->get(DeviceTypeRepository::class);
        $this->pageRepository = $this->container->get(PageRepository::class);
    }

    public function testIndex()
    {
        $this->client->request('GET', $this->generateUrl('admin_content_layout'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMoveSortNo()
    {
        $data['newSortNos'] = [
            '2-3' => 5,
            '3-3' => 6,
            '4-3' => 7,
        ];
        $data['targetLayoutId'] = 3;
        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_sort_no_move'),
            $data,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );
        $this->expected = 7;
        $this->actual = $this->PageLayoutRepo->findOneBy(['page_id' => 4, 'layout_id' => 3])->getSortNo();
        $this->verify();
    }

    public function testMoveSortNoToOtherList()
    {
        $dataSortNo['newSortNos'] = [
            '1-2' => 5,
            '2-3' => 6,
            '3-3' => 7,
        ];
        $dataSortNo['targetLayoutId'] = 3;
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
        $this->expected = 5;
        $this->actual = $this->PageLayoutRepo->findOneBy(['page_id' => 1, 'layout_id' => 3])->getSortNo();
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
                    'DeviceType' => DeviceType::DEVICE_TYPE_PC
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

    public function testIndexWithNew()
    {
        $this->client->request(
            'GET',
            $this->generateUrl(
                'admin_content_layout_new',
                ['DeviceType' => DeviceType::DEVICE_TYPE_PC]
            )
        );
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    public function testIndexWithInvalid()
    {
        $this->client->request(
            'GET',
            $this->generateUrl(
                'admin_content_layout_new',
                ['DeviceType' => 999]
            )
        );
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function testIndexWithDeviceNotFound()
    {
        $this->client->request(
            'GET',
            $this->generateUrl(
                'admin_content_layout_new'
            )
        );
        $this->assertTrue($this->client->getResponse()->isClientError());
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

    public function testDeleteSuccess()
    {
        $PcDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        $Layout = new Layout();
        $Layout->setName('Layout for unit test');
        $Layout->setDeviceType($PcDeviceType);
        $this->layoutRepository->save($Layout);

        $this->entityManager->flush();

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_content_layout_delete', ['id' => $Layout->getId()])
        );
        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/削除が完了しました。/u', $crawler->filter('div.alert-success')->text());
    }

    public function testDeleteFail()
    {
        $PcDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);
        $Layout = new Layout();
        $Layout->setName('Layout for unit test');
        $Layout->setDeviceType($PcDeviceType);
        $this->layoutRepository->save($Layout);
        $this->entityManager->flush();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_delete', ['id' => $Layout->getId()])
        );
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_content_layout_delete', ['id' => 0])
        );
        $this->assertTrue($this->client->getResponse()->isNotFound());
    }

    public function testDeleteFailByPages()
    {
        $PcDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        $Layout = new Layout();
        $Layout->setName('Layout for unit test');
        $Layout->setDeviceType($PcDeviceType);
        $this->layoutRepository->save($Layout);
        $this->entityManager->flush();

        $Page = new Page();
        $Page->setDeviceType($PcDeviceType);
        $Page->setName('Page for unit test');
        $Page->setUrl('layout-test-delete-fail');
        $this->pageRepository->save($Page);
        $this->entityManager->flush();

        $PageLayout = new PageLayout();
        $PageLayout->setLayout($Layout);
        $PageLayout->setLayoutId($Layout->getId());
        $PageLayout->setPage($Page);
        $PageLayout->setPageId($Page->getId());
        $PageLayout->setSortNo(1);
        $this->PageLayoutRepo->save($PageLayout);
        $this->entityManager->flush();

        $Layout->addPageLayout($PageLayout);
        $this->entityManager->flush();

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_content_layout_delete', ['id' => $Layout->getId()])
        );
        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/既に削除されています。/u', $crawler->filter('div.alert-warning')->text());
    }
}
