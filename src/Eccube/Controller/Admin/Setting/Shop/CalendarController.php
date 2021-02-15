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
use Eccube\Form\Type\Admin\CalendarType;
use Eccube\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     //* @Route("/%eccube_admin_route%/setting/shop/calendar/new", name="admin_setting_shop_calendar_new")
     * @Template("@admin/Setting/Shop/calendar_rule.twig")
     */
    public function index(Request $request)
    {

        $Calendar = new \Eccube\Entity\Calendar();
        $builder = $this->formFactory
            ->createBuilder(CalendarType::class, $Calendar);

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }
}
