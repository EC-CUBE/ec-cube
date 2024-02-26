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

namespace Page\Admin;

class ApiWebHookEditPage extends AbstractAdminPageStyleGuide
{
    public static function at(\AcceptanceTester $I)
    {
        $page = new self($I);
        $page->atPage('WebHook登録API管理');
        return $page;
    }

    public function 入力_PayloadURL($value)
    {
        $this->tester->fillField(['id' => 'web_hook_payload_url'], $value);
        return $this;
    }

    public function 入力_シークレット($value)
    {
        $this->tester->fillField(['id' => 'web_hook_secret'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['css' => '#ex-conversion-action > div > button']);
        return $this;
    }
}
