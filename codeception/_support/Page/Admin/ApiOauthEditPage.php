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

class ApiOauthEditPage extends AbstractAdminPageStyleGuide
{
    public static function at($I)
    {
        $page = new self($I);
        $page->atPage('OAuthクライアント登録API管理');
        return $page;
    }

    public function 入力_クライアントID($value)
    {
        $this->tester->fillField(['id' => 'api_admin_client_identifier'], $value);
        return $this;
    }

    public function 入力_クライアントシークレット($value)
    {
        $this->tester->fillField(['id' => 'api_admin_client_secret'], $value);
        return $this;
    }

    public function 入力_スコープread()
    {
        $this->tester->click(['id' => 'api_admin_client_scopes_0']);
        return $this;
    }

    public function 入力_リダイレクトURI($value)
    {
        $this->tester->fillField(['id' => 'api_admin_client_redirect_uris'], $value);
        return $this;
    }

    public function 登録()
    {
        $this->tester->click(['css' => '#ex-conversion-action > div > button']);
        return $this;
    }
}
