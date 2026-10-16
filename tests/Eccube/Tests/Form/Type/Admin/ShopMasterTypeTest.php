<?php

declare(strict_types=1);

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

namespace Eccube\Tests\Form\Type\Admin;

use Eccube\Entity\BaseInfo;
use Eccube\Entity\OpeningHours;
use Eccube\Form\Type\Admin\ShopMasterType;
use Eccube\Tests\Form\Type\AbstractTypeTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopMasterTypeTest extends AbstractTypeTestCase
{
    protected ?FormInterface $form = null;

    /**
     * @var array|null デフォルト値（正常系）を設定
     */
    protected ?array $formData = [
        /*
        'company_name' => '会社名',
        'company_kana' => 'カナ',
         */
        'shop_name' => '店舗名',
        /*
        'shop_kana' => 'カナ',
        'shop_name_eng' => 'shopname',
        'postal_code' => '530-0001',
        'address' => array(
            'pref' => '5',
            'addr01' => '北区',
            'addr02' => '梅田',
        ),
         */
        'phone_number' => '012-345-6789',
        /*
        'business_hour' => '店舗営業時間',
         */
        'email01' => 'eccube@example.com',
        'email02' => 'eccube@example.com',
        'email03' => 'eccube@example.com',
        'email04' => 'eccube@example.com',
        'delivery_free_amount' => '1000',
        'delivery_free_quantity' => '1000',
        // 営業時間の欄が描画されたことを示すセンチネル（shop_master.twig が出力する）
        'opening_hours_rendered' => '1',
        /*
        'good_traded' => '取り扱い商品',
        'message' => 'メッセージ',
        'option_product_delivery_fee' => '0',
        'option_delivery_fee' => '0',
        'option_customer_activate' => '0',
        'option_mypage_order_status_display' => '0',
        'option_favorite_product' => 0,
        'option_remember_me' => '0',
        'option_nostock_hidden' => '0',
         */
    ];

    protected function setUp(): void
    {
        parent::setUp();
        // CSRF tokenを無効にしてFormを作成
        $this->form = $this->formFactory
            ->createBuilder(ShopMasterType::class, null, ['csrf_protection' => false])
            ->getForm();
    }

    public function testValidData()
    {
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidPhoneNumberBlank()
    {
        $this->formData['phone_number'] = '';

        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountOverMaxLength()
    {
        $this->formData['delivery_free_amount'] = '12345678900';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountNotNumeric()
    {
        $this->formData['delivery_free_amount'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeAmountHasMinus()
    {
        $this->formData['delivery_free_amount'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityNotNumeric()
    {
        $this->formData['delivery_free_quantity'] = 'abcde';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidDeliveryFreeQuantityHasMinus()
    {
        $this->formData['delivery_free_quantity'] = '-12345';

        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '100';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMin()
    {
        $this->formData['basic_point_rate'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidBasicPointRateRangeMax()
    {
        $this->formData['basic_point_rate'] = '101';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidGoodTradedMaxLength()
    {
        $this->formData['good_traded'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidMessageMaxLength()
    {
        $this->formData['message'] = str_repeat('1', $this->eccubeConfig['eccube_ltext_len'] + 1);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidOpeningHours(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Monday', 'Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 閉店が開店より前の行は、翌日の閉店時刻として扱う（深夜営業）.
     */
    public function testValidOpeningHoursOvernight(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['PublicHolidays'], 'opens' => '20:00', 'closes' => '03:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidOpeningHoursOpensEqualsCloses(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Monday'], 'opens' => '00:00', 'closes' => '00:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertCount(1, $this->form->get('OpeningHours')->get('0')->get('closes')->getErrors());
    }

    /**
     * 日をまたぐ枠の後半（翌日の 00:00〜閉店）が、翌日の枠と重なればエラー.
     */
    public function testInValidOpeningHoursOvernightOverlapsNextDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Friday'], 'opens' => '18:00', 'closes' => '03:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '02:00', 'closes' => '10:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 日をまたぐ枠の後半が翌日の枠と接するだけ（閉店 = 翌日の開店）なら重複ではない.
     */
    public function testValidOpeningHoursOvernightTouchesNextDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Friday'], 'opens' => '18:00', 'closes' => '03:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '03:00', 'closes' => '10:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 日をまたぐ枠の前半（当日の開店〜24:00）が、同じ曜日の枠と重なればエラー.
     */
    public function testInValidOpeningHoursOvernightOverlapsSameDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Wednesday'], 'opens' => '10:00', 'closes' => '20:00'],
            ['day_of_week' => ['Wednesday'], 'opens' => '19:00', 'closes' => '01:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertCount(0, $this->form->get('OpeningHours')->get('0')->get('closes')->getErrors());
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 日曜の翌日は月曜として判定する.
     */
    public function testInValidOpeningHoursOvernightSundayWrapsToMonday(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Monday'], 'opens' => '00:00', 'closes' => '05:00'],
            ['day_of_week' => ['Sunday'], 'opens' => '22:00', 'closes' => '02:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 祝日は翌日の曜日が決まらないため、日をまたぐ枠の後半は重複判定に含めない.
     */
    public function testValidOpeningHoursOvernightPublicHolidaysIgnoresNextDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['PublicHolidays'], 'opens' => '22:00', 'closes' => '05:00'],
            ['day_of_week' => ['Monday'], 'opens' => '00:00', 'closes' => '10:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 閉店 00:00（〜深夜 0 時まで）は日をまたぐ行として保存でき、翌日の 00:00 始まりの枠とは重ならない.
     */
    public function testValidOpeningHoursClosesAtMidnight(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Friday'], 'opens' => '18:00', 'closes' => '00:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '00:00', 'closes' => '05:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 日をまたぐ行どうしの連夜の営業（金 18:00〜03:00 と 土 18:00〜03:00）は重ならない.
     */
    public function testValidOpeningHoursConsecutiveOvernights(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Friday'], 'opens' => '18:00', 'closes' => '03:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '18:00', 'closes' => '03:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    /**
     * 日をまたぐ行どうしで、前の行の翌日側と後の行の当日側が重なればエラー.
     */
    public function testInValidOpeningHoursOvernightNextDayOverlapsOvernightSameDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Friday'], 'opens' => '22:00', 'closes' => '05:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '03:00', 'closes' => '01:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 祝日は翌日側を比べないが、当日側どうしは比べる.
     */
    public function testInValidOpeningHoursOvernightPublicHolidaysSameDayOverlap(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['PublicHolidays'], 'opens' => '22:00', 'closes' => '05:00'],
            ['day_of_week' => ['PublicHolidays'], 'opens' => '23:00', 'closes' => '23:30'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 表示順（sort_no）は送信キーではなく画面の行の順に 0 から振られる.
     */
    public function testOpeningHoursSortNoFollowsScreenOrder(): void
    {
        $BaseInfo = new BaseInfo();
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $formData = $this->formData;
        $formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Tuesday'], 'opens' => '09:00', 'closes' => '18:00'],
            5 => ['day_of_week' => ['Wednesday'], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $actual = [];
        foreach ($BaseInfo->getOpeningHours() as $OpeningHours) {
            $actual[$OpeningHours->getDayOfWeek()[0]] = $OpeningHours->getSortNo();
        }
        $this->assertSame(['Monday' => 0, 'Tuesday' => 1, 'Wednesday' => 2], $actual);
    }

    /**
     * 既存の行の値をすべて消して送信すると、その行は削除される.
     */
    public function testOpeningHoursExistingRowClearedIsRemoved(): void
    {
        $BaseInfo = new BaseInfo();
        $BaseInfo->addOpeningHour(
            (new OpeningHours())
                ->setDayOfWeek(['Monday'])
                ->setOpens(new \DateTime('09:00'))
                ->setCloses(new \DateTime('18:00'))
        );
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $formData = $this->formData;
        // 曜日のチェックボックスは未チェックだとキーごと送られない
        $formData['OpeningHours'] = [
            0 => ['opens' => '', 'closes' => ''],
        ];
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertCount(0, $BaseInfo->getOpeningHours());
    }

    /**
     * 営業時間の欄が描画されていない送信（テンプレートの上書き等。センチネルも OpeningHours のキーも無い）では、
     * 既存の営業時間を削除しない.
     */
    public function testOpeningHoursKeptWhenFieldNotRendered(): void
    {
        $BaseInfo = $this->createBaseInfoWithOpeningHours();
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $formData = $this->formData;
        unset($formData['opening_hours_rendered']);
        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertCount(1, $BaseInfo->getOpeningHours());
        $this->assertSame(['Monday'], $BaseInfo->getOpeningHours()->first()->getDayOfWeek());
    }

    /**
     * 営業時間の欄が描画されていて行が 1 つも送られない（画面で全行を削除した）ときは、既存の営業時間を削除する.
     */
    public function testOpeningHoursRemovedWhenRenderedWithoutRows(): void
    {
        $BaseInfo = $this->createBaseInfoWithOpeningHours();
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $form->submit($this->formData);

        $this->assertTrue($form->isValid());
        $this->assertCount(0, $BaseInfo->getOpeningHours());
    }

    public function testInValidOpeningHoursMissingDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => [], 'opens' => '09:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidOpeningHoursOverlapSameDay(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Saturday'], 'opens' => '14:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 画面で中間行を削除すると送信キーが歯抜け（0, 2 等）になる。キーは 0 から詰め直され、
     * 重複エラーは画面で 2 行目の行に付く.
     */
    public function testInValidOpeningHoursOverlapAttachesErrorToSubmittedRow(): void
    {
        $this->formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            2 => ['day_of_week' => ['Saturday'], 'opens' => '14:00', 'closes' => '18:00'],
        ];
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertSame([0, 1], array_keys(iterator_to_array($this->form->get('OpeningHours'))));
        $this->assertOverlapErrorOn('1');
    }

    /**
     * 送信キーが歯抜け（0, 2）でも、行単位の検証（必須）のエラーは送信した行の閉店時刻に付く.
     * 詰め直さないと違反のパスが実在しない子を指し、ルートのフォームに付いて画面に何も出ない.
     */
    public function testInValidOpeningHoursGapKeysRequiredErrorOnSubmittedRow(): void
    {
        $this->formData['OpeningHours'] = [
            0 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Tuesday'], 'opens' => '09:00', 'closes' => ''],
        ];
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertCount(0, $this->form->getErrors());
        $rows = array_values(iterator_to_array($this->form->get('OpeningHours')));
        $this->assertCount(2, $rows);
        $this->assertCount(0, $rows[0]->get('closes')->getErrors());
        $this->assertValidatorErrorOn($rows[1]->get('closes'), 'admin.setting.shop.opening_hours.error.closes_required');
    }

    /**
     * 送信キーが歯抜け（1, 2）でも、開店と閉店が同じ時刻のエラーは送信した行に付く（先頭の行には付かない）.
     */
    public function testInValidOpeningHoursGapKeysOpensEqualsClosesErrorOnSubmittedRow(): void
    {
        $this->formData['OpeningHours'] = [
            1 => ['day_of_week' => ['Monday'], 'opens' => '09:00', 'closes' => '18:00'],
            2 => ['day_of_week' => ['Tuesday'], 'opens' => '10:00', 'closes' => '10:00'],
        ];
        $this->form->submit($this->formData);

        $this->assertFalse($this->form->isValid());
        $this->assertCount(0, $this->form->getErrors());
        $rows = array_values(iterator_to_array($this->form->get('OpeningHours')));
        $this->assertCount(2, $rows);
        $this->assertCount(0, $rows[0]->get('closes')->getErrors());
        $this->assertValidatorErrorOn($rows[1]->get('closes'), 'admin.setting.shop.opening_hours.error.opens_equals_closes');
    }

    public function testValidOpeningHoursDifferentDayNoOverlap(): void
    {
        $this->formData['OpeningHours'] = [
            ['day_of_week' => ['Saturday'], 'opens' => '10:00', 'closes' => '15:00'],
            ['day_of_week' => ['Sunday'], 'opens' => '10:00', 'closes' => '15:00'],
        ];
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidSameAsMultipleUrls(): void
    {
        $this->formData['same_as'] = "https://example.com/a\nhttps://example.com/b";
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSameAsContainsNonUrl(): void
    {
        $this->formData['same_as'] = "https://example.com/a\nnot-a-url";
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSameAsMaxLength(): void
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['same_as'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_ltext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidNumberOfEmployeesZero(): void
    {
        $this->formData['number_of_employees'] = '0';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNumberOfEmployeesNegative(): void
    {
        $this->formData['number_of_employees'] = '-1';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidNumberOfEmployeesIntMax(): void
    {
        $this->formData['number_of_employees'] = '2147483647';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidNumberOfEmployeesOverIntMax(): void
    {
        $this->formData['number_of_employees'] = '2147483648';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMin(): void
    {
        $this->formData['copyright_year'] = '1900';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testValidCopyrightYearRangeMax(): void
    {
        $this->formData['copyright_year'] = '9999';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidCopyrightYearBelowMin(): void
    {
        $this->formData['copyright_year'] = '1899';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidCopyrightYearAboveMax(): void
    {
        $this->formData['copyright_year'] = '10000';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidFoundingDatePast(): void
    {
        $this->formData['founding_date'] = '2000-04-01';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidFoundingDateFuture(): void
    {
        $this->formData['founding_date'] = (new \DateTime('+1 year'))->format('Y-m-d');
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testValidSiteImageUrl(): void
    {
        $this->formData['site_image'] = 'https://example.com/site.png';
        $this->form->submit($this->formData);
        $this->assertTrue($this->form->isValid());
    }

    public function testInValidSiteImageNotUrl(): void
    {
        $this->formData['site_image'] = 'not-a-url';
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    public function testInValidSiteImageMaxLength(): void
    {
        // 形式は有効なURLだが最大長を超えるケース（長さ制約のみを検証）
        $this->formData['site_image'] = 'https://example.com/'.str_repeat('a', $this->eccubeConfig['eccube_stext_len']);
        $this->form->submit($this->formData);
        $this->assertFalse($this->form->isValid());
    }

    /**
     * 納品書PDFの出力項目トグルが BaseInfo にマッピングされること (#6197).
     * チェックボックスは未チェックをキー欠落で表すため, OFF はキーを送らないことで再現する.
     */
    public function testOrderPdfVisibleTogglesMappedToEntity(): void
    {
        $BaseInfo = new BaseInfo();
        $form = $this->formFactory
            ->createBuilder(ShopMasterType::class, $BaseInfo, ['csrf_protection' => false])
            ->getForm();

        $formData = $this->formData;
        // 既定 OFF の店舗営業時間を ON にし, 既定 ON の店名はキーを送らず OFF にする
        $formData['order_pdf_visible_business_hour'] = '1';

        $form->submit($formData);

        $this->assertTrue($form->isValid());
        $this->assertFalse($BaseInfo->isOrderPdfVisibleShopName());
        $this->assertTrue($BaseInfo->isOrderPdfVisibleBusinessHour());
    }

    private function createBaseInfoWithOpeningHours(): BaseInfo
    {
        $BaseInfo = new BaseInfo();
        $BaseInfo->addOpeningHour(
            (new OpeningHours())
                ->setDayOfWeek(['Monday'])
                ->setOpens(new \DateTime('09:00'))
                ->setCloses(new \DateTime('18:00'))
        );

        return $BaseInfo;
    }

    /**
     * 指定したフォームに、validators ドメインの指定した文言のエラーが 1 件だけ付いていることを確かめる.
     */
    private function assertValidatorErrorOn(FormInterface $form, string $message): void
    {
        $errors = $form->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame(
            static::getContainer()->get(TranslatorInterface::class)->trans($message, [], 'validators'),
            $errors[0]->getMessage()
        );
    }

    /**
     * 営業時間の指定した行の閉店時刻に、重複のエラーが 1 件だけ付いていることを確かめる.
     * 単体バリデーション（開店と閉店の関係など）のエラーと区別するため、文言まで照合する.
     */
    private function assertOverlapErrorOn(string $key): void
    {
        $errors = $this->form->get('OpeningHours')->get($key)->get('closes')->getErrors();
        $this->assertCount(1, $errors);
        $this->assertSame(
            static::getContainer()->get(TranslatorInterface::class)->trans('admin.setting.shop.opening_hours.error.overlap'),
            $errors[0]->getMessage()
        );
    }
}
