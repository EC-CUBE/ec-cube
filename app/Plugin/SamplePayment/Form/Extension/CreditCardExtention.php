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
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            // TODO
            // - 自身の支払い方法IDを知る必要がある
            // - インストール時に、支払い方法へ追加する際、そのIDをどこかに保持しておくか、なんかしらの識別子をdtb_paymentにもたせる
            $my_payment_id = 1;

            /** @var Order $data */
            $data = $event->getData();
            $form = $event->getForm();

            // 支払い方法が一致する場合
            if ($my_payment_id == $data->getPayment()->getId()) {
                // TODO 確認画面以降は, Orderエンティティに保持されるため不要
                // TODO 注文手続き画面か確認画面かわかるようにする
                dump(222);
                $form->add('sample_payment_token', HiddenType::class, [
                ]);

                $form->add('sample_payment_card_no', TextType::class, [
                    'required' => false,
                    'mapped' => false,
                ]);

                // TODO
                // 確認する or 注文するボタンもここで制御したい
                // 属性追加やon click追加など
            }
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            // TODO
            // - 自身の支払い方法IDを知る必要がある
            // - インストール時に、支払い方法へ追加する際、そのIDをどこかに保持しておくか、なんかしらの識別子をdtb_paymentにもたせる
            $my_payment_id = 1;

            /** @var Order $data */
            $data = $event->getData();
            $form = $event->getForm();

            // 支払い方法が一致しなければremove
            if ($my_payment_id != $data['Payment']) {
                $form->remove('sample_payment_token');
                $form->remove('sample_payment_card_no');
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
