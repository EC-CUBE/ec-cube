<?php

declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class TagControllerTest extends AbstractAdminWebTestCase
{
    private ?TagRepository $TagRepo = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->TagRepo = $this->entityManager->getRepository(Tag::class);
    }

    public function testRouting()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('admin_product_tag'));
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
            Request::METHOD_POST,
            $this->generateUrl('admin_product_tag_sort_no_move'),
            $idAndSortNo,
            [],
            [
                'HTTP_X-Requested-With' => 'XMLHttpRequest',
                'CONTENT_TYPE' => 'application/json',
            ]
        );

        $this->expected = 6;
        $Tag = $this->TagRepo->find(3);
        $this->entityManager->refresh($Tag);
        $this->assertInstanceOf(Tag::class, $Tag); // Refresh しないとリクエストの値(string)が入ってしまう
        $this->actual = $Tag->getSortNo();
        $this->verify();
    }

    /**
     * @param $isSuccess
     * @param $expected
     */
    #[DataProvider(methodName: 'dataSubmitProvider')]
    public function testAddNew($isSuccess, $expected)
    {
        $formData = $this->createFormData();
        if (!$isSuccess) {
            $formData['method'] = '';
        }

        $this->client->request(Request::METHOD_POST,
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
        $this->assertInstanceOf(Tag::class, $Item);

        $this->client->request(Request::METHOD_POST,
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
        $this->assertInstanceOf(Tag::class, $Item);

        $crawler = $this->client->request(Request::METHOD_POST,
            $this->generateUrl('admin_product_tag'),
            [
                'tag_'.$Item->getId() => [
                    '_token' => 'dummy',
                    'name' => '',
                ],
            ]
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
        $this->assertStringContainsString('入力されていません', $crawler->html());
    }

    public function testDeleteSuccess()
    {
        $Item = new Tag();
        $Item->setName('Tag-102')
            ->setSortNo(999);

        $this->entityManager->persist($Item);
        $this->entityManager->flush();

        $TagId = $Item->getId();
        $this->client->request(Request::METHOD_DELETE,
            $this->generateUrl('admin_product_tag_delete', ['id' => $TagId])
        );

        $this->assertTrue($this->client->getResponse()->isRedirection());

        $Item = $this->TagRepo->find($TagId);
        $this->assertNotInstanceOf(Tag::class, $Item);
    }

    public function testDeleteFailNotFound()
    {
        $tagId = 9999;
        $this->client->request(
            Request::METHOD_DELETE,
            $this->generateUrl('admin_product_tag_delete', ['id' => $tagId])
        );
        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    public function createFormData()
    {
        return [
            '_token' => 'dummy',
            'name' => 'Tag-101',
        ];
    }

    public static function dataSubmitProvider(): \Iterator
    {
        yield [false, false];
        yield [true, true];
    }

    /**
     * ID・表示順が整数でない場合は 400 にする.
     *
     * @param array<mixed> $sortNos
     */
    #[DataProvider(methodName: 'provideInvalidSortNos')]
    public function testMoveSortNoWithInvalidValue(array $sortNos): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_tag_sort_no_move'),
            $sortNos,
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }

    /**
     * @return \Iterator<string, array{array<mixed>}>
     */
    public static function provideInvalidSortNos(): \Iterator
    {
        yield 'alpha id' => [['abc' => '1']];
        yield 'out of range id' => [['2147483648' => '1']];
        yield 'alpha sort_no' => [['1' => 'abc']];
        yield 'array sort_no' => [['1' => ['1']]];
    }

    /**
     * 存在しない ID は無視する.
     */
    public function testMoveSortNoWithNotFoundId(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_tag_sort_no_move'),
            ['2147483647' => '1'],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * sort_no は smallint のため、範囲外の表示順は 400 にする.
     */
    public function testMoveSortNoWithOutOfRangeSortNo(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('admin_product_tag_sort_no_move'),
            ['1' => '32768'],
            [],
            ['HTTP_X-Requested-With' => 'XMLHttpRequest']
        );

        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode(), (string) $this->client->getResponse()->getContent());
    }
}
