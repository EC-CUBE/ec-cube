<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Controller\Admin\Store;

use Eccube\Controller\AbstractController;
use Eccube\Entity\Master\DeviceType;
use Eccube\Form\Type\Admin\TemplateType;
use Eccube\Repository\Master\DeviceTypeRepository;
use Eccube\Repository\TemplateRepository;
use Eccube\Util\CacheUtil;
use Eccube\Util\StringUtil;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    /**
     * @var TemplateRepository
     */
    protected $templateRepository;

    /**
     * @var DeviceTypeRepository
     */
    protected $deviceTypeRepository;

    /**
     * TemplateController constructor.
     *
     * @param TemplateRepository $templateRepository
     * @param DeviceTypeRepository $deviceTypeRepository
     */
    public function __construct(
        TemplateRepository $templateRepository,
        DeviceTypeRepository $deviceTypeRepository
    ) {
        $this->templateRepository = $templateRepository;
        $this->deviceTypeRepository = $deviceTypeRepository;
    }

    /**
     * テンプレート一覧画面
     *
     * @Route("/%eccube_admin_route%/store/template", name="admin_store_template")
     * @Template("@admin/Store/template.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request, CacheUtil $cacheUtil)
    {
        $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

        $Templates = $this->templateRepository->findBy(['DeviceType' => $DeviceType]);

        $form = $this->formFactory->createBuilder()
            ->add('selected', HiddenType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $Template = $this->templateRepository->find($form['selected']->getData());

            $envFile = $this->getParameter('kernel.project_dir').'/.env';
            $env = file_exists($envFile) ? file_get_contents($envFile) : '';

            $env = StringUtil::replaceOrAddEnv($env, [
                'ECCUBE_TEMPLATE_CODE' => $Template->getCode(),
            ]);

            file_put_contents($envFile, $env);

            $this->addSuccess('admin.content.template.save.complete', 'admin');

            $cacheUtil->clearCache();

            return $this->redirectToRoute('admin_store_template');
        }

        return [
            'form' => $form->createView(),
            'Templates' => $Templates,
        ];
    }

    /**
     * テンプレート一覧からのダウンロード
     *
     * @Route("/%eccube_admin_route%/store/template/{id}/download", name="admin_store_template_download", requirements={"id" = "\d+"})
     *
     * @param Request $request
     * @param \Eccube\Entity\Template $Template
     *
     * @return BinaryFileResponse
     */
    public function download(Request $request, \Eccube\Entity\Template $Template)
    {
        // 該当テンプレートのディレクトリ
        $templateCode = $Template->getCode();
        $targetRealDir = $this->getParameter('kernel.project_dir').'/app/template/'.$templateCode;
        $targetHtmlRealDir = $this->getParameter('kernel.project_dir').'/html/template/'.$templateCode;

        // 一時ディレクトリ
        $uniqId = sha1(StringUtil::random(32));
        $tmpDir = \sys_get_temp_dir().'/'.$uniqId;
        $appDir = $tmpDir.'/app';
        $htmlDir = $tmpDir.'/html';

        // ファイル名
        $tarFile = $tmpDir.'.tar';
        $tarGzFile = $tarFile.'.gz';
        $downloadFileName = $Template->getCode().'.tar.gz';

        // 該当テンプレートを一時ディレクトリへコピーする.
        $fs = new Filesystem();
        $fs->mkdir([$appDir, $htmlDir]);
        $fs->mirror($targetRealDir, $appDir);
        $fs->mirror($targetHtmlRealDir, $htmlDir);

        // tar.gzファイルに圧縮する.
        $phar = new \PharData($tarFile);
        $phar->buildFromDirectory($tmpDir);
        // appディレクトリがない場合は, 空ディレクトリを追加
        // @see https://github.com/EC-CUBE/ec-cube/issues/742
        if (empty($phar['app'])) {
            $phar->addEmptyDir('app');
        }
        $phar->compress(\Phar::GZ);

        // ダウンロード完了後にファイルを削除する.
        // http://stackoverflow.com/questions/15238897/removing-file-after-delivering-response-with-silex-symfony
        $this->eventDispatcher->addListener(KernelEvents::TERMINATE, function () use (
            $tmpDir,
            $tarFile,
            $tarGzFile
        ) {
            log_debug('remove temp file: '.$tmpDir);
            log_debug('remove temp file: '.$tarFile);
            log_debug('remove temp file: '.$tarGzFile);
            $fs = new Filesystem();
            $fs->remove($tmpDir);
            $fs->remove($tarFile);
            $fs->remove($tarGzFile);
        });

        $response = new BinaryFileResponse($tarGzFile);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $downloadFileName);

        return $response;
    }

    /**
     * @Route("/%eccube_admin_route%/store/template/{id}/delete", name="admin_store_template_delete", requirements={"id" = "\d+"}, methods={"DELETE"})
     */
    public function delete(Request $request, \Eccube\Entity\Template $Template)
    {
        $this->isTokenValid();

        // デフォルトテンプレート
        if ($Template->isDefaultTemplate()) {
            $this->addError('admin.content.template.delete.default.error', 'admin');

            return $this->redirectToRoute('admin_store_template');
        }

        // 設定中のテンプレート
        if ($this->eccubeConfig['eccube.theme'] === $Template->getCode()) {
            $this->addError('admin.content.template.delete.current.error', 'admin');

            return $this->redirectToRoute('admin_store_template');
        }

        // テンプレートディレクトリの削除
        $templateCode = $Template->getCode();
        $targetRealDir = $this->container->getParameter('kernel.project_dir').'/app/template/'.$templateCode;
        $targetHtmlRealDir = $this->container->getParameter('kernel.project_dir').'/html/template/'.$templateCode;

        $fs = new Filesystem();
        $fs->remove($targetRealDir);
        $fs->remove($targetHtmlRealDir);

        // テーブルからも削除
        $this->entityManager->remove($Template);
        $this->entityManager->flush();

        $this->addSuccess('admin.content.template.delete.complete', 'admin');

        return $this->redirectToRoute('admin_store_template');
    }

    /**
     * テンプレートの追加画面.
     *
     * @Route("/%eccube_admin_route%/store/template/install", name="admin_store_template_install")
     * @Template("@admin/Store/template_add.twig")
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function install(Request $request)
    {
        $form = $this->formFactory
            ->createBuilder(TemplateType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var $Template \Eccube\Entity\Template */
            $Template = $form->getData();

            $TemplateExists = $this->templateRepository->findByCode($Template->getCode());

            // テンプレートコードの重複チェック.
            if ($TemplateExists) {
                $form['code']->addError(new FormError(trans('template.text.error.code_not_available')));

                return [
                    'form' => $form->createView(),
                ];
            }

            // 該当テンプレートのディレクトリ
            $templateCode = $Template->getCode();
            $targetRealDir = $this->getParameter('kernel.project_dir').'/app/template/'.$templateCode;
            $targetHtmlRealDir = $this->getParameter('kernel.project_dir').'/html/template/'.$templateCode;

            // 一時ディレクトリ
            $uniqId = sha1(StringUtil::random(32));
            $tmpDir = \sys_get_temp_dir().'/'.$uniqId;
            $appDir = $tmpDir.'/app';
            $htmlDir = $tmpDir.'/html';

            $formFile = $form['file']->getData();
            // ファイル名
            $archive = $templateCode.'.'.$formFile->getClientOriginalExtension();

            // ファイルを一時ディレクトリへ移動.
            $formFile->move($tmpDir, $archive);

            // 一時ディレクトリへ解凍する.
            try {
                if ($formFile->getClientOriginalExtension() === 'zip') {
                    $zip = new \ZipArchive();
                    $zip->open($tmpDir.'/'.$archive);
                    $zip->extractTo($tmpDir);
                    $zip->close();
                } else {
                    $phar = new \PharData($tmpDir.'/'.$archive);
                    $phar->extractTo($tmpDir, null, true);
                }
            } catch (\Exception $e) {
                $form['file']->addError(new FormError(trans('template.text.error.upload_failuer')));

                return [
                    'form' => $form->createView(),
                ];
            }

            $fs = new Filesystem();

            // appディレクトリの存在チェック.
            if (!file_exists($appDir)) {
                $fs->mkdir($appDir);
            }

            // htmlディレクトリの存在チェック.
            if (!file_exists($htmlDir)) {
                $fs->mkdir($htmlDir);
            }

            // 一時ディレクトリから該当テンプレートのディレクトリへコピーする.
            $fs->mirror($appDir, $targetRealDir);
            $fs->mirror($htmlDir, $targetHtmlRealDir);

            // 一時ディレクトリを削除.
            $fs->remove($tmpDir);

            $DeviceType = $this->deviceTypeRepository->find(DeviceType::DEVICE_TYPE_PC);

            $Template->setDeviceType($DeviceType);

            $this->entityManager->persist($Template);
            $this->entityManager->flush();

            $this->addSuccess('admin.content.template.add.complete', 'admin');

            return $this->redirectToRoute('admin_store_template');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
