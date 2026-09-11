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

namespace Eccube\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Faq;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class FaqType extends AbstractType
{
    public function __construct(
        protected EccubeConfig $eccubeConfig,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_stext_len']]),
                ],
            ])
            ->add('answer', TextareaType::class, [
                'required' => true,
                'purify_html' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => $this->eccubeConfig['eccube_ltext_len']]),
                ],
            ])
            // 表示順. コレクション内では hidden にして、JS が並び順から連番を振る。
            // 単独編集画面で未入力のときは null のまま保存し、FaqRepository::save() が最大値 + 1 を採番する
            // （コアの Category と同じ 1 始まりに揃える）。
            ->add('sort_no', $options['sortable'] ? HiddenType::class : IntegerType::class, [
                'required' => false,
                'attr' => $options['sortable'] ? ['class' => 'sort-no'] : [],
                'constraints' => [
                    new Assert\Range(['min' => 1, 'max' => 2147483647]),
                ],
            ])
            ->add('visible', ChoiceType::class, [
                'label' => false,
                'expanded' => false,
                'multiple' => false,
                'required' => true,
                'choices' => [
                    'admin.common.show' => true,
                    'admin.common.hide' => false,
                ],
            ]);

        if ($options['sortable']) {
            // JS が無効で hidden が空のまま送信された場合のフォールバック。
            // コレクション内の位置をそのまま 1 始まりの表示順にする（NOT NULL 制約に落ちないようにする）。
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
                $Faq = $event->getData();
                if ($Faq instanceof Faq && $Faq->getSortNo() === null) {
                    $Faq->setSortNo(((int) $event->getForm()->getName()) + 1);
                }
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Faq::class,
            // 表示順をフォーム内のドラッグ＆ドロップで扱うか.
            // 単独編集画面では数値入力、コレクション（商品FAQ・カテゴリFAQ）では hidden ＋ 自動連番。
            'sortable' => false,
        ]);
        $resolver->setAllowedTypes('sortable', 'bool');
    }

    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_faq';
    }
}
