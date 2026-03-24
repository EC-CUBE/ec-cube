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
    /** @var BasePolicy */
    private $securityPolicy;

    public function __construct(BasePolicy $securityPolicy)
    {
        $this->securityPolicy = $securityPolicy;
    }

    public function checkSecurity($tags, $filters, $functions): void
    {
        $this->securityPolicy->checkSecurity($tags, $filters, $functions);
    }

    public function checkMethodAllowed($obj, $method): void
    {
        // __toStringの場合はチェックをスキップする
        if ($method === '__toString') {
            return;
        }
        $this->securityPolicy->checkMethodAllowed($obj, $method);
    }

    public function checkPropertyAllowed($obj, $property): void
    {
        // Doctrine ORM 3.x の LazyGhostTrait が __isset() を実装しているため、
        // Twig 3.x がプロパティアクセスパスを経由するようになった。
        // ArrayAccess を実装しているオブジェクト（EC-CUBEエンティティ）の場合、
        // プロパティアクセスはゲッター経由の安全なアクセスと同等なので許可する。
        if ($obj instanceof \ArrayAccess) {
            return;
        }

        // ゲッターメソッドが存在する場合はメソッドアクセスとしてチェックする
        $getter = 'get' . ucfirst($property);
        if (method_exists($obj, $getter)) {
            $this->checkMethodAllowed($obj, $getter);

            return;
        }

        $this->securityPolicy->checkPropertyAllowed($obj, $property);
    }
}
