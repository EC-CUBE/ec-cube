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

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class IntlExtension extends AbstractExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('date_day', [$this, 'date_day'], ['needs_environment' => true]),
            new TwigFilter('date_min', [$this, 'date_min'], ['needs_environment' => true]),
            new TwigFilter('date_sec', [$this, 'date_sec'], ['needs_environment' => true]),
            new TwigFilter('date_day_with_weekday', [$this, 'date_day_with_weekday'], ['needs_environment' => true]),
        ];
    }

    /**
     * localizeddate('medium', 'none')のショートカット.
     *
     * 2015/08/28のように、日までのフォーマットで表示します(localeがjaの場合).
     * null,空文字に対して利用した場合は、空文字を返却します.
     *
     * @param Environment $env
     * @param $date
     *
     * @return bool|string
     */
    public function date_day(Environment $env, $date)
    {
        if (!$date) {
            return '';
        }

        return \twig_localized_date_filter($env, $date, 'medium', 'none');
    }

    /**
     * localizeddate('medium', 'short')のショートカット.
     *
     * 2015/08/28 16:13のように、分までのフォーマットで表示します(localeがjaの場合).
     * null,空文字に対して利用した場合は、空文字を返却します.
     *
     * @param Environment $env
     * @param $date
     *
     * @return bool|string
     */
    public function date_min(Environment $env, $date)
    {
        if (!$date) {
            return '';
        }

        return \twig_localized_date_filter($env, $date, 'medium', 'short');
    }

    /**
     * localizeddate('medium', 'medium')のショートカット.
     *
     * 2015/08/28 16:13:05(localeがjaの場合).
     * null,空文字に対して利用した場合は、空文字を返却します.
     *
     * @param Environment $env
     * @param $date
     *
     * @return bool|string
     */
    public function date_sec(Environment $env, $date)
    {
        if (!$date) {
            return '';
        }

        return \twig_localized_date_filter($env, $date, 'medium', 'medium');
    }

    /**
     * @param Environment $env
     * @param $date
     *
     * @return bool|string
     */
    public function date_day_with_weekday(Environment $env, $date)
    {
        if (!$date) {
            return '';
        }

        $date_day = \twig_localized_date_filter($env, $date, 'medium', 'none');
        // 曜日
        $dateFormatter = \IntlDateFormatter::create(
            'ja_JP@calendar=japanese',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Asia/Tokyo',
            \IntlDateFormatter::TRADITIONAL,
            'E'
        );

        return $date_day.'('.$dateFormatter->format($date).')';
    }
}
