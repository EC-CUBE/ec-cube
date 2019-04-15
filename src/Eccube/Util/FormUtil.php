<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

namespace Eccube\Util;


use Symfony\Component\Form\FormInterface;

class FormUtil
{
    /**
     * formオブジェクトからviewDataを取得する.
     *
     * @param FormInterface $form
     * @return array
     */
    public static function getViewData(FormInterface $form)
    {
        $viewData = array();
        $forms = $form->all();

        if (empty($forms)) {
            return $form->getViewData();
        }

        foreach ($forms as $key => $value) {
            // choice typeは各選択肢もFormとして扱われるため再帰しない.
            if ($value->getConfig()->hasOption('choices')) {
                $viewData[$key] = $value->getViewData();
            } else {
                $viewData[$key] = self::getViewData($value);
            }
        }

        return $viewData;
    }

    /**
     * formオブジェクトにviewdataをsubmitし, マッピングした結果を返す.
     *
     * @param FormInterface $form
     * @param $viewData
     * @return mixed
     */
    public static function submitAndGetData(FormInterface $form, $viewData)
    {
        $form->submit($viewData);

        return $form->getData();
    }
}
