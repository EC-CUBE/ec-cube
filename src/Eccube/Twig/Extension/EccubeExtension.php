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

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Product;
use Eccube\Entity\ProductClass;
use Eccube\Repository\ProductRepository;
use Eccube\Util\StringUtil;
use Symfony\Component\Form\FormView;
use Symfony\Component\Intl\Currencies;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class EccubeExtension extends AbstractExtension
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * EccubeExtension constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param ProductRepository $productRepository
     */
    public function __construct(EccubeConfig $eccubeConfig, ProductRepository $productRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->productRepository = $productRepository;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[] An array of functions
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('has_errors', [$this, 'hasErrors']),
            new TwigFunction('active_menus', [$this, 'getActiveMenus']),
            new TwigFunction('class_categories_as_json', [$this, 'getClassCategoriesAsJson']),
            new TwigFunction('product', [$this, 'getProduct']),
            new TwigFunction('currency_symbol', [$this, 'getCurrencySymbol']),
        ];
    }

    /**
     * Returns a list of filters.
     *
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('no_image_product', [$this, 'getNoImageProduct']),
            new TwigFilter('date_format', [$this, 'getDateFormatFilter']),
            new TwigFilter('price', [$this, 'getPriceFilter']),
            new TwigFilter('ellipsis', [$this, 'getEllipsis']),
            new TwigFilter('time_ago', [$this, 'getTimeAgo']),
            new TwigFilter('file_ext_icon', [$this, 'getExtensionIcon'], ['is_safe' => ['html']]),
        ];
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
     * @param array $menus
     *
     * @return array
     */
    public function getActiveMenus($menus = [])
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
        return empty($image) ? 'no_image_product.png' : $image;
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
        $locale = $this->eccubeConfig['locale'];
        $currency = $this->eccubeConfig['currency'];
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
            }
        }

        return $hasErrors;
    }

    /**
     * product_idで指定したProductを取得
     * Productが取得できない場合、または非公開の場合、商品情報は表示させない。
     * デバッグ環境以外ではProductが取得できなくでもエラー画面は表示させず無視される。
     *
     * @param $id
     *
     * @return Product|null
     */
    public function getProduct($id)
    {
        try {
            $Product = $this->productRepository->findWithSortedClassCategories($id);

            if ($Product->getStatus()->getId() == ProductStatus::DISPLAY_SHOW) {
                return $Product;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    /**
     * Get the ClassCategories as JSON.
     *
     * @param Product $Product
     *
     * @return string
     */
    public function getClassCategoriesAsJson(Product $Product)
    {
        $Product->_calc();
        $class_categories = [
            '__unselected' => [
                '__unselected' => [
                    'name' => trans('common.select'),
                    'product_class_id' => '',
                ],
            ],
        ];
        foreach ($Product->getProductClasses() as $ProductClass) {
            /** @var ProductClass $ProductClass */
            if (!$ProductClass->isVisible()) {
                continue;
            }
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ClassCategory1 = $ProductClass->getClassCategory1();
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if ($ClassCategory2 && !$ClassCategory2->isVisible()) {
                continue;
            }
            $class_category_id1 = $ClassCategory1 ? (string) $ClassCategory1->getId() : '__unselected2';
            $class_category_id2 = $ClassCategory2 ? (string) $ClassCategory2->getId() : '';
            $class_category_name2 = $ClassCategory2 ? $ClassCategory2->getName().($ProductClass->getStockFind() ? '' : trans('front.product.out_of_stock_label')) : '';

            $class_categories[$class_category_id1]['#'] = [
                'classcategory_id2' => '',
                'name' => trans('common.select'),
                'product_class_id' => '',
            ];
            $class_categories[$class_category_id1]['#'.$class_category_id2] = [
                'classcategory_id2' => $class_category_id2,
                'name' => $class_category_name2,
                'stock_find' => $ProductClass->getStockFind(),
                'price01' => $ProductClass->getPrice01() === null ? '' : number_format($ProductClass->getPrice01()),
                'price02' => number_format($ProductClass->getPrice02()),
                'price01_inc_tax' => $ProductClass->getPrice01() === null ? '' : number_format($ProductClass->getPrice01IncTax()),
                'price02_inc_tax' => number_format($ProductClass->getPrice02IncTax()),
                'price01_with_currency' => $ProductClass->getPrice01() === null ? '' : $this->getPriceFilter($ProductClass->getPrice01()),
                'price02_with_currency' => $this->getPriceFilter($ProductClass->getPrice02()),
                'price01_inc_tax_with_currency' => $ProductClass->getPrice01() === null ? '' : $this->getPriceFilter($ProductClass->getPrice01IncTax()),
                'price02_inc_tax_with_currency' => $this->getPriceFilter($ProductClass->getPrice02IncTax()),
                'product_class_id' => (string) $ProductClass->getId(),
                'product_code' => $ProductClass->getCode() === null ? '' : $ProductClass->getCode(),
                'sale_type' => (string) $ProductClass->getSaleType()->getId(),
            ];
        }

        return json_encode($class_categories);
    }

    /**
     * Display file extension icon
     *
     * @param $ext
     * @param $attr
     * @param $iconOnly アイコンのクラス名のみ返す場合はtrue
     *
     * @return string
     */
    public function getExtensionIcon($ext, $attr = [], $iconOnly = false)
    {
        $classes = [
            'txt' => 'fa-file-text-o',
            'rtf' => 'fa-file-text-o',
            'pdf' => 'fa-file-pdf-o',
            'doc' => 'fa-file-word-o',
            'docx' => 'fa-file-word-o',
            'csv' => 'fa-file-excel-o',
            'xls' => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'ppt' => 'fa-file-powerpoint-o',
            'pptx' => 'fa-file-powerpoint-o',
            'png' => 'fa-file-image-o',
            'jpg' => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'bmp' => 'fa-file-image-o',
            'gif' => 'fa-file-image-o',
            'zip' => 'fa-file-archive-o',
            'tar' => 'fa-file-archive-o',
            'gz' => 'fa-file-archive-o',
            'rar' => 'fa-file-archive-o',
            '7zip' => 'fa-file-archive-o',
            'mp3' => 'fa-file-audio-o',
            'm4a' => 'fa-file-audio-o',
            'wav' => 'fa-file-audio-o',
            'mp4' => 'fa-file-video-o',
            'wmv' => 'fa-file-video-o',
            'mov' => 'fa-file-video-o',
            'mkv' => 'fa-file-video-o',
        ];
        $ext = strtolower($ext);

        $class = isset($classes[$ext]) ? $classes[$ext] : 'fa-file-o';

        if ($iconOnly) {
            return $class;
        }

        $attr['class'] = isset($attr['class'])
            ? $attr['class']." fa {$class}"
            : "fa {$class}";

        $html = '<i ';
        foreach ($attr as $name => $value) {
            $html .= "{$name}=\"$value\" ";
        }
        $html .= '></i>';

        return $html;
    }

    /**
     * Get currency symbol
     *
     * @param null $currency
     *
     * @return bool|string
     */
    public function getCurrencySymbol($currency = null)
    {
        if (is_null($currency)) {
            $currency = $this->eccubeConfig->get('currency');
        }
        $symbol = Currencies::getSymbol($currency);

        return $symbol;
    }
}
