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

namespace Eccube\Controller\Admin\Product;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Category;
use Eccube\Form\Type\Admin\CategoryFaqType;
use Eccube\Repository\CategoryRepository;
use Eccube\Util\CacheUtil;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CategoryFaqController extends AbstractController
{
    public function __construct(
        protected CategoryRepository $categoryRepository,
        private readonly CacheUtil $cacheUtil,
    ) {
    }

    /**
     * カテゴリごとFAQを編集する（1 カテゴリ専用ページ）。
     *
     * @return array<string, mixed>|RedirectResponse
     */
    #[Route(path: '/%eccube_admin_route%/product/category/{id}/faq', name: 'admin_product_category_faq', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Product/category_faq.twig')]
    public function index(Request $request, Category $Category): array|RedirectResponse
    {
        $form = $this->formFactory
            ->createBuilder(CategoryFaqType::class, $Category)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->save($Category);

            $this->addSuccess('admin.common.save_complete', 'admin');

            $this->cacheUtil->clearDoctrineCache();

            return $this->redirectToRoute('admin_product_category_faq', ['id' => $Category->getId()]);
        }

        return [
            'form' => $form->createView(),
            'Category' => $Category,
        ];
    }
}
