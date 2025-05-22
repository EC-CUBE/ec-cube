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

class VaddyCest
{
    /** @var string */
    private $vaddyVerificationFile;

    /** @var string */
    private $vaddyVerificationCode;

    public function _before(AcceptanceTester $I)
    {
        $this->vaddyVerificationCode = getenv('VADDY_VERIFICATION_CODE');
        $this->vaddyVerificationFile = "vaddy-{$this->vaddyVerificationCode}.html";
    }

    public function begin(AcceptanceTester $I)
    {
        $I->amOnPage("/{$this->vaddyVerificationFile}?action=begin&time=".time().'&label='.getenv('VADDY_CRAWL'));
        $I->see($this->vaddyVerificationCode);
    }

    public function commit(AcceptanceTester $I)
    {
        $I->amOnPage("/{$this->vaddyVerificationFile}?action=commit&time=".time());
        $I->see($this->vaddyVerificationCode);
    }
}
