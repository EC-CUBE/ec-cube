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

namespace Plugin\SamplePayment\Form\Extension;

use Eccube\Entity\Order;
use Eccube\Form\Type\Shopping\OrderType;
use Eccube\Repository\PaymentRepository;
use Plugin\SamplePayment\Service\Method\CreditCard;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * 注文手続き画面のFormを拡張し、カード入力フォームを追加する.
 * 支払い方法に応じてエクステンションを作成する.
 *
 * FIXME extentionを一つひとつ作るよりは、各フォームタイプに切り分けてもよいかも.
 */
class CreditCardExtention extends AbstractTypeExtension
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            /** @var Order $data */
            $data = $event->getData();
            $form = $event->getForm();

            // 支払い方法が一致する場合
            if ($data->getPayment()->getMethodClass() === CreditCard::class) {
                // TODO 確認画面以降は, Orderエンティティに保持されるため不要
                // TODO 注文手続き画面か確認画面かわかるようにする
                $form->add('sample_payment_token', HiddenType::class, [
                    'required' => false,
                    'mapped' => true, // Orderエンティティに追加したカラムなので、mappedはtrue
                    // TODO 注文手続き画面の場合のみNotBlankを有効にしたい
                    // 'constraints' => [
                    //    new NotBlank()
                    //]*/
                ]);

                // 確認画面の表示用、submitは行わない(credit_confirm.twig参照)
                // PaymentMethod::verifyで、取得した下4桁の番号をセットしている(予定)
                $form->add('sample_payment_card_no_last4', HiddenType::class, [
                    'required' => false,
                    'mapped' => false,
                ]);
                // TODO 確認する or 注文するボタンもここで制御したい
                // TODO 属性追加やon click追加など
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $Payment = $this->paymentRepository->findOneBy(['method_class' => CreditCard::class]);

            /** @var Order $data */
            $data = $event->getData();
            $form = $event->getForm();

            // 支払い方法が一致しなければremove
            if ($Payment->getId() != $data['Payment']) {
                $form->remove('sample_payment_token');
                $form->remove('sample_payment_card_no_last4');
            }
        });
    }

    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return OrderType::class;
    }
}
