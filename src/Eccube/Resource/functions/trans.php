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
 * @param array<string, mixed> $parameters
 *
 * @return string
 *
 * @throws Exception
 */
function trans(string|int $id, array $parameters = [], ?string $domain = null, ?string $locale = null)
{
    $Translator = TranslatorFacade::create();

    return $Translator->trans($id, $parameters, $domain, $locale);
}

/**
 * @param mixed $number - 不要引数
 * @param array<mixed> $parameters
 *
 * @return string
 *
 * @deprecated  transを使用してください。
 */
function transChoice(string|int $id, mixed $number, array $parameters = [], ?string $domain = null, ?string $locale = null)
{
    return trans($id, $parameters, $domain, $locale);
}
