<?php

namespace Eccube\Controller\Admin\Basis;

use Eccube\Application;
use Eccube\Controller\AbstractController;

class TaxRuleController extends AbstractController
{
    private $main_title;
    private $sub_title;
    const DEFAULT_TAX_RULE_ID = 0;

    public $form;

    public function __construct()
    {
        $this->main_title = '基本情報管理';
        $this->sub_title = '税率設定';

        $this->tpl_subno = 'tax';
        $this->tpl_mainno = 'basis';
    }

    /**
     * 税率設定の初期表示・登録
     *
     * @param Application $app
     * @param null $tax_rule_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Application $app, $tax_rule_id = null)
    {
        if ($tax_rule_id == null) {
            $TaxRule = $app['eccube.repository.tax_rule']->newTaxRule();
        } else {
            $TaxRule = $app['eccube.repository.tax_rule']->getById($tax_rule_id);
        }

        $builder = $app['form.factory']->createBuilder('tax_rule', $TaxRule);
        if ($tax_rule_id == self::DEFAULT_TAX_RULE_ID && $tax_rule_id <> null) {
            // 基本税率設定は適用日時の変更は行わない
            $builder = $builder->remove('apply_date');
        }

        /* @var $form \Symfony\Component\Form\FormInterface */
        $form = $builder->getForm();

        if ($app['request']->getMethod() === 'POST') {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $TaxRule->setMemberId(1); //FIXME: 管理画面ログイン認証完成後に対応
                $app['orm.em']->persist($TaxRule);
                $app['orm.em']->flush();
                $app['session']->getFlashBag()->add('tax_rule.complete', 'admin.register.complete');

                return $app->redirect($app['url_generator']->generate('admin_basis_tax_rule'));
            }
        }

        // 共通税率一覧
        $TaxRules = $app['eccube.repository.tax_rule']->getList();

        return $app['twig']->render('Admin/Basis/tax_rule.twig', array(
            'tpl_maintitle' => $this->main_title,
            'tpl_subtitle' => $this->sub_title,
            'tax_rule_id' => $tax_rule_id,
            'TaxRules' => $TaxRules,
            'form' => $form->createView(),
        ));
    }

    /**
     * 特定の共通税率の削除
     *
     * @param Application $app
     * @param $tax_rule_id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, $tax_rule_id)
    {

        if ($tax_rule_id != self::DEFAULT_TAX_RULE_ID ) {
            $TaxRule = $app['eccube.repository.tax_rule']->getById($tax_rule_id);
            $app['eccube.repository.tax_rule']->delete($TaxRule);
        }

        return $app->redirect($app['url_generator']->generate('admin_basis_tax_rule'));
    }

    public function parameterEdit(Application $app, $tax_rule_id)
    {
        //TODO: 商品別税率設定のパラメーター設定の更新、更新後indexへリダイレクト
        return new \Symfony\Component\HttpFoundation\Response('parameterEdit');
    }

}