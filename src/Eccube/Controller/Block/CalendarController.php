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

namespace Eccube\Controller\Block;

use Eccube\Controller\AbstractController;
use Eccube\Repository\CalendarRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @var CalendarRepository
     */
    protected $calendarRepository;

    /**
     * CalendarController constructor.
     *
     *  @param CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * @Route("/block/calendar", name="block_calendar")
     * @Template("Block/calendar.twig")
     */
    public function index(Request $request)
    {
        // 当月と翌月で指定して定休日データ取る？
        $Calendars = $this->calendarRepository->getList();
//        $builder = $this->formFactory
//            ->createNamedBuilder('', SearchProductBlockType::class)
//            ->setMethod('GET');
//
//        $request = $this->requestStack->getMasterRequest();
//
//        $form = $builder->getForm();
//        $form->handleRequest($request);

        return [
            'Calendars' => $Calendars,
        ];
    }
}
