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

namespace Eccube\Form\Type;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\BaseInfo;
use Eccube\Repository\BaseInfoRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class KanaType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var BaseInfo
     */
    protected $baseInfo;

    /**
     * KanaType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     * @param BaseInfoRepository $baseInfoRepository
     */
    public function __construct(EccubeConfig $eccubeConfig, BaseInfoRepository $baseInfoRepository)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->baseInfo = $baseInfoRepository->get();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // ひらがなをカタカナに変換する
        // 引数はmb_convert_kanaのもの
        $builder->addEventSubscriber(new \Eccube\Form\EventListener\ConvertKanaListener('CV'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => $this->baseInfo->isOptionRequireKana(),
            'lastname_options' => [
                'attr' => [
                    'placeholder' => 'common.last_name_kana',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        'message' => 'form_error.kana_only',
                    ]),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_kana_len'],
                    ]),
                ],
            ],
            'firstname_options' => [
                'attr' => [
                    'placeholder' => 'common.first_name_kana',
                ],
                'constraints' => [
                    new Assert\Regex([
                        'pattern' => '/^[ァ-ヶｦ-ﾟー]+$/u',
                        'message' => 'form_error.kana_only',
                    ]),
                    new Assert\Length([
                        'max' => $this->eccubeConfig['eccube_kana_len'],
                    ]),
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return NameType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'kana';
    }
}
