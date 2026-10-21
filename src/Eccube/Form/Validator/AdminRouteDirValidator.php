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

namespace Eccube\Form\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * AdminRouteDir 制約のバリデータ.
 */
class AdminRouteDirValidator extends ConstraintValidator
{
    public function __construct(private readonly Security $security, private readonly RouterInterface $router)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed $value
     */
    #[\Override]
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof AdminRouteDir) {
            throw new UnexpectedTypeException($constraint, AdminRouteDir::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_scalar($value) && !(\is_object($value) && method_exists($value, '__toString'))) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = (string) $value;

        // security.yaml の定義そのものに照合する. 認証を掛けない (security: false) ファイアウォールに
        // 管理画面配下のパスが一致する名前は拒否する. 照合は大文字・小文字を区別する (PathRequestMatcher).
        // / や & を含む値で URI の解釈が変わらないよう符号化する. PathRequestMatcher は復号してから照合する
        $firewall = $this->security->getFirewallConfig(Request::create('/'.rawurlencode($value).'/'));
        if (null !== $firewall && !$firewall->isSecurityEnabled()) {
            $this->context->buildViolation($constraint->unsecuredMessage)
                ->addViolation();

            return;
        }

        if (in_array($value, $this->getFrontRouteSegments(), true)) {
            $this->context->buildViolation($constraint->frontRouteMessage)
                ->addViolation();
        }
    }

    /**
     * 管理画面以外のルートのパスに含まれる名前を返す.
     *
     * @return array<string, string>
     */
    private function getFrontRouteSegments(): array
    {
        $segments = [];
        foreach ($this->router->getRouteCollection() as $routeName => $route) {
            $path = $route->getPath();
            // 管理画面以外
            if (false === stripos($routeName, 'admin')
                && false === stripos($path, '/_')
                && false === stripos($path, 'admin')
            ) {
                foreach (explode('/', $path) as $target) {
                    if ('' !== $target) {
                        $segments[$target] = $target;
                    }
                }
            }
        }

        return $segments;
    }
}
