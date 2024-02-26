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

    private $formData = [];

    public function __construct(\AcceptanceTester $I)
    {
        parent::__construct($I);
    }

    /**
     * @param $I
     * @param $id
     *
     * @return EntryPage
     */
    public static function go($I)
    {
        $page = new self($I);
        $page->goPage('/entry');

        return $page;
    }

    public function フォーム入力($form = [])
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
            'entry[plain_password][first]' => 'password1234',
            'entry[plain_password][second]' => 'password1234',
            'entry[user_policy_check]' => '1',
        ];
        $this->formData = $form;
        return $this;
    }
    
    public function 同意する()
    {
        $this->tester->submitForm(['css' => '.ec-layoutRole__main form'], $this->formData, ['css' => 'button.ec-blockBtn--action']);
        $this->tester->seeInField(['id' => 'entry_email_first'], $this->formData['entry[email][first]']);
        return $this;
    }

    public function 登録する()
    {
        $this->tester->click('.ec-registerRole form button.ec-blockBtn--action');
        return $this;
    }
}
