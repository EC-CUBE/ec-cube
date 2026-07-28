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

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Repository\FaqRepository;
use Eccube\Service\FaqStructuredDataService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Attribute\Route;

class FaqController extends AbstractController
{
    public function __construct(
        private readonly FaqRepository $faqRepository,
        private readonly FaqStructuredDataService $faqStructuredDataService,
    ) {
    }

    /**
     * サイト共通FAQブロック。
     *
     * @return array<string, mixed>
     */
    #[Route(path: '/block/faq', name: 'block_faq', methods: ['GET'])]
    #[Template(template: 'Block/faq.twig')]
    public function index(): array
    {
        $Faqs = $this->faqRepository->getCommonFaq();

        return [
            'Faqs' => $Faqs,
            'faq_json_ld' => $this->faqStructuredDataService->createFaqPageJsonLd($Faqs),
        ];
    }
}
