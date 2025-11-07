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

namespace Eccube\Twig\Extension;

use Eccube\Common\Constant;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CsrfExtension extends AbstractExtension
{
    /**
     * CsrfExtension constructor.
     */
    public function __construct(protected CsrfTokenManagerInterface $tokenManager)
    {
    }

    /**
     * @return array<int, TwigFunction>
     */
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csrf_token_for_anchor', $this->getCsrfTokenForAnchor(...), ['is_safe' => ['all']]),
        ];
    }

    public function getCsrfTokenForAnchor(): string
    {
        $token = $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();

        return 'token-for-anchor=\''.$token.'\'';
    }

    public function getCsrfToken(): string
    {
        return $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();
    }
}
