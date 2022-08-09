<?php

namespace security;

use AcceptanceTester;
use Eccube\Entity\Payment;

class SO01CRSFSecurityCest {
    public function csrf_security_01(AcceptanceTester $I) {
        $I->comment('Checking CSRF security via admin setting payment page');
        $I->loginAsAdmin();
        $I->setRestCookie('eccube', $I->grabCookie('eccube'));
        $I->stopFollowingRedirects();
        $I->sendPost('/admin/setting/shop/payment/1/edit', [
            'payment_register[method]' => '郵便振替',
            'payment_register[charge]' => '2',
            'payment_register[rule_min]' => '1',
            'payment_register[rule_max]' => '2',
            'payment_register[payment_image_file]' => '&#x22;><script>alert(&#x22;xss&#x22;)</script>'
        ]);
        // @fixme: Rest module keeps redirecting regardless of stopFollowingRedirects rule which ends with a success code (200).
//        $I->dontSeeResponseCodeIs(301);
//        $I->dontSeeResponseCodeIs(200);

        // Check the payment type exits
        $I->seeInRepository(Payment::class, [
            'id' => 1,
            'method' => '郵便振替'
        ]);
        // Check it doesn't contain posted data.
        $I->dontSeeInRepository(Payment::class, [
            'id' => 1,
            'payment_image' => '&#x22;><script>alert(&#x22;xss&#x22;)</script>'
        ]);
    }
}
