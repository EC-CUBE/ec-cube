<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Twig\Extension;

use Eccube\Common\Constant;
use Eccube\Service\TaxRuleService;
use Eccube\Util\StringUtil;
use Silex\Application;
use Symfony\Bridge\Twig\Extension\RoutingExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class EccubeExtension extends \Twig_Extension
{
    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        $RoutingExtension = $this->app['twig']->getExtension(RoutingExtension::class);

        $app = $this->app;
        return array(
            new \Twig_SimpleFunction('has_errors', array($this, 'hasErrors')),
            new \Twig_SimpleFunction('is_object', array($this, 'isObject')),
            new \Twig_SimpleFunction('calc_inc_tax', array($this, 'getCalcIncTax')),
            new \Twig_SimpleFunction('active_menus', array($this, 'getActiveMenus')),
            new \Twig_SimpleFunction('csrf_token_for_anchor', array($this, 'getCsrfTokenForAnchor'), array('is_safe' => array('all'))),

            // Override: \Symfony\Bridge\Twig\Extension\RoutingExtension::url
            new \Twig_SimpleFunction('url', array($this, 'getUrl'), array('is_safe_callback' => array($RoutingExtension, 'isUrlGenerationSafe'))),
            // Override: \Symfony\Bridge\Twig\Extension\RoutingExtension::path
            new \Twig_SimpleFunction('path', array($this, 'getPath'), array('is_safe_callback' => array($RoutingExtension, 'isUrlGenerationSafe'))),

            new \Twig_SimpleFunction('php_*', function() {
                    $arg_list = func_get_args();
                    $function = array_shift($arg_list);
                    if (is_callable($function)) {
                        return call_user_func_array($function, $arg_list);
                    }
                    trigger_error('Called to an undefined function : php_'. $function, E_USER_WARNING);

            }, ['pre_escape' => 'html', 'is_safe' => ['html']]),

            new \Twig_SimpleFunction('eccube_block_*', function() use ($app) {
                    $sources = $app['eccube.twig.block.templates'];
                    $arg_list = func_get_args();
                    $block_name = array_shift($arg_list);
                    foreach ($sources as $source) {
                        $template = $app['twig']->loadTemplate($source);
                        if (!isset($arg_list[0])) {
                            $arg_list[0] = [];
                        }
                        if ($template->hasBlock($block_name, $arg_list[0])) {
                            echo $result = $template->renderBlock($block_name, $arg_list[0]);
                            return;
                        }
                    }
                    trigger_error($block_name.' block is not found', E_USER_WARNING);
            }, ['pre_escape' => 'html', 'is_safe' => ['html']])
        );
    }

    /**
     * Returns a list of filters.
     *
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('no_image_product', array($this, 'getNoImageProduct')),
            new \Twig_SimpleFilter('date_format', array($this, 'getDateFormatFilter')),
            new \Twig_SimpleFilter('price', array($this, 'getPriceFilter')),
            new \Twig_SimpleFilter('ellipsis', array($this, 'getEllipsis')),
            new \Twig_SimpleFilter('time_ago', array($this, 'getTimeAgo')),
        );
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getName()
    {
        return 'eccube';
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getCalcIncTax($price, $tax_rate, $tax_rule)
    {
        return $price + $this->app[TaxRuleService::class]->calcTax($price, $tax_rate, $tax_rule);
    }

    /**
     * Name of this extension
     *
     * @param array $menus
     * @return array
     */
    public function getActiveMenus($menus = array())
    {
        $count = count($menus);
        for ($i = $count; $i <= 2; $i++) {
            $menus[] = '';
        }

        return $menus;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getCsrfTokenForAnchor()
    {
        $token = $this->app['csrf.token_manager']->getToken(Constant::TOKEN_NAME)->getValue();
        return 'token-for-anchor=\'' . $token . '\'';
    }

    /**
     * return No Image filename
     *
     * @return string
     */
    public function getNoImageProduct($image)
    {
        return empty($image) ? 'no_image_product.jpg' : $image;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getDateFormatFilter($date, $value = '', $format = 'Y/m/d')
    {
        if (is_null($date)) {
            return $value;
        } else {
            return $date->format($format);
        }
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getPriceFilter($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',')
    {
        $locale = $this->app['config']['locale'];
        $currency = $this->app['config']['currency'];

        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency($number, $currency);
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getEllipsis($value, $length = 100, $end = '...')
    {
        return StringUtil::ellipsis($value, $length, $end);
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getTimeAgo($date)
    {
        return StringUtil::timeAgo($date);
    }

    /**
     * bind から URL へ変換します。
     * \Symfony\Bridge\Twig\Extension\RoutingExtension::getPath の処理を拡張し、
     * RouteNotFoundException 発生時に E_USER_WARNING を発生させ、
     * 文字列 "/404?bind={bind}" を返します。
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $relative
     * @return string URL
     */
    public function getPath($name, $parameters = array(), $relative = false)
    {
        $RoutingExtension = $this->app['twig']->getExtension(RoutingExtension::class);
        try {
            return $RoutingExtension->getPath($name, $parameters, $relative);
        } catch (RouteNotFoundException $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $RoutingExtension->getPath('homepage').'404?bind='.$name;
    }

    /**
     * bind から URL へ変換します。
     * \Symfony\Bridge\Twig\Extension\RoutingExtension::getUrl の処理を拡張し、
     * RouteNotFoundException 発生時に E_USER_WARNING を発生させ、
     * 文字列 "/404?bind={bind}" を返します。
     *
     * @param string $name
     * @param array $parameters
     * @param boolean $schemeRelative
     * @return string URL
     */
    public function getUrl($name, $parameters = array(), $schemeRelative = false)
    {
        $RoutingExtension = $this->app['twig']->getExtension(RoutingExtension::class);
        try {
            return $RoutingExtension->getUrl($name, $parameters, $schemeRelative);
        } catch (RouteNotFoundException $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }

        return $RoutingExtension->getUrl('homepage').'404?bind='.$name;
    }

    /**
     * Check if the value is object
     *
     * @param object $value
     * @return bool
     */
    public function isObject($value)
    {
        return is_object($value);
    }

    /**
     * FormView にエラーが含まれるかを返す.
     *
     * @return bool
     */
    public function hasErrors()
    {
        $hasErrors = false;

        $views = func_get_args();
        foreach ($views as $view) {
            if (!$view instanceof FormView) {
                throw new \InvalidArgumentException();
            }
            if (count($view->vars['errors'])) {
                $hasErrors = true;
                break;
            };
        }

        return $hasErrors;
    }
}
