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

namespace Eccube\Util;

use Symfony\Component\Form\FormInterface;

class FormUtil
{
    /**
     * formオブジェクトからviewDataを取得する.
     *
     * @param FormInterface $form
     *
     * @return array
     */
    public static function getViewData(FormInterface $form)
    {
        $viewData = [];
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
     *
     * @return mixed
     */
    public static function submitAndGetData(FormInterface $form, $viewData)
    {
        $form->submit($viewData);

        return $form->getData();
    }
}
