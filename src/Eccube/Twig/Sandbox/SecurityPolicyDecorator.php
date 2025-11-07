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

namespace Eccube\Twig\Sandbox;

use Twig\Sandbox\SecurityPolicy as BasePolicy;
use Twig\Sandbox\SecurityPolicyInterface;

class SecurityPolicyDecorator implements SecurityPolicyInterface
{
    public function __construct(private readonly BasePolicy $securityPolicy)
    {
    }

    /**
     * @param array<mixed> $tags
     * @param array<mixed> $filters
     * @param array<mixed> $functions
     *
     * @throws \Twig\Sandbox\SecurityError
     */
    #[\Override]
    public function checkSecurity($tags, $filters, $functions): void
    {
        $this->securityPolicy->checkSecurity($tags, $filters, $functions);
    }

    /**
     * @param mixed $obj
     * @param string $method
     *
     * @throws \Twig\Sandbox\SecurityNotAllowedMethodError
     */
    #[\Override]
    public function checkMethodAllowed($obj, $method): void
    {
        // __toStringの場合はチェックをスキップする
        if ($method === '__toString') {
            return;
        }
        $this->securityPolicy->checkMethodAllowed($obj, $method);
    }

    /**
     * @param mixed $obj
     * @param string $method
     *
     * @throws \Twig\Sandbox\SecurityNotAllowedPropertyError
     */
    #[\Override]
    public function checkPropertyAllowed($obj, $method): void
    {
        $this->securityPolicy->checkPropertyAllowed($obj, $method);
    }
}
