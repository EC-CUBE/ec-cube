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

use \Codeception\Example;

class CL01DenyCest
{
    /**
     * @example { "title": "varが公開されていないか", "file": "var/cache/prod/annotations.map" }
     * @example { "title": ".envが公開されていないか", "file": ".env"}
     * @example { "title": "vendorが公開されていないか", "file": "vendor/symfony/config/README.md"}
     * @example { "title": "codeceptionが公開されていないか", "file": "codeception/acceptance/config.ini"}
     */
    public function denyFiles(AcceptanceTester $I, Example $data)
    {
        $I->wantTo($data['title']);
        $I->sendGet($data['file']);
        $I->seeResponseCodeIs(403);
    }
}
