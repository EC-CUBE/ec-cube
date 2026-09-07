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

namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Service\Content\AssetContentService;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class JsController extends AbstractController
{
    public function __construct(private readonly AssetContentService $assetContentService)
    {
    }

    /**
     * @return RedirectResponse|array<string, FormView>
     */
    #[Route(path: '/%eccube_admin_route%/content/js', name: 'admin_content_js', methods: ['GET', 'POST'])]
    #[Template(template: '@admin/Content/js.twig')]
    public function index(Request $request): RedirectResponse|array
    {
        $this->addInfoOnce('admin.common.restrict_file_upload_info', 'admin');

        $builder = $this->formFactory
            ->createBuilder(FormType::class)
            ->add('js', TextareaType::class, [
                'required' => false,
            ]);
        $form = $builder->getForm();

        // 読み込みは書き込み権限と切り離す. 権限を分離した構成 (html/user_data がレーン S) では
        // 書き込めないだけで, 現在の内容は表示できる必要がある.
        try {
            $form->get('js')->setData($this->assetContentService->read('js'));
        } catch (ContentValidationException $e) {
            $this->addWarning(implode(' ', $e->getErrors()), 'admin');
            log_error('failed to read the customize file.', $e->getErrors());
        }

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->assetContentService->apply('js', (string) $form->get('js')->getData());
                $this->addSuccess('admin.common.save_complete', 'admin');

                return $this->redirectToRoute('admin_content_js');
            } catch (ContentWriteException $e) {
                $message = trans('admin.common.save_error');
                $this->addError($message, 'admin');
                log_error($message, [$e->getPath(), $e]);
            } catch (ContentValidationException $e) {
                // 配置先の拡張子が eccube_file_uploadable_extensions から外されている場合など.
                // 500 にせずエラーとして表示する
                $this->addError(trans('admin.common.save_error'), 'admin');
                $this->addError(implode(' ', $e->getErrors()), 'admin');
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
