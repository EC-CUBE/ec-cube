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
use Eccube\Repository\TagRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class ProductTag extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var TagRepository
     */
    protected $tagRepository;

    /**
     * CategoryType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig, TagRepository $tagRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->tagRepository = $tagRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $Tag = $event->getData();
            $id = $Tag->getId();
            if (null !== $id) {
                $form = $event->getForm();
                $form->add('id', IntegerType::class, [
                    'mapped' => false,
                    'constraints' => [
                        new Assert\NotBlank(),
                    ],
                    'data' => $id,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $Tag = $event->getData();
            if (null === $Tag->getId()) {
                return;
            }

            $form = $event->getForm();
            $newId = $form['id']->getData();
            $originId = $Tag->getId();

            if ($newId !== $originId) {
                $count = $this->tagRepository->createQueryBuilder('t')
                    ->select('COUNT(t.id)')
                    ->where('t.id = :id AND t.id <> :origin_id')
                    ->setParameter('id', $newId)
                    ->setParameter('origin_id', $originId)
                    ->getQuery()
                    ->getSingleScalarResult();

                if ($count > 0) {
                    $form['id']->addError(new FormError('このIDはすでに利用されています。'));
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_product_tag';
    }
}
