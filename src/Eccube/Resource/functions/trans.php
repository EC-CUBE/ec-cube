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
 * @throws Exception
 */
function trans(string|int $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
{
    $Translator = TranslatorFacade::create();

    return $Translator->trans($id, $parameters, $domain, $locale);
}
