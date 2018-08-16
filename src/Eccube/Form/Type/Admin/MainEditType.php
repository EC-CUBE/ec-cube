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

namespace Eccube\Form\Type\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Eccube\Common\EccubeConfig;
use Eccube\Entity\Layout;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Page;
use Eccube\Form\Validator\TwigLint;
use Eccube\Repository\Master\DeviceTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class MainEditType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * MainEditType constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param DeviceTypeRepository $deviceTypeRepository
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        DeviceTypeRepository $deviceTypeRepository,
        EccubeConfig $eccubeConfig
    ) {
        $this->entityManager = $entityManager;
        $this->deviceTypeRepository = $deviceTypeRepository;
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'mainedit.label.name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('url', TextType::class, [
                'label' => 'URL',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+(?<!\/)$/',
                    ]),
                ],
            ])
            ->add('file_name', TextType::class, [
                'label' => 'mainedit.label.file_name',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+$/',
                    ]),
                ],
            ])
            ->add('tpl_data', TextareaType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\NotBlank(),
                    new TwigLint(),
                ],
            ])
            ->add('author', TextType::class, [
                'label' => 'author',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('description', TextType::class, [
                'label' => 'description',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('keyword', TextType::class, [
                'label' => 'keyword',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])
            ->add('meta_robots', TextType::class, [
                'label' => 'robots',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ]),
                ],
            ])->add('meta_tags', TextAreaType::class, [
                'label' => '追加metaタグ',
                'required' => false,
                'constraints' => [
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ]),
                ],
            ])
            ->add('PcLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'label' => 'PC',
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

                    return $er->createQueryBuilder('l')
                        ->where('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->add('SpLayout', EntityType::class, [
                'mapped' => false,
                'placeholder' => '---',
                'required' => false,
                'label' => 'mainedit.label.smartphone',
                'class' => Layout::class,
                'query_builder' => function (EntityRepository $er) {
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_MB);

                    return $er->createQueryBuilder('l')
                        ->where('l.DeviceType = :DeviceType')
                        ->setParameter('DeviceType', $DeviceType)
                        ->orderBy('l.id', 'DESC');
                },
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $Page = $event->getData();
                if (is_null($Page->getId())) {
                    return;
                }
                $form = $event->getForm();
                $Layouts = $Page->getLayouts();
                foreach ($Layouts as $Layout) {
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_PC) {
                        $form['PcLayout']->setData($Layout);
                    }
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_MB) {
                        $form['SpLayout']->setData($Layout);
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();

                /** @var Page $Page */
                $Page = $event->getData();

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('count(p)')
                    ->from('Eccube\\Entity\\Page', 'p')
                    ->where('p.url = :url')
                    ->andWhere('p.DeviceType = :DeviceType')
                    ->setParameter('url', $Page->getUrl())
                    ->setParameter('DeviceType', $Page->getDeviceType());

                if (null === $Page->getId()) {
                    $qb
                        ->andWhere('p.id IS NOT NULL');
                } else {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $Page->getId());
                }

                $count = $qb->getQuery()->getSingleScalarResult();
                if ($count > 0) {
                    $form['url']->addError(new FormError('mainedit.text.error.url_exists'));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'main_edit';
    }
}
