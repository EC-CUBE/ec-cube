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
use Twig\TwigTest;

class EccubeExtension extends AbstractExtension
{
    /**
     * EccubeExtension constructor.
     */
    public function __construct(protected EccubeConfig $eccubeConfig, private readonly ProductRepository $productRepository)
    {
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[] An array of functions
     */
    #[\Override]
    public function getFunctions(): array
    {
        return [
            new TwigFunction('has_errors', $this->hasErrors(...)),
            new TwigFunction('active_menus', $this->getActiveMenus(...)),
            new TwigFunction('class_categories_as_json', $this->getClassCategoriesAsJson(...)),
            new TwigFunction('product', $this->getProduct(...)),
            new TwigFunction('currency_symbol', $this->getCurrencySymbol(...)),
        ];
    }

    /**
     * Returns a list of filters.
     *
     * @return TwigFilter[]
     */
    #[\Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('no_image_product', $this->getNoImageProduct(...)),
            new TwigFilter('date_format', $this->getDateFormatFilter(...)),
            new TwigFilter('price', $this->getPriceFilter(...)),
            new TwigFilter('ellipsis', $this->getEllipsis(...)),
            new TwigFilter('time_ago', $this->getTimeAgo(...)),
            new TwigFilter('file_ext_icon', $this->getExtensionIcon(...), ['is_safe' => ['html']]),
            new TwigFilter('json_encode_safe', $this->getJsonEncodeSafe(...), ['is_safe' => ['js']]),
        ];
    }

    /**
     * Returns a list of tests.
     *
     * @return TwigTest[]
     */
    #[\Override]
    public function getTests(): array
    {
        return [
            new TwigTest('integer', fn ($value) => is_integer($value)),
        ];
    }

    /**
     * Name of this extension
     */
    public function getName(): string
    {
        return 'eccube';
    }

    /**
     * Name of this extension
     *
     * @param array<mixed> $menus
     *
     * @return array<mixed>
     */
    public function getActiveMenus(array $menus = []): array
    {
        $count = count($menus);
        for ($i = $count; $i <= 2; $i++) {
            $menus[] = '';
        }

        return $menus;
    }

    /**
     * return No Image filename
     */
    public function getNoImageProduct(?string $image): string
    {
        return empty($image) ? 'no_image_product.png' : $image;
    }

    /**
     * Name of this extension
     */
    public function getDateFormatFilter(?\DateTimeInterface $date, string $value = '', string $format = 'Y/m/d'): string
    {
        if (is_null($date)) {
            return $value;
        }

        return $date->format($format);
    }

    /**
     * Name of this extension
     */
    public function getPriceFilter(float|string|null $number, int $decimals = 0, string $decPoint = '.', string $thousandsSep = ','): string
    {
        /** @var string $locale */
        $locale = $this->eccubeConfig['locale'];
        /** @var string $currency */
        $currency = $this->eccubeConfig['currency'];
        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

        return $formatter->formatCurrency((float) ($number ?? 0), $currency);
    }

    /**
     * Name of this extension
     */
    public function getEllipsis(string $value, int $length = 100, string $end = '...'): string
    {
        return StringUtil::ellipsis($value, $length, $end);
    }

    /**
     * Name of this extension
     */
    public function getTimeAgo(string|\DateTimeInterface $date): string
    {
        return StringUtil::timeAgo($date);
    }

    /**
     * FormView にエラーが含まれるかを返す.
     */
    public function hasErrors(): bool
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
     */
    public function getProduct(int|float|string $id): ?Product
    {
        try {
            $Product = $this->productRepository->findWithSortedClassCategories($id);

            if ($Product->getStatus()->getId() == ProductStatus::DISPLAY_SHOW) {
                return $Product;
            }
        } catch (\Exception) {
            return null;
        }

        return null;
    }

    /**
     * Get the ClassCategories as JSON.
     */
    public function getClassCategoriesAsJson(Product $Product): string
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
            /** @var ProductClass $ProductClass */
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
                'price01' => $ProductClass->getPrice01() === null ? '' : number_format((float) $ProductClass->getPrice01()),
                'price02' => number_format((float) $ProductClass->getPrice02()),
                'price01_inc_tax' => $ProductClass->getPrice01() === null ? '' : number_format((float) $ProductClass->getPrice01IncTax()),
                'price02_inc_tax' => number_format((float) $ProductClass->getPrice02IncTax()),
                'price01_with_currency' => $ProductClass->getPrice01() === null ? '' : $this->getPriceFilter($ProductClass->getPrice01()),
                'price02_with_currency' => $this->getPriceFilter($ProductClass->getPrice02()),
                'price01_inc_tax_with_currency' => $ProductClass->getPrice01() === null ? '' : $this->getPriceFilter($ProductClass->getPrice01IncTax()),
                'price02_inc_tax_with_currency' => $this->getPriceFilter($ProductClass->getPrice02IncTax()),
                'product_class_id' => (string) $ProductClass->getId(),
                'product_code' => $ProductClass->getCode() ?? '',
                'sale_type' => (string) $ProductClass->getSaleType()?->getId(),
            ];
        }

        return json_encode($class_categories, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Display file extension icon
     *
     * @param array<string, string> $attr
     * @param bool $iconOnly アイコンのクラス名のみ返す場合はtrue
     */
    public function getExtensionIcon(string $ext, array $attr = [], bool $iconOnly = false): string
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

        $class = $classes[$ext] ?? 'fa-file-o';

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

        return $html.'></i>';
    }

    /**
     * Get currency symbol
     */
    public function getCurrencySymbol(?string $currency = null): bool|string
    {
        if ($currency === null) {
            $currency = $this->eccubeConfig->get('currency');
        }

        return Currencies::getSymbol($currency);
    }

    /**
     * Safely encode a value to JSON for use in HTML/JavaScript context.
     */
    public function getJsonEncodeSafe(mixed $value): string
    {
        return json_encode($value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}
