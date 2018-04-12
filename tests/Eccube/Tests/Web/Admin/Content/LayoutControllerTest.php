<?php

namespace Eccube\Tests\Web\Admin\Content;

use Eccube\Repository\PageLayoutRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Repository\LayoutRepository;
use Eccube\Entity\Master\DeviceType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Entity\Layout;

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
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->PageLayoutRepo = $this->container->get(PageLayoutRepository::class);
        $this->layoutRepository = $this->container->get(LayoutRepository::class);
        $this->deviceTypeRepository = $this->container->get(DeviceTypeRepository::class);
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

    public function testLayoutSetDefaultSuccess()
    {
        $PcDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        /** @var Layout $defaultLayout */
        $defaultLayout = $this->layoutRepository->findOneBy([
            'default_layout' => 1,
            'DeviceType' => $PcDeviceType
        ]);
        $this->assertNotNull($defaultLayout);

        /** @var Layout $normalLayout */
        $normalLayout = $this->layoutRepository->findOneBy([
            'default_layout' => 0,
            'DeviceType' => $PcDeviceType
        ]);
        $this->assertNotNull($normalLayout);

        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_default', ['id' => $normalLayout->getId()]),
            ['_token' => 'dummy']
        );
        $crawler = $this->client->followRedirect();

        $this->assertRegExp('/登録が完了しました。/u', $crawler->filter('div.alert-success')->text());

        $this->assertEquals(1, $normalLayout->getDefaultLayout());
        $this->assertEquals(0, $defaultLayout->getDefaultLayout());
    }

    public function testLayoutSetDefaultFail()
    {
        $SpDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_SP);
        $PcDeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        $SpLayout = new Layout();
        $SpLayout->setDeviceType($SpDeviceType);
        $SpLayout->setName('SP Layout for Unit Test');
        $this->layoutRepository->save($SpLayout);

        $PcLayout = new Layout();
        $PcLayout->setDeviceType($PcDeviceType);
        $PcLayout->setName('PC Layout for Unit Test');
        $this->layoutRepository->save($PcLayout);

        $this->entityManager->flush();

        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_default', ['id' => 0]),
            ['_token' => 'dummy']
        );
        $this->assertTrue($this->client->getResponse()->isNotFound());

        $this->client->request(
            'GET',
            $this->generateUrl('admin_content_layout_default', ['id' => $PcDeviceType->getId()]),
            ['_token' => 'dummy']
        );
        $this->assertEquals(405, $this->client->getResponse()->getStatusCode());

        $this->client->request(
            'POST',
            $this->generateUrl('admin_content_layout_default', ['id' => $SpLayout->getId()]),
            ['_token' => 'dummy']
        );
        $crawler = $this->client->followRedirect();
        $this->assertRegExp('/登録できませんでした。/u', $crawler->filter('div.alert-danger')->text());
    }
}
