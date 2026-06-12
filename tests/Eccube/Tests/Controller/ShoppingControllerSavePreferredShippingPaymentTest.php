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

namespace Eccube\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Controller\ShoppingController;
use Eccube\DependencyInjection\Facade\LoggerFacade;
use Eccube\Entity\Customer;
use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\Payment;
use Eccube\Entity\Shipping;
use Eccube\Log\Logger;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

/**
 * ShoppingController::savePreferredShippingPayment の単体テスト.
 *
 * 注文確定後の保存処理は、例外が発生しても注文を失敗させず、注文完了画面用の
 * フラッシュメッセージを積むことが要件（受入基準）。例外注入は機能テストでは
 * 困難なため、EntityManager をモックして純粋な単体テストで検証する。
 *
 * @see https://github.com/EC-CUBE/ec-cube/issues/6819
 */
final class ShoppingControllerSavePreferredShippingPaymentTest extends TestCase
{
    private ShoppingController|MockObject $controller;

    private EntityManagerInterface|MockObject $entityManager;

    private FlashBagInterface|MockObject $flashBag;

    protected function setUp(): void
    {
        parent::setUp();

        // log_info() / log_warning() / log_error() が使用する LoggerFacade を初期化する.
        $this->resetLoggerFacade();
        LoggerFacade::init($this->createMock(ContainerInterface::class), $this->createMock(Logger::class));

        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->flashBag = $this->createMock(FlashBagInterface::class);

        $session = $this->createMock(FlashBagAwareSessionInterface::class);
        $session->method('getFlashBag')->willReturn($this->flashBag);

        // getUser() は会員を返すようにオーバーライドし, それ以外の実装は維持する.
        $this->controller = $this->getMockBuilder(ShoppingController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getUser'])
            ->getMock();
        $this->controller->method('getUser')->willReturn(new Customer());

        $reflection = new \ReflectionClass($this->controller);
        $reflection->getProperty('entityManager')->setValue($this->controller, $this->entityManager);
        $reflection->getProperty('session')->setValue($this->controller, $session);
    }

    protected function tearDown(): void
    {
        $this->resetLoggerFacade();

        parent::tearDown();
    }

    /**
     * 保存処理で例外が発生しても再スローされず, 注文完了画面用のフラッシュが積まれること.
     */
    public function testSwallowsExceptionAndAddsFlash(): void
    {
        $form = $this->createCheckedForm();
        $Order = $this->createSingleShippingOrder();

        // flush で例外を発生させる.
        $this->entityManager->method('flush')->willThrowException(new \RuntimeException('DB error'));

        // 注文完了画面用のフラッシュが積まれること.
        $this->flashBag->expects($this->once())
            ->method('add')
            ->with('preferred_save_error', 'front.shopping.preferred_save_failed');

        // 例外が再スローされない（注文処理が継続できる）こと.
        $this->callSavePreferredShippingPayment($Order, $form);
    }

    /**
     * 複数配送先の場合は flush を行わず, フラッシュも積まないこと（保存スキップ）.
     */
    public function testSkipsSaveWhenMultipleShipping(): void
    {
        $form = $this->createCheckedForm();

        $Order = $this->createMock(Order::class);
        $Order->method('getId')->willReturn(1);
        $Order->method('getShippings')->willReturn(new ArrayCollection([
            $this->createStub(Shipping::class),
            $this->createStub(Shipping::class),
        ]));

        $this->entityManager->expects($this->never())->method('flush');
        $this->flashBag->expects($this->never())->method('add');

        $this->callSavePreferredShippingPayment($Order, $form);
    }

    /**
     * チェックボックスが OFF の場合は flush を行わないこと.
     */
    public function testSkipsSaveWhenCheckboxOff(): void
    {
        $childForm = $this->createMock(FormInterface::class);
        $childForm->method('getData')->willReturn(false);
        $form = $this->createMock(FormInterface::class);
        $form->method('has')->with('save_preferred_shipping_payment')->willReturn(true);
        $form->method('get')->with('save_preferred_shipping_payment')->willReturn($childForm);

        $Order = $this->createSingleShippingOrder();

        $this->entityManager->expects($this->never())->method('flush');
        $this->flashBag->expects($this->never())->method('add');

        $this->callSavePreferredShippingPayment($Order, $form);
    }

    /**
     * save_preferred_shipping_payment が ON のフォームモックを生成する.
     */
    private function createCheckedForm(): FormInterface|MockObject
    {
        $childForm = $this->createMock(FormInterface::class);
        $childForm->method('getData')->willReturn(true);

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->with('save_preferred_shipping_payment')->willReturn(true);
        $form->method('get')->with('save_preferred_shipping_payment')->willReturn($childForm);

        return $form;
    }

    /**
     * 単一配送先で Payment / Delivery を持つ受注モックを生成する.
     */
    private function createSingleShippingOrder(): Order|MockObject
    {
        $Shipping = $this->createMock(Shipping::class);
        $Shipping->method('getDelivery')->willReturn($this->createMock(Delivery::class));

        $Order = $this->createMock(Order::class);
        $Order->method('getId')->willReturn(1);
        $Order->method('getShippings')->willReturn(new ArrayCollection([$Shipping]));
        $Order->method('getPayment')->willReturn($this->createMock(Payment::class));

        return $Order;
    }

    private function resetLoggerFacade(): void
    {
        $ref = new \ReflectionClass(LoggerFacade::class);
        $ref->getProperty('instance')->setValue(null, null);
    }

    private function callSavePreferredShippingPayment(Order $Order, FormInterface $form): void
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('savePreferredShippingPayment');
        $method->invoke($this->controller, $Order, $form);
    }
}
