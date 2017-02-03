<?php

namespace Eccube\Application;

use Eccube\Event\TemplateEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Twig trait.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kentaro Ohkouchi
 */
trait TwigTrait
{

    /**
     * Renders a view and returns a Response.
     *
     * To stream a view, pass an instance of StreamedResponse as a third argument.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     * @param Response $response A Response instance
     *
     * @return Response A Response instance
     */
    public function render($view, array $parameters = array(), Response $response = null)
    {
        $twig = $this['twig'];

        // twigファイルのソースコードを読み込み, 文字列化.
        $source = $twig->getLoader()->getSourceContext($view)->getCode();

        // イベントの実行.
        // プラグインにはテンプレートファイル名、文字列化されたtwigファイル、パラメータを渡す
        $event = new TemplateEvent($view, $source, $parameters, $response);

        $eventName = $view;
        if ($this->isAdminRequest()) {
            // 管理画面の場合、event名に「Admin/」を付ける
            $eventName = 'Admin/' . $view;
        }
        $this['monolog']->debug('Template Event Name : ' . $eventName);

        // $this['eccube.event.dispatcher']->dispatch($eventName, $event);

        if ($response instanceof StreamedResponse) {
            $response->setCallback(function () use ($twig, $view, $parameters) {
                $twig->display($view, $parameters);
            });
        } else {
            if (null === $response) {
                $response = new Response();
            }

            // プラグインで変更された文字列から, テンプレートオブジェクトを生成
            $template = $twig->createTemplate($event->getSource());

            // レンダリング実行.
            $content = $template->render($event->getParameters());
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Renders a view.
     *
     * @param string $view The view name
     * @param array $parameters An array of parameters to pass to the view
     *
     * @return string The rendered view
     */
    public function renderView($view, array $parameters = array())
    {
        return $this['twig']->render($view, $parameters);
    }
}
