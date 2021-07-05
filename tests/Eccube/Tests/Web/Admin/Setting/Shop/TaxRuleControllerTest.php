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

namespace Eccube\Tests\Web\Admin\Setting\Shop;

use Eccube\Entity\TaxRule;
use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;

class TaxRuleControllerTest extends AbstractAdminWebTestCase
{
    /**
     * @return TaxRule
     */
    public function createTaxRule()
    {
        $faker = $this->getFaker();
        $TargetTaxRule = $this->entityManager->getRepository(\Eccube\Entity\TaxRule::class)->newTaxRule();
        $TargetTaxRule->setTaxRate($faker->randomNumber(2));
        $now = new \DateTime();
        $TargetTaxRule->setApplyDate($now);
        $this->entityManager->persist($TargetTaxRule);
        $this->entityManager->flush();

        return $TargetTaxRule;
    }

    public function testRoutingAdminBasisTaxIndex()
    {
        $this->client->request(
            'GET',
            $this->generateUrl('admin_setting_shop_tax')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testRoutingAdminBasisTaxDelete()
    {
        $redirectUrl = $this->generateUrl('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_tax_delete', ['id' => 1])
        );

        $actual = $this->client->getResponse()->isRedirect($redirectUrl);

        $this->assertSame(true, $actual);
    }

    public function testEdit()
    {
        $TaxRule = $this->createTaxRule();
        $tid = $TaxRule->getId();
        $now = new \DateTime();
        $form = [
            '_token' => 'dummy',
            'tax_rate' => 10,
            'rounding_type' => rand(1, 3),
            'apply_date' => [
                'date' => [
                    'year' => $now->format('Y'),
                    'month' => $now->format('n'),
                    'day' => $now->format('j'),
                ],
                'time' => [
                    'hour' => $now->format('G'),
                    // Symfony specification of without leading zero
                    // https://symfony.com/doc/3.4/reference/forms/types/datetime.html#minutes
                    'minute' => (int) $now->format('i'),
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_tax'),
            [
                'tax_rule' => $form,
                'tax_rule_id' => "$tid",
                'mode' => 'edit_inline',
            ]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form['tax_rate'];
        $this->actual = $TaxRule->getTaxRate();
        $this->verify();
    }

    public function testTaxDeleteSuccess()
    {
        $TaxRule = $this->createTaxRule();

        $taxRuleId = $TaxRule->getId();
        $redirectUrl = $this->generateUrl('admin_setting_shop_tax');

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_tax_delete', ['id' => $TaxRule->getId()])
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));
        $this->assertNull($this->entityManager->getRepository(\Eccube\Entity\TaxRule::class)->find($taxRuleId));
    }

    public function testTaxDeleteFail()
    {
        $tid = 99999;

        $this->client->request(
            'DELETE',
            $this->generateUrl('admin_setting_shop_tax_delete', ['id' => $tid])
        );
        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testEditWithTime()
    {
        $TaxRule = $this->createTaxRule();
        $tid = $TaxRule->getId();
        $now = new \DateTime();
        $form = [
            '_token' => 'dummy',
            'tax_rate' => 10,
            'rounding_type' => rand(1, 3),
            'apply_date' => [
                'date' => [
                    'year' => $now->format('Y'),
                    'month' => $now->format('n'),
                    'day' => $now->format('j'),
                ],
                'time' => [
                    'hour' => 23,
                    'minute' => 1,
                ],
            ],
        ];

        $this->client->request(
            'POST',
            $this->generateUrl('admin_setting_shop_tax'),
            [
                'tax_rule' => $form,
                'tax_rule_id' => "$tid",
                'mode' => 'edit_inline',
            ]
        );

        $redirectUrl = $this->generateUrl('admin_setting_shop_tax');
        $this->assertTrue($this->client->getResponse()->isRedirect($redirectUrl));

        $this->expected = $form['tax_rate'];
        $this->actual = $TaxRule->getTaxRate();
        $this->verify();
    }
}
