<?php

namespace security;

use AcceptanceTester;

class SO02JavascriptInjectionSecurityCest {
    public function javascript_injection_01(AcceptanceTester $I) {
        $I->comment('Checking Javascript injection via order email page');
        $I->loginAsAdmin();
    }
}
