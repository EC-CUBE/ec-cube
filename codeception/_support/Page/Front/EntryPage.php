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

namespace Page\Front;

class EntryPage extends AbstractFrontPage
{
    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/entry');

        return $page;
    }

    public function 新規会員登録($form = [])
    {
        $this->tester->amOnPage('/entry');
        $email = uniqid().microtime(true).'@example.com';

        $form += [
            'entry[name][name01]' => '姓',
            'entry[name][name02]' => '名',
            'entry[kana][kana01]' => 'セイ',
            'entry[kana][kana02]' => 'メイ',
            'entry[postal_code]' => '530-0001',
            'entry[address][pref]' => ['value' => '27'],
            'entry[address][addr01]' => '大阪市北区',
            'entry[address][addr02]' => '梅田2-4-9 ブリーゼタワー13F',
            'entry[phone_number]' => '1234567890',
            'entry[email][first]' => $email,
            'entry[email][second]' => $email,
            'entry[password][first]' => 'password',
            'entry[password][second]' => 'password',
            'entry[user_policy_check]' => '1',
        ];
        $this->tester->submitForm(['css' => '.ec-layoutRole__main form'], $form, ['css' => 'button.ec-blockBtn--action']);
        $this->tester->see($form['entry[email][first]']);
        $this->tester->click('.ec-registerRole form button.ec-blockBtn--action');

        return $this;
    }
}
