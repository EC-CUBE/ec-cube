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
use Eccube\Entity\BaseInfo;
use Eccube\Entity\TaxRule;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\TaxRuleType;
use Eccube\Repository\TaxRuleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TaxRuleController
 */
class TaxRuleController extends AbstractController
{
    /**
     * @var BaseInfo
     */
    protected $BaseInfo;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * TaxRuleController constructor.
     *
     * @param BaseInfo $BaseInfo
     * @param TaxRuleRepository $taxRuleRepository
     */
    public function __construct(BaseInfo $BaseInfo, TaxRuleRepository $taxRuleRepository)
    {
        $this->BaseInfo = $BaseInfo;
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * 税率設定の初期表示・登録
     *
     * @Route("/%eccube_admin_route%/setting/shop/tax", name="admin_setting_shop_tax")
     * @Route("/%eccube_admin_route%/setting/shop/tax/new", name="admin_setting_shop_tax_new")
     * @Template("@admin/Setting/Shop/tax_rule.twig")
     */
    public function index(Request $request)
    {
        $TargetTaxRule = $this->taxRuleRepository->newTaxRule();
        $builder = $this->formFactory
            ->createBuilder(TaxRuleType::class, $TargetTaxRule);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'BaseInfo' => $this->BaseInfo,
                'TargetTaxRule' => $TargetTaxRule,
            ],
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $mode = $request->get('mode');
        if ($mode != 'edit_inline') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $this->entityManager->persist($TargetTaxRule);
                $this->entityManager->flush();

                $event = new EventArgs(
                    [
                        'form' => $form,
                        'BaseInfo' => $this->BaseInfo,
                        'TargetTaxRule' => $TargetTaxRule,
                    ],
                    $request
                );
                $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_COMPLETE, $event);

                $this->addSuccess('admin.shop.tax.save.complete', 'admin');

                return $this->redirectToRoute('admin_setting_shop_tax');
            }
        }

        // 共通税率一覧
        $TaxRules = $this->taxRuleRepository->getList();

        // edit tax rule form
        $forms = [];
        $errors = [];
        /** @var TaxRule $TaxRule */
        foreach ($TaxRules as $TaxRule) {
            /* @var $builder \Symfony\Component\Form\FormBuilderInterface */
            $builder = $this->formFactory->createBuilder(TaxRuleType::class, $TaxRule);
            if ($TaxRule->isDefaultTaxRule()) {
                $builder->remove('apply_date');
            }
            $editTaxRuleForm = $builder->getForm();
            // error number
            $error = 0;
            if ($mode == 'edit_inline'
                && $request->getMethod() === 'POST'
                && (string) $TaxRule->getId() === $request->get('tax_rule_id')
                ) {
                $editTaxRuleForm->handleRequest($request);
                if ($editTaxRuleForm->isValid()) {
                    $taxRuleData = $editTaxRuleForm->getData();

                    $this->entityManager->persist($taxRuleData);
                    $this->entityManager->flush();

                    $this->addSuccess('admin.shop.tax.save.complete', 'admin');

                    return $this->redirectToRoute('admin_setting_shop_tax');
                }
                $error = count($editTaxRuleForm->getErrors(true));
            }

            $forms[$TaxRule->getId()] = $editTaxRuleForm->createView();
            $errors[$TaxRule->getId()] = $error;
        }

        return [
            'TargetTaxRule' => $TargetTaxRule,
            'TaxRules' => $TaxRules,
            'form' => $form->createView(),
            'forms' => $forms,
            'errors' => $errors,
        ];
    }

    /**
     * 税率設定の削除
     *
     * @Method("DELETE")
     * @Route("/%eccube_admin_route%/setting/shop/tax/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_tax_delete")
     */
    public function delete(Request $request, TaxRule $TaxRule)
    {
        $this->isTokenValid();

        if (!$TaxRule->isDefaultTaxRule()) {
            $this->taxRuleRepository->delete($TaxRule);

            $event = new EventArgs(
                [
                    'TargetTaxRule' => $TaxRule,
                ],
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.shop.tax.delete.complete', 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_tax');
    }
}
