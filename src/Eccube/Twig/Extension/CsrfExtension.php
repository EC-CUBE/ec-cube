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
     * @var CsrfTokenManagerInterface
     */
    protected $tokenManager;

    /**
     * CsrfExtension constructor.
     *
     * @param CsrfTokenManagerInterface $tokenManager
     */
    public function __construct(CsrfTokenManagerInterface $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('csrf_token_for_anchor', [$this, 'getCsrfTokenForAnchor'], ['is_safe' => ['all']]),
        ];
    }

    /**
     * @return string
     */
    public function getCsrfTokenForAnchor()
    {
        $token = $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();

        return 'token-for-anchor=\''.$token.'\'';
    }

    /**
     * @return string
     */
    public function getCsrfToken()
    {
        return $this->tokenManager->getToken(Constant::TOKEN_NAME)->getValue();
    }
}
