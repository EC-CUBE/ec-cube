<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\SamplePayment\Service\Method;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\Payment\Method\CreditCard as BaseCreditCard;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Service\Payment\PaymentResult;
use Plugin\SamplePayment\Entity\PaymentStatus;
use Symfony\Component\Form\FormInterface;

class CreditCard extends BaseCreditCard
{
    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(OrderStatusRepository $orderStatusRepository, EntityManagerInterface $entityManager)
    {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->entityManager = $entityManager;
    }

    public function verify()
    {
    }

    /**
     * 決済サーバと通信して、決済処理を行う.
     *
     * @return PaymentResult
     */
    public function checkout()
    {
        // TODO トークンから仮売上あげる
        // エラーだったらどうするか？
        // とりあえずShoppingExceptionなげればエラーにはできる

        // トークン取得
        $token = $this->Order->getSamplePaymentToken();
        // 決済サーバに仮売上のリクエスト送る(設定等によって送るリクエストは異なる)

        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::NEW);
        $this->Order->setOrderStatus($OrderStatus);

        $message = '決済完了しました。トークン -> '.$token;
        //$this->Order->addMailText($message);

        $this->Order->setNote($message);

        $result = new PaymentResult();
        $result->setSuccess(true);

        return $result;
    }

    /**
     * 受注ステータス, 決済ステータスを更新する
     * まだAPI通信は行わない.
     *
     * @return PaymentDispatcher
     */
    public function apply()
    {
        // TODO 決済処理中ステータスを定数化
        $OrderStatus = $this->orderStatusRepository->find(9);
        $this->Order->setOrderStatus($OrderStatus);

        $PaymentStatus = $this->entityManager->find(PaymentStatus::class, PaymentStatus::OUTSTANDING);
        $this->Order->setSamplePaymentPaymentStatus($PaymentStatus);

        // TODO ここでの処理はcheckoutで実装しても実質的には問題ない
        // TODO 3Dセキュアのフローが見えてない
        // TODO flushされるタイミングが読みづらいな->最後にflush
    }

    /**
     * @param FormInterface
     */
    public function setFormType(FormInterface $form)
    {
        $this->Order = $form->getData();

        // TODO Orderエンティティにトークンが保持されているのでフォームは不要
        // TODO フォームよりOrderがほしい
        // TODO applyやcheckoutでOrderが渡ってきてほしい.
        // TODO やっぱりFormはいる -> Orderには保持しないデータはFormで引き回す(確認画面とか). 画面に持っていくデータを詰められるオブジェクトがあればいいのかな
    }
}
