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

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\Tag;
use Eccube\Repository\TagRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TagControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var TagRepository
     */
    private $TagRepo;

    public function setUp()
    {
        parent::setUp();
        $this->TagRepo = $this->entityManager->getRepository(\Eccube\Entity\Tag::class);
    }

    public function testRouting()
    {
        $this->client->request('GET', $this->generateUrl('admin_product_tag'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMoveSortNo()
    {
        $idAndSortNo = [
            1 => 4,
            2 => 5,
            3 => 6,
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_product_tag_sort_no_move'),
            $idAndSortNo,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->expected = 6;
        $this->actual = $this->TagRepo->find(3)->getSortNo();
        $this->verify();
    }

    /**
     * @param $isSuccess
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testAddNew($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['method'] = '';
        }

        $this->client->request('POST',
            $this->generateUrl('admin_product_tag'),
            [
                'admin_product_tag' => $formData,
            ]
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function testEdit()
    {
        $formData = $this->createFormData();

        $Item = $this->TagRepo->find(1);

        $this->client->request('POST',
            $this->generateUrl('admin_product_tag'),
            [
                'tag_'.$Item->getId() => $formData,
            ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $this->expected = 'Tag-101';
        $this->actual = $Item->getName();
        $this->verify();
    }

    public function testEditInvalid()
    {
        $Item = $this->TagRepo->find(1);

        $crawler = $this->client->request('POST',
            $this->generateUrl('admin_product_tag'),
            [
                'tag_'.$Item->getId() => [
                    '_token' => 'dummy',
                    'name' => '',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertContains('入力されていません', $crawler->html());
    }

    public function testDeleteSuccess()
    {
        $Item = new Tag();
        $Item->setName('Tag-102')
            ->setSortNo(999);

        $this->entityManager->persist($Item);
        $this->entityManager->flush();

        $TagId = $Item->getId();
        $this->client->request('DELETE',
            $this->generateUrl('admin_product_tag_delete', ['id' => $TagId])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Item = $this->TagRepo->find($TagId);
        $this->assertNull($Item);
    }

    public function testDeleteFailNotFound()
    {
        $tagId = 9999;
        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_product_tag_delete', ['id' => $tagId])
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function createFormData()
    {
        $form = [
            '_token' => 'dummy',
            'name' => 'Tag-101',
        ];

        return $form;
    }

    public function dataSubmitProvider()
    {
        return [
            [false, false],
            [true, true],
        ];
    }
}
