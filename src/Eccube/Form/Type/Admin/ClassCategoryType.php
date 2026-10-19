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
use Eccube\Entity\ClassCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ClassCategoryType extends AbstractType
{
    public function __construct(protected EccubeConfig $eccubeConfig)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $options
     */
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                ],
            ])
            ->add('backend_name', TextType::class, [
                'label' => 'classname.label.backend_name',
                'required' => false,
                'constraints' => [
                    new Assert\Length(max: $this->eccubeConfig['eccube_stext_len']),
                ],
            ])
            ->add('visible', ChoiceType::class, [
                'label' => false,
                'choices' => ['common.label.display' => true, 'common.label.hide' => false],
                'required' => true,
                'expanded' => false,
            ])
        ;

        // 新規登録の画面は表示/非表示を送らず, ClassCategoryRepository::save() が表示にする.
        // 登録済みの規格分類で表示/非表示を空にすると null のまま保存されるため, 入力エラーにする.
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $ClassCategory = $event->getData();
            $form = $event->getForm()->get('visible');
            if ($ClassCategory instanceof ClassCategory && $ClassCategory->getId() && null === $form->getData()) {
                $form->addError(new FormError(trans('This value should not be null.', [], 'validators')));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClassCategory::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getBlockPrefix(): string
    {
        return 'admin_class_category';
    }
}
