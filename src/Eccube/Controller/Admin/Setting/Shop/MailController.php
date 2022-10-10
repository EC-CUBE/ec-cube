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
use Eccube\Entity\MailTemplate;
use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Eccube\Form\Type\Admin\MailType;
use Eccube\Repository\MailTemplateRepository;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * Class MailController
 */
class MailController extends AbstractController
{
    /**
     * @var MailTemplateRepository
     */
    protected $mailTemplateRepository;

    /**
     * MailController constructor.
     *
     * @param MailTemplateRepository $mailTemplateRepository
     */
    public function __construct(MailTemplateRepository $mailTemplateRepository)
    {
        $this->mailTemplateRepository = $mailTemplateRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail", name="admin_setting_shop_mail", methods={"GET", "POST"})
     * @Route("/%eccube_admin_route%/setting/shop/mail/{id}", requirements={"id" = "\d+"}, name="admin_setting_shop_mail_edit", methods={"GET", "POST"})
     * @Template("@admin/Setting/Shop/mail.twig")
     */
    public function index(Request $request, Environment $twig, CacheUtil $cacheUtil, MailTemplate $Mail = null)
    {
        $builder = $this->formFactory
            ->createBuilder(MailType::class, $Mail);

        $event = new EventArgs(
            [
                'builder' => $builder,
                'Mail' => $Mail,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_INITIALIZE);

        $form = $builder->getForm();
        $form['template']->setData($Mail);
        $htmlFileName = $Mail ? $this->getHtmlFileName($Mail->getFileName()) : null;

        // ファイル名が空である場合は「Mail/」を入力
        if (is_null($form['file_name']->getData())) {
            $form['file_name']->setData('Mail/');
        }

        // POST時は登録 or 更新 or 削除
        if ('POST' === $request->getMethod()) {
            $beforeFileName = $form['file_name']->getData();
            $form->handleRequest($request);
            $newFileName = $form['file_name']->getData();
            $isRenamed = $beforeFileName !== $newFileName;

            $templatePath = $this->getParameter('eccube_theme_front_dir');
            $newFilePath = $templatePath.'/'.$newFileName;

            $deleteValue = $request->request->get('delete_template');
            
            // 削除
            if (!is_null($deleteValue) && $deleteValue === '1') {
                $mailTemplate = $this->mailTemplateRepository->find($Mail->getId());
                
                $this->entityManager->remove($mailTemplate);
                $this->entityManager->flush();
                
                $fs = new Filesystem();
                $fs->remove($templatePath.'/'.$beforeFileName);
                $fs->remove($templatePath.'/'.$this->getHtmlFileName($beforeFileName));

                $this->addSuccess('admin.common.delete_complete', 'admin');
                
                return $this->redirectToRoute('admin_setting_shop_mail');
            }
            else if ($form->isValid()) {
                $fs = new Filesystem();
                // ファイル名が「.twig」で終わっていない場合
                if (substr($newFileName, -5) !== '.twig')  {
                    $this->addError('admin.setting.shop.mail.invalid_twig_ext', 'admin');

                    return $this->redirectToRoute('admin_setting_shop_mail');
                }
                // ファイル名が「Mail/」から始まっていない場合
                else if (substr($newFileName, 0, 5) !== 'Mail/')  {
                    $this->addError('admin.setting.shop.mail.invalid_twig_beginning', 'admin');

                    return $this->redirectToRoute('admin_setting_shop_mail');
                }
                // ファイル名に「/」を含む場合
                else if (strpos(substr($newFileName, 5), '/') !== false)  {
                    $this->addError('admin.setting.shop.mail.invalid_twig_slash', 'admin');

                    return $this->redirectToRoute('admin_setting_shop_mail');
                }
                // 新しいファイル名、かつ、既に同じファイルパスが存在する場合
                else if ($isRenamed && $fs->exists($newFilePath)) {
                    $this->addError("admin.setting.shop.mail.already_exist_twig_file", 'admin');

                    return $this->redirectToRoute('admin_setting_shop_mail');
                }
                
                // 新規登録
                if (is_null($Mail)) {
                    $templateName = $form['name']->getData();
                    
                    $nowDateTime = new DateTime('now');
                    
                    // DBに書き込み
                    $mailTemplate = new MailTemplate();
                    $mailTemplate->setCreator(null);
                    $mailTemplate->setName($templateName);
                    $mailTemplate->setFileName($newFileName);
                    $mailTemplate->setMailSubject($form['mail_subject']->getData());
                    $mailTemplate->setCreateDate($nowDateTime);
                    $mailTemplate->setUpdateDate($nowDateTime);
                    $this->mailTemplateRepository->save($mailTemplate);

                    $this->entityManager->flush();

                    // ファイル生成・更新
                    $mailData = $form->get('tpl_data')->getData();
                    $mailData = StringUtil::convertLineFeed($mailData);
                    $fs->dumpFile($newFilePath, $mailData);
                    
                    // HTMLファイル用
                    $htmlMailData = $form->get('html_tpl_data')->getData();
                    if (!is_null($htmlMailData)) {
                        $htmlMailData = StringUtil::convertLineFeed($htmlMailData);
                        $fs->dumpFile($templatePath.'/'.$this->getHtmlFileName($newFileName), $htmlMailData);
                    }

                    $this->addSuccess('admin.common.save_complete', 'admin');
                    
                    return $this->redirectToRoute('admin_setting_shop_mail_edit', ['id' => $mailTemplate->getId()]);
                }
                // 更新
                else {
                    $this->entityManager->flush();

                    // ファイル生成・更新
                    $fs = new Filesystem();
                    $mailData = $form->get('tpl_data')->getData();
                    $mailData = StringUtil::convertLineFeed($mailData);
                    $fs->dumpFile($newFilePath, $mailData);
                    
                    // ファイル名の変更を行った場合、リネーム前のファイルは削除
                    if ($isRenamed) {
                        $fs->remove($templatePath.'/'.$beforeFileName);
                    }
                    
                    // HTMLファイル用
                    $htmlMailData = $form->get('html_tpl_data')->getData();
                    if (!is_null($htmlMailData)) {
                        $htmlMailData = StringUtil::convertLineFeed($htmlMailData);
                        $fs->dumpFile($templatePath.'/'.$this->getHtmlFileName($newFileName), $htmlMailData);
                        
                        // ファイル名の変更を行った場合、リネーム前のファイルは削除
                        if ($isRenamed) {
                            $beforeHtmlFileName = $this->getHtmlFileName($beforeFileName);
                            $fs->remove($templatePath.'/'.$beforeHtmlFileName);
                        }
                    }

                    $event = new EventArgs(
                        [
                            'form' => $form,
                            'Mail' => $Mail,
                            'templatePath' => $templatePath,
                            'filePath' => $newFileName,
                        ],
                        $request
                    );
                    $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_INDEX_COMPLETE);

                    $this->addSuccess('admin.common.save_complete', 'admin');

                    // キャッシュの削除
                    $cacheUtil->clearTwigCache();

                    return $this->redirectToRoute('admin_setting_shop_mail_edit', ['id' => $Mail->getId()]);
                }
            }
        }
        // GET時は表示のみ
        else {
            if (!is_null($Mail)) {
                // テンプレートファイルの取得
                $source = $twig->getLoader()
                    ->getSourceContext($Mail->getFileName())
                    ->getCode();

                $form->get('tpl_data')->setData($source);
                if ($twig->getLoader()->exists($htmlFileName)) {
                    $source = $twig->getLoader()
                        ->getSourceContext($htmlFileName)
                        ->getCode();

                    $form->get('html_tpl_data')->setData($source);
                }
            }
        }

        return [
            'form' => $form->createView(),
            'id' => is_null($Mail) ? null : $Mail->getId(),
            'template_name' => is_null($Mail) ? '' : $Mail->getName(),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/setting/shop/mail/preview", name="admin_setting_shop_mail_preview", methods={"POST"})
     * @Template("@admin/Setting/Shop/mail_view.twig")
     */
    public function preview(Request $request)
    {
        if (!$request->isXmlHttpRequest() && $this->isTokenValid()) {
            throw new BadRequestHttpException();
        }

        $html_body = $request->get('html_body');

        $event = new EventArgs(
            [
                'html_body' => $html_body,
            ],
            $request
        );
        $this->eventDispatcher->dispatch($event, EccubeEvents::ADMIN_SETTING_SHOP_MAIL_PREVIEW_COMPLETE);

        return [
            'html_body' => $html_body,
        ];
    }

    /**
     * HTML用テンプレート名を取得する
     *
     * @param  string $fileName
     *
     * @return string
     */
    protected function getHtmlFileName($fileName)
    {
        // HTMLテンプレートファイルの取得
        $targetTemplate = pathinfo($fileName);
        $suffix = '.html';

        return $targetTemplate['dirname'].DIRECTORY_SEPARATOR.$targetTemplate['filename'].$suffix.'.'.$targetTemplate['extension'];
    }
}
