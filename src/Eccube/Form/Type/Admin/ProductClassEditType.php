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

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\ClassCategory;
use Eccube\Entity\ProductClass;
use Eccube\Form\DataTransformer;
use Eccube\Form\Type\Master\DeliveryDurationType;
use Eccube\Form\Type\Master\SaleTypeType;
use Eccube\Form\Type\PriceType;
use Eccube\Repository\BaseInfoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductClassEditType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * ProductClassEditType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        BaseInfoRepository $baseInfoRepository,
        EccubeConfig $eccubeConfig
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->baseInfoRepository = $baseInfoRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('checked', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
            ])
            ->add('code', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('stock', IntegerType::class, [
                'required' => false,
            ])
            ->add('stock_unlimited', CheckboxType::class, [
                'label' => 'admin.product.stock_unlimited__short',
                'required' => false,
            ])
            ->add('sale_limit', NumberType::class, [
                'required' => false,
            ])
            ->add('price01', PriceType::class, [
                'required' => false,
            ])
            ->add('price02', PriceType::class, [
                'required' => false,
            ])
            ->add('tax_rate', TextType::class, [
                'required' => false,
            ])
            ->add('delivery_fee', PriceType::class, [
                'required' => false,
            ])
            ->add('sale_type', SaleTypeType::class, [
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('delivery_duration', DeliveryDurationType::class, [
                'required' => false,
                'placeholder' => 'common.select__unspecified',
            ]);

        $transformer = new DataTransformer\EntityToIdTransformer($this->entityManager, ClassCategory::class);
        $builder
            ->add($builder->create('ClassCategory1', HiddenType::class)
                ->addModelTransformer($transformer)
            )
            ->add($builder->create('ClassCategory2', HiddenType::class)
                ->addModelTransformer($transformer)
            );

        // 各行の個別税率設定.
        $this->setTaxRate($builder);

        // 各行の登録チェックボックス.
        $this->setCheckbox($builder);

        // バリデーションの設定. 各行にチェックが付いているときだけ検証する.
        $this->addValidations($builder);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductClass::class,
        ]);
    }

    /**
     * 各行の個別税率設定の制御.
     *
     * @param FormBuilderInterface $builder
     */
    protected function setTaxRate(FormBuilderInterface $builder)
    {
        if (!$this->baseInfoRepository->get()->isOptionProductTaxRule()) {
            return;
        }
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (!$data instanceof ProductClass) {
                return;
            }
            if ($data->getId() && $data->getTaxRule()) {
                $form = $event->getForm();
                $form['tax_rate']->setData($data->getTaxRule()->getTaxRate());
            }
        });
    }

    /**
     * 各行の登録チェックボックスの制御.
     *
     * @param FormBuilderInterface $builder
     */
    protected function setCheckbox(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            if (!$data instanceof ProductClass) {
                return;
            }
            if ($data->getId() && $data->isVisible()) {
                $form = $event->getForm();
                $form['checked']->setData(true);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();
            $data->setVisible($form['checked']->getData() ? true : false);
        });
    }

    protected function addValidations(FormBuilderInterface $builder)
    {
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $form->getData();

            if (!$form['checked']->getData()) {
                // チェックがついていない場合はバリデーションしない.
                return;
            }

            // 在庫数
            $errors = $this->validator->validate($data['stock'], [
                new Assert\Regex([
                    'pattern' => "/^\d+$/u",
                    'message' => 'form_error.numeric_only',
                ]),
            ]);
            $this->addErrors('stock', $form, $errors);

            // 在庫数無制限
            if (empty($data['stock_unlimited']) && null === $data['stock']) {
                $form['stock_unlimited']->addError(new FormError(trans('admin.product.product_class_set_stock_quantity')));
            }

            // 販売制限数
            $errors = $this->validator->validate($data['sale_limit'], [
                new Assert\Length([
                    'max' => 10,
                ]),
                new Assert\GreaterThanOrEqual([
                    'value' => 1,
                ]),
                new Assert\Regex([
                    'pattern' => "/^\d+$/u",
                    'message' => 'form_error.numeric_only',
                ]),
            ]);
            $this->addErrors('sale_limit', $form, $errors);

            // 販売価格
            $errors = $this->validator->validate($data['price02'], [
                new Assert\NotBlank(),
            ]);

            $this->addErrors('price02', $form, $errors);

            // 税率
            $errors = $this->validator->validate($data['tax_rate'], [
                new Assert\Range(['min' => 0, 'max' => 100]),
                new Assert\Regex([
                    'pattern' => "/^\d+(\.\d+)?$/",
                    'message' => 'form_error.float_only',
                ]),
            ]);
            $this->addErrors('tax_rate', $form, $errors);

            // 販売種別
            $errors = $this->validator->validate($data['sale_type'], [
                new Assert\NotBlank(),
            ]);
            $this->addErrors('sale_type', $form, $errors);
        });
    }

    protected function addErrors($key, FormInterface $form, ConstraintViolationListInterface $errors)
    {
        foreach ($errors as $error) {
            $form[$key]->addError(new FormError($error->getMessage()));
        }
    }
}
