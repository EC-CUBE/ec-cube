<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Tests\Web\Admin\Product;

use Eccube\Entity\Tag;
use Eccube\Repository\TagRepository;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TagContorllerTest extends AbstractAdminWebTestCase
{
    /**
     * @var TagRepository
     */
    private $TagRepo;

    public function setUp()
    {
        parent::setUp();
        $this->TagRepo = $this->container->get(TagRepository::class);
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
            array(),
            array(
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            )
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
            array(
                'admin_product_tag' => $formData
            )
        );

        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();
    }

    public function testRoutingEdit()
    {
        $Item = $this->TagRepo->find(1);
        $this->client->request('GET', $this->generateUrl('admin_product_tag_edit', array('id' => $Item->getId())));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * @param $isSuccess
     * @param $expected
     * @dataProvider dataSubmitProvider
     */
    public function testEdit($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['method'] = '';
        }

        $Item = $this->TagRepo->find(1);

        $this->client->request('POST',
            $this->generateUrl('admin_product_tag_edit', array('id' => $Item->getId())),
            array(
                'admin_product_tag' => $formData
            )
        );
        $this->expected = $expected;
        $this->actual = $this->client->getResponse()->isRedirection();
        $this->verify();

        $this->expected = 'Tag-101';
        $this->actual = $Item->getName();
        $this->verify();
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
            $this->generateUrl('admin_product_tag_delete', array('id' => $TagId))
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
            $this->generateUrl('admin_product_tag_delete', array('id' => $tagId))
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function createFormData()
    {

        $form = array(
            '_token' => 'dummy',
            'name' => 'Tag-101',
        );

        return $form;
    }

    public function dataSubmitProvider()
    {
        return array(
            array(false, false),
            array(true, true),
        );
    }
}
