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


namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Controller\AbstractController;
use Eccube\Entity\MailTemplate;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailTemplateRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MailController
 *
 * @package Eccube\Controller\Admin\Setting\Shop
 */
class MailController extends AbstractController
{
    /**
     * @var MailTemplateRepository
     */
    protected $mailTemplateRepository;

    /**
     * MailController constructor.
     *
     * @param MailTemplateRepository $mailTemplateRepository
     */
    public function __construct(MailTemplateRepository $mailTemplateRepository)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @Route("/%admin_route%/setting/shop/mail", name="admin_setting_shop_mail")
     * @Route("/%admin_route%/setting/shop/mail/{id}", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_edit")
     * @Template("@admin/Setting/Shop/mail.twig")
     */
    public function index(Request $request, MailTemplate $Mail = null)
    {
        $builder = $this->formFactory
            ->createBuilder(MailType::class, $Mail);

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'Mail' => $Mail,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form['template']->setData($Mail);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // 新規登録は現時点では未実装とする.
            if (is_null($Mail)) {
                $this->addError('admin.shop.mail.save.error', 'admin');

                return $this->redirectToRoute('admin_setting_shop_mail');
            }

            if ($form->isValid()) {

                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'Mail' => $Mail,
                    ),
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.shop.mail.save.complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop_mail_edit', array('id' => $Mail->getId()));
            }
        }

        return [
            'form' => $form->createView(),
            'id' => is_null($Mail) ? null : $Mail->getId(),
        ];
    }
}
