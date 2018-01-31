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

use Eccube\Service\TaxRuleService;
use Eccube\Util\StringUtil;
use Symfony\Component\Form\FormView;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EccubeExtension extends AbstractExtension
{
    /**
     * @var TaxRuleService
     */
    protected $TaxRuleService;

    public function __construct(TaxRuleService $TaxRuleService)
    {
        $this->TaxRuleService = $TaxRuleService;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            new TwigFunction('has_errors', array($this, 'hasErrors')),
            new TwigFunction('is_object', array($this, 'isObject')),
            new TwigFunction('calc_inc_tax', array($this, 'getCalcIncTax')),
            new TwigFunction('active_menus', array($this, 'getActiveMenus')),
            new TwigFunction('php_*', function() {
                    $arg_list = func_get_args();
                    $function = array_shift($arg_list);
                    if (is_callable($function)) {
                        return call_user_func_array($function, $arg_list);
                    }
                    trigger_error('Called to an undefined function : php_'. $function, E_USER_WARNING);

            }, ['pre_escape' => 'html', 'is_safe' => ['html']]),
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
            new TwigFilter('no_image_product', array($this, 'getNoImageProduct')),
            new TwigFilter('date_format', array($this, 'getDateFormatFilter')),
            new TwigFilter('price', array($this, 'getPriceFilter')),
            new TwigFilter('ellipsis', array($this, 'getEllipsis')),
            new TwigFilter('time_ago', array($this, 'getTimeAgo')),
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
        return $price + $this->TaxRuleService->calcTax($price, $tax_rate, $tax_rule);
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
        // $locale = $this->app['config']['locale'];
        // $currency = $this->app['config']['currency'];

        // FIXME
        $locale = 'ja_JP';
        $currency = 'JPY';
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
