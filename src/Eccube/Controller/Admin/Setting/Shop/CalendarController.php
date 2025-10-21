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

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NoResultException;
use Eccube\Controller\AbstractController;
use Eccube\Entity\Calendar;
use Eccube\Form\Type\Admin\CalendarType;
use Eccube\Repository\CalendarRepository;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

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
     * CalendarController constructor.
     *
     *  @param CalendarRepository $calendarRepository
     */
    public function __construct(CalendarRepository $calendarRepository)
    {
        $this->calendarRepository = $calendarRepository;
    }

    /**
     * カレンダー設定の初期表示・登録
     *
     * @param Request $request
     *
     * @return RedirectResponse|array<string, mixed>
     */
    #[Route(path: '/%eccube_admin_route%/setting/shop/calendar', name: 'admin_setting_shop_calendar', methods: ['GET', 'POST'])]
    #[Route(path: '/%eccube_admin_route%/setting/shop/calendar/new', name: 'admin_setting_shop_calendar_new', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Setting/Shop/calendar.twig')]
    public function index(Request $request): RedirectResponse|array
    {
        $Calendar = new Calendar();
        $builder = $this->formFactory
            ->createBuilder(CalendarType::class, $Calendar);

        $form = $builder->getForm();

        $mode = $request->get('mode');
        if ($mode != 'edit_inline') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($Calendar);
                $this->entityManager->flush();

                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop_calendar');
            }
        }

        // カレンダーリスト取得
        $Calendars = $this->calendarRepository->getListOrderByIdDesc();

        $forms = [];
        $errors = [];
        /** @var Calendar $Calendar */
        foreach ($Calendars as $Calendar) {
            $builder = $this->formFactory->createBuilder(CalendarType::class, $Calendar);

            $editCalendarForm = $builder->getForm();

            // error number
            $error = 0;
            if ($mode == 'edit_inline'
                && $request->getMethod() === 'POST'
                && (string) $Calendar->getId() === $request->get('calendar_id')
            ) {
                $editCalendarForm->handleRequest($request);
                if ($editCalendarForm->isValid()) {
                    $calendarData = $editCalendarForm->getData();

                    $this->entityManager->persist($calendarData);
                    $this->entityManager->flush();

                    $this->addSuccess('admin.common.save_complete', 'admin');

                    return $this->redirectToRoute('admin_setting_shop_calendar');
                }
                $error = count($editCalendarForm->getErrors(true));
            }

            $forms[$Calendar->getId()] = $editCalendarForm->createView();
            $errors[$Calendar->getId()] = $error;
        }

        return [
            'Calendar' => $Calendar,
            'Calendars' => $Calendars,
            'form' => $form->createView(),
            'forms' => $forms,
            'errors' => $errors,
        ];
    }

    /**
     * カレンダー設定の削除
     *
     * @param Request $request
     * @param Calendar $Calendar
     *
     * @return RedirectResponse
     *
     * @throws NoResultException|ORMException
     */
    #[Route(path: '/%eccube_admin_route%/setting/shop/calendar/{id}/delete', name: 'admin_setting_shop_calendar_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(Request $request, Calendar $Calendar): RedirectResponse
    {
        $this->isTokenValid();
        $this->calendarRepository->delete($Calendar);
        $this->addSuccess('admin.common.delete_complete', 'admin');

        return $this->redirectToRoute('admin_setting_shop_calendar');
    }
}
