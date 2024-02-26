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

use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\Node\Expression\ArrayExpression;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;
use Twig\TwigFunction;

/**
 * \Symfony\Bridge\Twig\Extension\RoutingExtension の拡張です.
 * Symfony5 より \Symfony\Bridge\Twig\Extension\RoutingExtension が final になったため, 各メソッドを移植しています.
 */
class IgnoreRoutingNotFoundExtension extends AbstractExtension
{
    /** @var UrlGeneratorInterface */
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('url', [$this, 'getUrl'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
            new TwigFunction('path', [$this, 'getPath'], ['is_safe_callback' => [$this, 'isUrlGenerationSafe']]),
        ];
    }

    /**
     * bind から URL へ変換します。
     * \Symfony\Bridge\Twig\Extension\RoutingExtension::getPath の処理を拡張し、
     * RouteNotFoundException 発生時に 文字列 "/404?bind={bind}" を返します。
     *
     * @param string $name
     * @param array $parameters
     * @param bool $relative
     *
     * @return string
     */
    public function getPath($name, $parameters = [], $relative = false)
    {
        try {
            return $this->generator->generate($name, $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH);
        } catch (RouteNotFoundException $e) {
            log_warning($e->getMessage(), ['exception' => $e]);

            return $this->generator->generate('homepage', $parameters, $relative ? UrlGeneratorInterface::RELATIVE_PATH : UrlGeneratorInterface::ABSOLUTE_PATH).'404?bind='.$name;
        }
    }

    /**
     * bind から URL へ変換します。
     * \Symfony\Bridge\Twig\Extension\RoutingExtension::getUrl の処理を拡張し、
     * RouteNotFoundException 発生時に 文字列 "/404?bind={bind}" を返します。
     *
     * @param string $name
     * @param array $parameters
     * @param bool $schemeRelative
     *
     * @return string
     */
    public function getUrl($name, $parameters = [], $schemeRelative = false)
    {
        try {
            return $this->generator->generate($name, $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL);
        } catch (RouteNotFoundException $e) {
            log_warning($e->getMessage(), ['exception' => $e]);

            return $this->generator->generate('homepage', $parameters, $schemeRelative ? UrlGeneratorInterface::NETWORK_PATH : UrlGeneratorInterface::ABSOLUTE_URL).'404?bind='.$name;
        }
    }

    /**
     * @param Node $argsNode The arguments of the path/url function
     *
     * @return array An array with the contexts the URL is safe
     *
     * @see \Symfony\Bridge\Twig\Extension\RoutingExtension
     */
    public function isUrlGenerationSafe(Node $argsNode): array
    {
        // support named arguments
        $paramsNode = $argsNode->hasNode('parameters') ? $argsNode->getNode('parameters') : (
            $argsNode->hasNode(1) ? $argsNode->getNode(1) : null
        );

        if (null === $paramsNode || $paramsNode instanceof ArrayExpression && \count($paramsNode) <= 2 &&
            (!$paramsNode->hasNode(1) || $paramsNode->getNode(1) instanceof ConstantExpression)
        ) {
            return ['html'];
        }

        return [];
    }
}
