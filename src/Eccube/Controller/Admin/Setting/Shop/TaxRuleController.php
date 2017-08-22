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

use Doctrine\ORM\EntityManager;
use Eccube\Annotation\Component;
use Eccube\Annotation\Inject;
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Eccube\Entity\TaxRule;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\TaxRuleType;
use Eccube\Repository\BaseInfoRepository;
use Eccube\Repository\TaxRuleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Component
 * @Route(service=TaxRuleController::class)
 */
class TaxRuleController extends AbstractController
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @Inject("eccube.event.dispatcher")
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @Inject(BaseInfoRepository::class)
     * @var BaseInfoRepository
     */
    protected $baseInfoRepository;

    /**
     * @Inject(TaxRuleRepository::class)
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * 税率設定の初期表示・登録
     *
     * @Route("/{_admin}/setting/shop/tax", name="admin_setting_shop_tax")
     * @Route("/{_admin}/setting/shop/tax/new", name="admin_setting_shop_tax_new")
     * @Route("/{_admin}/setting/shop/tax/{id}/edit", requirements={"id":"\d+"}, name="admin_setting_shop_tax_edit")
     * @Template("Setting/Shop/tax_rule.twig")
     */
    public function index(Application $app, Request $request, TaxRule $TargetTaxRule = null)
    {
        if (is_null($TargetTaxRule)) {
            $TargetTaxRule = $this->taxRuleRepository->newTaxRule();
        }

        $BaseInfo = $this->baseInfoRepository->get();

        $builder = $this->formFactory
            ->createBuilder(TaxRuleType::class, $TargetTaxRule);

        $builder
            ->get('option_product_tax_rule')
            ->setData($BaseInfo->getOptionProductTaxRule());

        if ($TargetTaxRule->isDefaultTaxRule()) {
            // 基本税率設定は適用日時の変更は行わない
            $builder = $builder->remove('apply_date');
        }

        $event = new EventArgs(
            array(
                'builder' => $builder,
                'BaseInfo' => $BaseInfo,
                'TargetTaxRule' => $TargetTaxRule,
            ),
            $request
        );
        $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE, $event);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $this->isValid($form)) {

            $this->entityManager->persist($TargetTaxRule);
            $this->entityManager->flush();

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'BaseInfo' => $BaseInfo,
                    'TargetTaxRule' => $TargetTaxRule,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_INDEX_COMPLETE, $event);

            $app->addSuccess('admin.shop.tax.save.complete', 'admin');

            return $app->redirect($app->url('admin_setting_shop_tax'));
        }

        // 共通税率一覧
        $TaxRules = $this->taxRuleRepository->getList();

        return [
            'TargetTaxRule' => $TargetTaxRule,
            'TaxRules' => $TaxRules,
            'form' => $form->createView(),
        ];
    }

    /**
     * 税率設定の削除
     *
     * @Method("DELETE")
     * @Route("/{_admin}/setting/shop/tax/{id}/delete", requirements={"id":"\d+"}, name="admin_setting_shop_tax_delete")
     */
    public function delete(Application $app, Request $request, TaxRule $TaxRule)
    {
        $this->isTokenValid($app);

        if (!$TaxRule->isDefaultTaxRule()) {
            $this->taxRuleRepository->delete($TaxRule);

            $event = new EventArgs(
                array(
                    'TargetTaxRule' => $TaxRule,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_DELETE_COMPLETE, $event);

            $app->addSuccess('admin.shop.tax.delete.complete', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_tax'));
    }

    /**
     * 軽減税率の有効/無効設定
     *
     * @Method("POST")
     * @Route("/{_admin}/setting/shop/tax/edit_param", name="admin_setting_shop_tax_edit_param")
     */
    public function editParameter(Application $app, Request $request)
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

            $BaseInfo = $this->baseInfoRepository->get();
            $BaseInfo->setOptionProductTaxRule($form['option_product_tax_rule']->getData());
            $this->entityManager->flush($BaseInfo);

            $event = new EventArgs(
                array(
                    'form' => $form,
                    'BaseInfo' => $BaseInfo,
                ),
                $request
            );
            $this->eventDispatcher->dispatch(
                EccubeEvents::ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_COMPLETE,
                $event
            );

            $app->addSuccess('admin.shop.tax.save.complete', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_tax'));
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
            $form['apply_date']->addError(new FormError('既に同じ適用日時で登録されています。'));

            return false;
        }

        return true;
    }
}
