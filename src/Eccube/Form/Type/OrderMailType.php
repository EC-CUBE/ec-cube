<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class OrderMailType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('template', 'mail_template', array(
                'label' => 'テンプレート',
                'required' => true,
                'mapped' => false,
            ))
            ->add('subject', 'text', array(
                'label' => 'タイトル',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('header', 'textarea', array(
                'label' => 'ヘッダー',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('footer', 'textarea', array(
                'label' => 'フッター',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('footer', 'textarea', array(
                'label' => 'フッター',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->addEventSubscriber(new \Eccube\Event\FormEventSubscriber());
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'order_mail';
    }
}
