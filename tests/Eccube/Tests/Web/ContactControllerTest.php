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

namespace Eccube\Tests\Web;

use Eccube\Entity\BaseInfo;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Email;

final class ContactControllerTest extends AbstractWebTestCase
{
    use MailerAssertionsTrait;

    protected function createFormData()
    {
        $faker = $this->getFaker();
        $email = $faker->safeEmail;
        $faker->lexify('????????');

        return [
            'name' => [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
            ],
            'kana' => [
                'kana01' => $faker->lastKanaName,
                'kana02' => $faker->firstKanaName,
            ],
            'postal_code' => $faker->postcode,
            'address' => [
                'pref' => '5',
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
            ],
            'phone_number' => $faker->phoneNumber,
            'email' => $email,
            'contents' => $faker->realText(),
            '_token' => 'dummy',
        ];
    }

    public function testRoutingIndex()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('contact'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testConfirm()
    {
        $crawler = $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $this->createFormData(),
                'mode' => 'confirm', ]
        );

        $this->assertTrue($this->client->getResponse()->isSuccessful());

        // 確認ページ(Contact/confirm.twig)がレンダリングされていること
        $this->assertSame(1, $crawler->filter('.ec-contactConfirmRole')->count());

        // ContactController が Page(contact_confirm) を渡していること.
        // contact_confirm の Page は meta_tags に noindex を持つ.
        $this->assertSame('noindex', $crawler->filter('meta[name="robots"]')->attr('content'));

        // NOTE: <title> は確認ページでも「お問い合わせ(入力ページ)」になる.
        // contact と contact_confirm は同一パス '/contact' に割り当てられており,
        // ルータは常に先に定義された contact にマッチする. TwigInitializeListener は
        // _route から引いた Page 名を twig グローバル title に設定するため,
        // コントローラが render に渡す Page (contact_confirm) は title に反映されない.
        // title の検証は実装側の対応が必要なためここでは行わない.
    }

    public function testComplete()
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $this->createFormData(),
                'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testCompleteWithSanitize()
    {
        $form = $this->createFormData();
        $form['name']['name01'] .= '<Sanitize&>';
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $form,
                'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();

        $this->assertStringContainsString('＜Sanitize＆＞', (string) $Message->getTextBody(), 'テキストメールがサニタイズされている');
        $this->assertStringContainsString('＜Sanitize＆＞', (string) $Message->getHtmlBody(), 'HTMLメールがサニタイズされている');
    }

    /**
     * 必須項目のみのテストケース
     *
     * @see https://github.com/EC-CUBE/ec-cube/issues/1314
     */
    public function testCompleteWithRequired()
    {
        $formData = $this->createFormData();
        $formData['kana']['kana01'] = null;
        $formData['kana']['kana02'] = null;
        $formData['postal_code'] = null;
        $formData['address']['pref'] = null;
        $formData['address']['addr01'] = null;
        $formData['address']['addr02'] = null;
        $formData['phone_number'] = null;

        $this->client->enableProfiler();
        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $formData,
                'mode' => 'complete', ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testCompleteWithLogin()
    {
        // Always generate a form before login
        $formData = $this->createFormData();
        $this->logInTo($this->createCustomer());

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $formData,
                'mode' => 'complete', ]
        );

        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));

        $BaseInfo = $this->entityManager->find(BaseInfo::class, 1);

        $this->assertEmailCount(1);
        /** @var Email $Message */
        $Message = $this->getMailerMessage(0);
        $this->assertInstanceOf(BaseInfo::class, $BaseInfo);

        $this->expected = '['.$BaseInfo->getShopName().'] お問い合わせを受け付けました。';
        $this->actual = $Message->getSubject();
        $this->verify();
    }

    public function testRoutingComplete()
    {
        $this->client->request(Request::METHOD_GET, $this->generateUrl('contact_complete'));
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function testMailNoRFC()
    {
        $formData = $this->createFormData();
        // RFCに準拠していないメールアドレスを設定
        $formData['email'] = 'aa..@example.com';

        $this->client->request(
            Request::METHOD_POST,
            $this->generateUrl('contact'),
            ['contact' => $formData,
                'mode' => 'complete', ]
        );
        $this->assertTrue($this->client->getResponse()->isRedirect($this->generateUrl('contact_complete')));
    }
}
