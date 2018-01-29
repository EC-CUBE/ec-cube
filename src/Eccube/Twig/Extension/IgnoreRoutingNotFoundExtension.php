<?php

namespace Eccube\Twig\Extension;

use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class IgnoreRoutingNotFoundExtension extends RoutingExtension
{
    /**
     * bind から URL へ変換します。
     * \Symfony\Bridge\Twig\Extension\RoutingExtension::getPath の処理を拡張し、
     * RouteNotFoundException 発生時に 文字列 "/404?bind={bind}" を返します。
     *
     * @param string $name
     * @param array $parameters
     * @param bool $relative
     * @return string
     */
    public function getPath($name, $parameters = array(), $relative = false)
    {
        try {
            return parent::getPath($name, $parameters, $relative);
        } catch (RouteNotFoundException $e) {

            log_warning($e->getMessage(), ['exception' => $e]);

            return parent::getPath('homepage').'404?bind='.$name;
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
     * @return string
     */
    public function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        try {
            return parent::getUrl($name, $parameters, $schemeRelative);
        } catch (RouteNotFoundException $e) {

            log_warning($e->getMessage(), ['exception' => $e]);

            return parent::getUrl('homepage').'404?bind='.$name;
        }
    }
}