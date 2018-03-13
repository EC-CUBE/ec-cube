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
use Eccube\Util\CacheUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TaxRuleController
 *
 * @package Eccube\Controller\Admin\Setting\Shop
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
     * @Route("/%eccube_admin_route%/setting/shop/tax/{id}/edit", requirements={"id" = "\d+"}, name="admin_setting_shop_tax_edit")
     * @Template("@admin/Setting/Shop/tax_rule.twig")
     */
    public function index(Request $request, $id = null)
    {
        if (is_null($id)) {
            $TargetTaxRule = $this->taxRuleRepository->newTaxRule();
        } else {
            $TargetTaxRule = $this->taxRuleRepository->find($id);
            if (is_null($TargetTaxRule)) {
                throw new NotFoundHttpException();
            }
        }

        $builder = $this->formFactory
            ->createBuilder(TaxRuleType::class, $TargetTaxRule);

        $builder
            ->get('option_product_tax_rule')
            ->setData($this->BaseInfo->isOptionProductTaxRule());

        if ($TargetTaxRule->isDefaultTaxRule()) {
            // 基本税率設定は適用日時の変更は行わない
            $builder = $builder->remove('apply_date');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'BaseInfo' => $this->BaseInfo,
                'TargetTaxRule' => $TargetTaxRule,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();

        $mode = $request->get('mode');
        if ($mode != 'edit_inline') {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $this->isValid($form)) {
                $this->entityManager->persist($TargetTaxRule);
                $this->entityManager->flush();

                $event = new EventArgs(
                    array(
                        'form' => $form,
                        'BaseInfo' => $this->BaseInfo,
                        'TargetTaxRule' => $TargetTaxRule,
                    ),
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
        $forms = array();
        $errors = array();
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
                && (string)$TaxRule->getId() === $request->get('tax_rule_id')
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
            'errors' => $errors
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
                array(
                    'TargetTaxRule' => $TaxRule,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_DELETE_COMPLETE, $event);

            $this->addSuccess('admin.shop.tax.delete.complete', 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_tax');
    }

    /**
     * 軽減税率の有効/無効設定
     *
     * @Method("POST")
     * @Route("/%eccube_admin_route%/setting/shop/tax/edit_param", name="admin_setting_shop_tax_edit_param")
     */
    public function editParameter(Request $request, CacheUtil $cacheUtil)
    {
        $builder = $this->formFactory
            ->createBuilder(TaxRuleType::class);

        $event = new EventArgs(
            array(
                'builder' => $builder,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        // 軽減税率設定の項目のみ処理する
        if ($form->isSubmitted() && $form['option_product_tax_rule']->isValid()) {

            $this->BaseInfo->setOptionProductTaxRule($form['option_product_tax_rule']->getData());
            $this->entityManager->persist($this->BaseInfo);
            $this->entityManager->flush();

            $cacheUtil->clearCache();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'BaseInfo' => $this->BaseInfo,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(
                EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_COMPLETE,
                $event
            );

            $this->addSuccess('admin.shop.tax.save.complete', 'admin');
        }

        return $this->redirectToRoute('admin_setting_shop_tax');
    }

    protected function isValid(Form $form)
    {
        if (!$form->isValid()) {
            return false;
        }
        /**
         * 同一日時のエラーチェック.
         */
        /** @var $TargetTaxRule \Eccube\Entity\TaxRule */
        $TargetTaxRule = $form->getData();
        $parameters = array();
        $parameters['apply_date'] = $TargetTaxRule->getApplyDate();
        $qb = $this->entityManager
            ->getRepository('Eccube\Entity\TaxRule')
            ->createQueryBuilder('t')
            ->select('count(t.id)')
            ->where('t.apply_date = :apply_date');
        // 編集時は, 編集対象をのぞいて検索.
        if ($TargetTaxRule->getId()) {
            $qb->andWhere('t.id <> :id');
            $parameters['id'] = $TargetTaxRule->getId();
        }
        $qb->setParameters($parameters);
        $count = $qb
            ->getQuery()
            ->getSingleScalarResult();
        // 同じ適用日時の登録データがあればエラーとする.
        if ($count > 0) {
            $form['apply_date']->addError(new FormError(trans('taxrule.text.error.date_not_available')));

            return false;
        }

        return true;
    }
}
