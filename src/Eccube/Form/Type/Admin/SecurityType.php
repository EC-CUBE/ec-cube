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

use Eccube\Common\EccubeConfig;
use Eccube\Util\StringUtil;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityType extends AbstractType
{
    /**
     * @var EccubeConfig
     */
    protected $eccubeConfig;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * SecurityType constructor.
     * @param EccubeConfig $eccubeConfig
     * @param ValidatorInterface $validator
     */
    public function __construct(EccubeConfig $eccubeConfig, ValidatorInterface $validator, RequestStack $requestStack)
    {
        $this->eccubeConfig = $eccubeConfig;
        $this->validator = $validator;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allowHosts = $this->eccubeConfig->get('eccube_admin_allow_hosts');
        $allowHosts = implode("\n", $allowHosts);
        $builder
            ->add('admin_route_dir', TextType::class, array(
                'label' => 'security.label.directory_name',
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('max' => $this->eccubeConfig['eccube_stext_len'])),
                    new Assert\Regex(array(
                       'pattern' => "/^[0-9a-zA-Z]+$/",
                   )),
                ),
                'data' => $this->eccubeConfig->get('eccube_admin_route'),
            ))
            ->add('admin_allow_hosts', TextareaType::class, array(
                'required' => false,
                'label' => 'security.label.ip_restriction',
                'constraints' => array(
                    new Assert\Length(array('max' => $this->eccubeConfig['eccube_ltext_len'])),
                ),
                'data' => $allowHosts,
            ))
            ->add('force_ssl', CheckboxType::class, array(
                'label' => 'security.label.ssl_mandatory',
                'required' => false,
                'data' => $this->eccubeConfig->get('eccube_force_ssl'),
            ))
            ->addEventListener(FormEvents::POST_SUBMIT, function ($event) {
                $form = $event->getForm();
                $data = $form->getData();

                $ips = preg_split("/\R/", $data['admin_allow_hosts'], null, PREG_SPLIT_NO_EMPTY);

                foreach($ips as $ip) {
                    $errors = $this->validator->validate($ip, array(
                            new Assert\Ip(),
                        )
                    );
                    if ($errors->count() != 0) {
                        $form['admin_allow_hosts']->addError(new FormError(trans('security.text.error.not_ipv4', array('%ip%' => $ip))));
                    }
                }

                $request = $this->requestStack->getCurrentRequest();
                if ($data['force_ssl'] && !$request->isSecure()) {
                    $form['force_ssl']->addError(new FormError(trans('security.text.error.not_https')));
                }
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'admin_security';
    }
}
