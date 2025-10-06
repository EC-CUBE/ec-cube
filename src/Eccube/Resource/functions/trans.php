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

/**
 * @param string|int $id
 * @param array<string, mixed> $parameters
 * @param string|null $domain
 * @param string|null $locale
 *
 * @return string
 *
 * @throws Exception
 */
function trans($id, array $parameters = [], ?string $domain = null, ?string $locale = null)
{
    $Translator = TranslatorFacade::create();

    return $Translator->trans($id, $parameters, $domain, $locale);
}

/**
 * @param string|int $id
 * @param mixed $number - 不要引数
 * @param array<mixed> $parameters
 * @param null $domain
 * @param null $locale
 *
 * @return string
 *
 * @deprecated  transを使用してください。
 */
function transChoice($id, $number, array $parameters = [], $domain = null, $locale = null)
{
    return trans($id, $parameters, $domain, $locale);
}
