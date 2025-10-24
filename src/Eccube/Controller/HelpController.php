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

namespace Eccube\Controller;

use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Routing\Attribute\Route;

class HelpController extends AbstractController
{
    /**
     * ご利用ガイド.
     *
     * @return array<empty>
     */
    #[Route(path: '/help/guide', name: 'help_guide', methods: ['GET'])]
    #[Template(template: 'Help/guide.twig')]
    public function guide(): array
    {
        return [];
    }

    /**
     * 当サイトについて.
     *
     * @return array<empty>
     */
    #[Route(path: '/help/about', name: 'help_about', methods: ['GET'])]
    #[Template(template: 'Help/about.twig')]
    public function about(): array
    {
        return [];
    }

    /**
     * プライバシーポリシー.
     *
     * @return array<empty>
     */
    #[Route(path: '/help/privacy', name: 'help_privacy', methods: ['GET'])]
    #[Template(template: 'Help/privacy.twig')]
    public function privacy(): array
    {
        return [];
    }

    /**
     * 利用規約.
     *
     * @return array<empty>
     */
    #[Route(path: '/help/agreement', name: 'help_agreement', methods: ['GET'])]
    #[Template(template: 'Help/agreement.twig')]
    public function agreement(): array
    {
        return [];
    }
}
