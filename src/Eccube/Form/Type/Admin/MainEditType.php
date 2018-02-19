<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('name', TextType::class, array(
                'label' => 'mainedit.label.name',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                ),
            ))
            ->add('url', TextType::class, array(
                'label' => 'URL',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+(?<!\/)$/',
                    )),
                ),
            ))
            ->add('file_name', TextType::class, array(
                'label' => 'mainedit.label.file_name',
                'required' => true,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                    new Assert\Regex(array(
                        'pattern' => '/^([0-9a-zA-Z_\-]+\/?)+$/',
                    )),
                ),
            ))
            ->add('tpl_data', HiddenType::class, array(
                'label' => false,
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new TwigLint(),
                ],
            ))
            ->add('author', TextType::class, array(
                'label' => 'author',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                ),
            ))
            ->add('description', TextType::class, array(
                'label' => 'description',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                ),
            ))
            ->add('keyword', TextType::class, array(
                'label' => 'keyword',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    )),
                ),
            ))
            ->add('meta_robots', TextType::class, array(
                'label' => 'robots',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_stext_len'],
                    ))
                )
            ))->add('meta_tags', TextAreaType::class, array(
                'label' => '追加metaタグ',
                'required' => false,
                'constraints' => array(
                    new Assert\Length(array(
                        'max' => $this->eccubeConfig['eccube_lltext_len'],
                    ))
                )
            ))
            ->add('DeviceType', EntityType::class, array(
                'class' => 'Eccube\Entity\Master\DeviceType',
                'choice_label' => 'id',
            ))
            ->add('id', HiddenType::class)
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
                    $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_SP);

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
                    if ($Layout->getDeviceType()->getId() == DeviceType::DEVICE_TYPE_SP) {
                        $form['SpLayout']->setData($Layout);
                    }
                }
            })
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $url = $form['url']->getData();
                $DeviceType = $form['DeviceType']->getData();
                $page_id = $form['id']->getData();

                $qb = $this->entityManager->createQueryBuilder();
                $qb->select('p')
                    ->from('Eccube\\Entity\\Page', 'p')
                    ->where('p.url = :url')
                    ->setParameter('url', $url)
                    ->andWhere('p.DeviceType = :DeviceType')
                    ->setParameter('DeviceType', $DeviceType);
                if (is_null($page_id)) {
                    $qb
                        ->andWhere('p.id IS NOT NULL');
                } else {
                    $qb
                        ->andWhere('p.id <> :page_id')
                        ->setParameter('page_id', $page_id);
                }

                $Page = $qb
                    ->getQuery()
                    ->getResult();
                if (count($Page) > 0) {
                    $form['url']->addError(new FormError('mainedit.text.error.url_exists'));
                }
            });
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Page::class,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'main_edit';
    }
}
