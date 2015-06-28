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
use Eccube\Application;
use Eccube\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaxRuleController extends AbstractController
{
    /**
     * 税率設定の初期表示・登録
     *
     * @param Application $app
     * @param Request $request
     * @param null $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request, $id = null)
    {
        $TargetTaxRule = null;

        if ($id == null) {
            $TargetTaxRule = $app['eccube.repository.tax_rule']->newTaxRule();
        } else {
            $TargetTaxRule = $app['eccube.repository.tax_rule']->find($id);
            if (is_null($TargetTaxRule)) {
                throw new NotFoundHttpException();
            }
        }

        /** @var  $BaseInfo \Eccube\Entity\BaseInfo */
        $BaseInfo = $app['eccube.repository.base_info']->get();

        $builder = $app['form.factory']
            ->createBuilder('tax_rule', $TargetTaxRule);

        $builder
            ->get('option_product_tax_rule')
            ->setData($BaseInfo->getOptionProductTaxRule());

        if ($TargetTaxRule->isDefaultTaxRule()) {
            // 基本税率設定は適用日時の変更は行わない
            $builder = $builder->remove('apply_date');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);
            if ($this->isValid($app['orm.em'], $form)) {
                $app['orm.em']->persist($TargetTaxRule);
                $app['orm.em']->flush();

                $app->addSuccess('admin.shop.tax.save.complete', 'admin');

                return $app->redirect($app->url('admin_setting_shop_tax'));
            }
        }

        // 共通税率一覧
        $TaxRules = $app['eccube.repository.tax_rule']->getList();

        return $app->render('Setting/Shop/tax_rule.twig', array(
            'TargetTaxRule' => $TargetTaxRule,
            'TaxRules' => $TaxRules,
            'form' => $form->createView(),
        ));
    }

    /**
     * 税率設定の削除
     *
     * @param Application $app
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, $id)
    {
        $TargetTaxRule = $app['eccube.repository.tax_rule']->find($id);
        if (!$TargetTaxRule->isDefaultTaxRule()) {
            $app['eccube.repository.tax_rule']->delete($TargetTaxRule);
            $app->addSuccess('admin.shop.tax.delete.complete', 'admin');
        }

        return $app->redirect($app->url('admin_setting_shop_tax'));
    }

    /**
     * 軽減税率の有効/無効設定
     *
     * @param Application $app
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editParameter(Application $app, Request $request)
    {
        $form = $app['form.factory']
            ->createBuilder('tax_rule')
            ->getForm();

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            // 軽減税率設定の項目のみ処理する
            $optionForm = $form->get('option_product_tax_rule');
            if ($optionForm->isValid()) {
                /** @var  $BaseInfo \Eccube\Entity\BaseInfo */
                $BaseInfo = $app['eccube.repository.base_info']->get();
                $BaseInfo->setOptionProductTaxRule($optionForm->getData());
                $app['orm.em']->flush();

                $app->addSuccess('admin.shop.tax.save.complete', 'admin');
            }
        }

        return $app->redirect($app->url('admin_setting_shop_tax'));
    }

    protected function isValid(EntityManager $em, Form $form)
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
        $qb = $em
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
