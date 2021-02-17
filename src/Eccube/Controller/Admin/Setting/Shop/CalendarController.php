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

namespace Eccube\Controller\Admin\Setting\Shop;

use Eccube\Controller\AbstractController;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\CalendarType;
use Eccube\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Eccube\Entity\Calendar;

/**
 * Class CalendarController
 */
class CalendarController extends AbstractController
{
    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * TaxRuleController constructor.
     *
     * @param CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * カレンダー設定の初期表示・登録
     *
     * @Route("/%eccube_admin_route%/setting/shop/calendar", name="admin_setting_shop_calendar")
     * @Route("/%eccube_admin_route%/setting/shop/calendar/new", name="admin_setting_shop_calendar_new")
     * @Template("@admin/Setting/Shop/calendar.twig")
     */
    public function index(Request $request)
    {
        $Calendar = new Calendar();
        $builder = $this->formFactory
            ->createBuilder(CalendarType::class, $Calendar);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Calendar' => $Calendar,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CALENDAR_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $mode = $request->get('mode');
        if ($mode != 'edit_inline') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($Calendar);
                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'Calendar' => $Calendar,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_CALENDAR_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop_calendar');
            }
        }

        return [
            'Calendar' => $Calendar,
            'form' => $form->createView(),
        ];
    }
}
