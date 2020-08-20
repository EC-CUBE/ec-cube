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

use Eccube\Entity\TaxRule;
use Eccube\Form\Type\Master\RoundingTypeType;
use Eccube\Repository\TaxRuleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class TaxRuleType
 */
class TaxRuleType extends AbstractType
{
    protected $taxRuleRepository;

    public function __construct(TaxRuleRepository $taxRuleRepository)
    {
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tax_rate', IntegerType::class, [
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Range(['min' => 0]),
                    new Assert\Regex([
                        'pattern' => "/^\d+(\.\d+)?$/u",
                        'message' => 'form_error.float_only',
                    ]),
                ],
            ])
            ->add('rounding_type', RoundingTypeType::class, [
                'required' => true,
            ])
            ->add('apply_date', DateTimeType::class, [
                'date_widget' => 'choice',
                'input' => 'datetime',
                'format' => 'yyyy-MM-dd HH:mm',
                'years' => range(date('Y'), date('Y') + 10),
                'placeholder' => [
                    'year' => '----', 'month' => '--', 'day' => '--',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var TaxRule $TaxRule */
            $TaxRule = $event->getData();
            $qb = $this->taxRuleRepository->createQueryBuilder('t');
            $qb
                ->select('count(t.id)')
                ->where('t.apply_date = :apply_date')
                ->setParameter('apply_date', $TaxRule->getApplyDate());

            if ($TaxRule->getId()) {
                $qb
                    ->andWhere('t.id <> :id')
                    ->setParameter('id', $TaxRule->getId());
            }
            $count = $qb->getQuery()
                ->getSingleScalarResult();
            if ($count > 0) {
                $form = $event->getForm();
                $form['apply_date']->addError(new FormError(trans('admin.setting.shop.tax.apply_date.available_error')));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TaxRule::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'tax_rule';
    }
}
