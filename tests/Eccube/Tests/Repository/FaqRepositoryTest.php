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

namespace Eccube\Tests\Repository;

use Eccube\Entity\Category;
use Eccube\Entity\Faq;
use Eccube\Entity\Product;
use Eccube\Repository\FaqRepository;
use Eccube\Tests\EccubeTestCase;

final class FaqRepositoryTest extends EccubeTestCase
{
    private ?FaqRepository $faqRepository = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faqRepository = $this->entityManager->getRepository(Faq::class);
    }

    private function createFaq(string $question, int $sortNo, bool $visible = true, ?Product $Product = null, ?Category $Category = null): Faq
    {
        $Faq = new Faq();
        $Faq->setQuestion($question)
            ->setAnswer('answer of '.$question)
            ->setSortNo($sortNo)
            ->setVisible($visible)
            ->setProduct($Product)
            ->setCategory($Category);
        $this->entityManager->persist($Faq);
        $this->entityManager->flush();

        return $Faq;
    }

    public function testGetCommonFaqReturnsOnlyVisibleUnlinkedInSortOrder(): void
    {
        $Product = $this->createProduct();
        $Category = $this->entityManager->getRepository(Category::class)->findOneBy([]);

        $this->createFaq('common-b', 2);
        $this->createFaq('common-a', 1);
        $this->createFaq('common-hidden', 0, false);
        $this->createFaq('product-faq', 1, true, $Product);
        if ($Category) {
            $this->createFaq('category-faq', 1, true, null, $Category);
        }

        $result = $this->faqRepository->getCommonFaq();

        // 他テストの残留データに影響されないよう、本テストが作成した行だけに絞って検証する。
        $mine = ['common-a', 'common-b', 'common-hidden'];
        $questions = array_values(array_filter(
            array_map(static fn (Faq $f): ?string => $f->getQuestion(), $result),
            static fn (?string $q): bool => in_array($q, $mine, true)
        ));
        $this->assertSame(['common-a', 'common-b'], $questions);
    }

    public function testGetCommonFaqRespectsLimit(): void
    {
        $this->createFaq('limit-1', 1);
        $this->createFaq('limit-2', 2);
        $this->createFaq('limit-3', 3);

        // 上限を指定すると、その件数までに制限される（残留データがあっても超えない）。
        $this->assertCount(1, $this->faqRepository->getCommonFaq(1));
        $this->assertCount(2, $this->faqRepository->getCommonFaq(2));

        // 上限なしなら作成した3件以上が返る。
        $this->assertGreaterThanOrEqual(3, count($this->faqRepository->getCommonFaq()));
    }

    public function testGetProductFaq(): void
    {
        $Product = $this->createProduct();
        $this->createFaq('p-2', 2, true, $Product);
        $this->createFaq('p-1', 1, true, $Product);
        $this->createFaq('p-hidden', 0, false, $Product);
        $this->createFaq('common', 1);

        $result = $this->faqRepository->getProductFaq($Product);

        $questions = array_map(static fn (Faq $f): ?string => $f->getQuestion(), $result);
        $this->assertSame(['p-1', 'p-2'], $questions);
    }

    public function testGetCategoryFaq(): void
    {
        $Category = $this->entityManager->getRepository(Category::class)->findOneBy([]);
        $this->assertInstanceOf(Category::class, $Category);

        $this->createFaq('c-2', 2, true, null, $Category);
        $this->createFaq('c-1', 1, true, null, $Category);
        $this->createFaq('c-hidden', 0, false, null, $Category);
        $this->createFaq('common', 1);

        $result = $this->faqRepository->getCategoryFaq($Category);

        // 共有カテゴリを使うため、本テストが作成した行だけに絞って検証する。
        $mine = ['c-1', 'c-2', 'c-hidden'];
        $questions = array_values(array_filter(
            array_map(static fn (Faq $f): ?string => $f->getQuestion(), $result),
            static fn (?string $q): bool => in_array($q, $mine, true)
        ));
        $this->assertSame(['c-1', 'c-2'], $questions);
    }
}
