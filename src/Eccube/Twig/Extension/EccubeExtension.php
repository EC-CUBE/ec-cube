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

use Silex\Application;

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
        return array(
            'image_info' => new \Twig_Function_Method($this, 'getImageInfo'),
            'calc_inc_tax' => new \Twig_Function_Method($this, 'getCalcIncTax'),
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
            'no_image_main_list' => new \Twig_Filter_Method($this, 'getNoImageMainList'),
            'no_image_main' => new \Twig_Filter_Method($this, 'getNoImageMain'),
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
    public function getImageInfo($path)
    {
        $image_info = array(
            'path'      => null,
            'width'     => null,
            'height'    => null,
            'type'      => null,
            'tag'       => null,
        );

        // TODO FIX PATH
        $realpath = realpath(__DIR__ . '/../../../../html' . $path);
        if (!$realpath) {
            return $image_info;
        }

        $info = getimagesize($realpath);
        if ($info) {
            $image_info = array(
                'path'      => $path,
                'width'     => $info[0],
                'height'    => $info[1],
                'type'      => $info[2],
                'tag'       => $info[3],
            );
        }

        return $image_info;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getNoImageMainList($image)
    {
        return empty($image) ? 'noimage_main_list.jpg' : $image;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getNoImageMain($image)
    {
        return empty($image) ? 'noimage_main.jpg' : $image;
    }

    /**
     * Name of this extension
     *
     * @return string
     */
    public function getCalcIncTax($price, $tax_rate, $tax_rule)
    {
        return $price + $this->app['eccube.service.tax_rule']->calcTax($price, $tax_rate, $tax_rule);
    }
}
