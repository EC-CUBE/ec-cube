<?php

namespace Eccube\Application;

use Eccube\Event\TemplateEvent;
use Monolog\Logger;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

trait ApplicationTrait
{
    /**
     * Application Shortcut Methods
     */
    public function addSuccess($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.success', $message);
    }

    public function addError($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.error', $message);
    }

    public function addDanger($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.danger', $message);
    }

    public function addWarning($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.warning', $message);
    }

    public function addInfo($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->add('eccube.' . $namespace . '.info', $message);
    }

    public function addRequestError($message, $namespace = 'front')
    {
        $this['session']->getFlashBag()->set('eccube.' . $namespace . '.request.error', $message);
    }

    public function clearMessage()
    {
        $this['session']->getFlashBag()->clear();
    }

    public function deleteMessage()
    {
        $this->clearMessage();
        $this->addWarning('admin.delete.warning', 'admin');
    }

    public function setLoginTargetPath($targetPath, $namespace = null)
    {
        if (is_null($namespace)) {
            $this['session']->getFlashBag()->set('eccube.login.target.path', $targetPath);
        } else {
            $this['session']->getFlashBag()->set('eccube.' . $namespace . '.login.target.path', $targetPath);
        }
    }

    public function isAdminRequest()
    {
        return isset($this['admin']) ? $this['admin'] : null;
    }

    public function isFrontRequest()
    {
        return isset($this['front']) ? $this['front'] : null;
    }

    /**
     * 他のコントローラにリクエストをフォワードします.
     *
     * @param string $path フォワード先のパス
     * @param array $requestParameters
     * @return Response
     */
    public function forward($path, array $requestParameters = [])
    {
        $request = $this['request_stack']->getCurrentRequest();

        $subRequest = Request::create(
            $path,
            $request->getMethod(),
            $requestParameters,
            $request->cookies->all(),
            [],
            $request->server->all()
        );
        if ($request->getSession()) {
            $subRequest->setSession($request->getSession());
        }

        return $this->handle($subRequest, HttpKernelInterface::SUB_REQUEST, false);
    }

    /**
     * フォワードをチェーンでつなげます.
     *
     * @param string $path フォワード先のパス
     * @param array $requestParameters
     * @param Response $response
     * @return Application
     */
    public function forwardChain($path, array $requestParameters = [], Response &$response = null)
    {
        $response = $this->forward($path, $requestParameters);
        return $this;
    }

    /**
     * コンテナに登録済のサービスを上書きする.
     * Pimple標準では再登録を行うと, `RuntimeException: Cannot override frozen service`が投げられるため,一度unsetしてから再登録を行う.
     * config系の変更程度に利用はとどめること
     *
     * @param $key
     * @param $service
     * @throws \InvalidArgumentException keyが存在しない場合.
     */
    public function overwrite($key, $service)
    {
        if (!$this->offsetExists($key)) {
            throw new \InvalidArgumentException();
        }
        $this->offsetUnset($key);
        $this[$key] = $service;
    }

    /**
     * プライマリーキーを使用してオブジェクトを取得する.
     *
     * 主に、マスタデータを取得するために使用する.
     *
     * @param string $class
     * @param integer $id
     * @throws \InvalidArgumentException $class が不正な場合
     * @return object|null エンティティのインスタンス
     */
    public function find($class, $id)
    {
        if (!class_exists($class)) {
            throw new \InvalidArgumentException();
        }
        return $this['orm.em']->getRepository($class)->find($id);
    }
}
