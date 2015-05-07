<?php

namespace Eccube\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentRegisterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('method', 'text', array(
                'label' => '支払方法',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('charge', 'integer', array(
                'label' => '手数料',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ))
            ->add('rule_max', 'integer', array(
                'label' => '利用条件（円）',
                'required' => true,
            ))
            ->add('upper_rule', 'integer', array(
                'label' => '〜利用条件（円）',
                'required' => true,
            ))
            // カード会社などの上限
            ->add('upper_rule_max', 'hidden')
            // カード会社などの下限
            ->add('rule_min', 'hidden')
            ->add('charge_flg', 'hidden')
            ->add('payment_image_file', 'file', array(
                'label' => 'ロゴ画像',
                'mapped' => false,
            ))

            ->addEventListener(FormEvents::POST_BIND, function ($event) {
                $form = $event->getForm();
                $ruleMax = $form['rule_max']->getData();
                $ruleMin = $form['rule_min']->getData();
                if ($ruleMin != '' && $ruleMax < $ruleMin) {
                    $form['rule_max']->addError(new FormError('利用条件(下限)は' . $ruleMin .'円以下にしてください。'));
                }
            })
            ->addEventListener(FormEvents::POST_BIND, function ($event) {
                $form = $event->getForm();
                $upperRule = $form['upper_rule']->getData();
                $upperRuleMax = $form['upper_rule_max']->getData();
                if ($upperRuleMax != '' && $upperRule < $upperRuleMax) {
                    $form['upper_rule']->addError(new FormError('利用条件(上限)は' . $upperRuleMax .'円以下にしてください。'));
                }
            })
            ->addEventListener(FormEvents::POST_BIND, function ($event) {
                $form = $event->getForm();
                $upperRule = $form['upper_rule']->getData();
                $ruleMax = $form['rule_max']->getData();
                if ($ruleMax > $upperRule) {
                    $form['upper_rule']->addError(new FormError('利用条件(上限)は利用条件（下限）以上にしてください。'));
                }
            })
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Eccube\Entity\Payment',
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'payment_register';
    }
}
