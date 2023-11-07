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

use Eccube\DependencyInjection\Facade\TranslatorFacade;

function trans($id, array $parameters = [], $domain = null, $locale = null)
{
    $Translator = TranslatorFacade::create();

    return $Translator->trans($id, $parameters, $domain, $locale);
}

/**
 * @deprecated  transを使用してください。
 *
 * @param mixed $id
 * @param mixed $number
 * @param mixed|null $domain
 * @param mixed|null $locale
 */
function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
{
    return trans($id, $parameters, $domain, $locale);
}
