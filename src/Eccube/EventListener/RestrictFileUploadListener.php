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

namespace Eccube\EventListener;

use Eccube\Common\EccubeConfig;
use Eccube\Request\Context;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class RestrictFileUploadListener implements EventSubscriberInterface
{
    /**
     * 読み取り専用として扱う画面であることを示すリクエスト属性.
     */
    public const READ_ONLY_ATTRIBUTE = 'eccube_read_only';

    /**
     * 読み取り専用のときに案内する CLI コマンドを保持するリクエスト属性.
     */
    public const READ_ONLY_COMMAND_ATTRIBUTE = 'eccube_read_only_command';

    /**
     * 読み取り専用の対象になり得る画面であることを示すリクエスト属性.
     * 制限が無効なときに「無効化できます」の案内を出すかの判定に使う.
     */
    public const RESTRICTABLE_ATTRIBUTE = 'eccube_restrictable';

    /**
     * 画面遷移にも POST を使うため, HTTP メソッドでは書き込みを判別できないルート.
     * これらの書き込み操作はコントローラ側で拒否する (FileController::index).
     *
     * @var string[]
     */
    private const SELF_GUARDED_ROUTES = ['admin_content_file'];

    public function __construct(protected EccubeConfig $eccubeConfig, protected Context $requestContext)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if (!$this->requestContext->isAdmin()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        if (!is_string($route)) {
            return;
        }

        /** @var array<string, string|null> $restrictUrls */
        $restrictUrls = $this->eccubeConfig['eccube_restrict_file_upload_urls'];
        if (!array_key_exists($route, $restrictUrls)) {
            return;
        }

        // 制限が無効でも, 対象になり得る画面であることは伝える
        $request->attributes->set(self::RESTRICTABLE_ATTRIBUTE, true);

        if ($this->eccubeConfig['eccube_restrict_file_upload'] !== '1') {
            return;
        }

        $request->attributes->set(self::READ_ONLY_ATTRIBUTE, true);
        $request->attributes->set(self::READ_ONLY_COMMAND_ATTRIBUTE, $restrictUrls[$route]);

        // 安全なメソッドは通す. 現在の内容を表示できないと, CLI へ渡す元データが分からない
        if ($request->isMethodSafe()) {
            return;
        }

        // 画面遷移にも POST を使うルートは, 書き込みかどうかをコントローラ側で判定する
        if (in_array($route, self::SELF_GUARDED_ROUTES, true)) {
            return;
        }

        throw new AccessDeniedHttpException(trans('exception.error_message_restrict_url'));
    }

    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'kernel.request' => ['onKernelRequest', 7], // RouterListener より必ず後で実行する
        ];
    }
}
