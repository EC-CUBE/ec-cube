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

class TopController extends AbstractController
{
    /**
     * @return array<empty>
     */
    #[Route(path: '/', name: 'homepage', methods: ['GET'])]
    #[Template(template: 'index.twig')]
    public function index(): array
    {
        return [];
    }
}
