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
use Eccube\Entity\BaseInfo;
use Eccube\Entity\TaxRule;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\TaxRuleType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\TaxRuleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param BaseInfoRepository $baseInfoRepository
     * @param TaxRuleRepository $taxRuleRepository
     */
    public function __construct(BaseInfoRepository $baseInfoRepository, TaxRuleRepository $taxRuleRepository)
    {
        $this->BaseInfo = $baseInfoRepository->get();
        $this->taxRuleRepository = $taxRuleRepository;
    }

    /**
     * 税率設定の初期表示・登録
     *
     * @Route("/%eccube_admin_route%/setting/shop/tax", name="admin_setting_shop_tax", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/shop/tax/new", name="admin_setting_shop_tax_new", methods={"GET", "POST"})
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

                $this->addSuccess('admin.common.save_complete', 'admin');

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

                    $this->addSuccess('admin.common.save_complete', 'admin');

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
     * @Route("/%eccube_admin_route%/setting/shop/tax/{id}/delete", requirements={"id" = "\d+"}, name="admin_setting_shop_tax_delete", methods={"DELETE"})
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

            $this->addSuccess('admin.common.delete_complete', 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_tax');
    }
}
