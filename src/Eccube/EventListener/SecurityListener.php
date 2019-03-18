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

namespace Eccube\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\Member;
use Eccube\Service\CartService;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Service\OrderHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class SecurityListener implements EventSubscriberInterface
{
    protected $em;

    protected $cartService;

    protected $purchaseFlow;

    protected $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        CartService $cartService,
        PurchaseFlow $cartPurchaseFlow,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->cartService = $cartService;
        $this->purchaseFlow = $cartPurchaseFlow;
        $this->requestStack = $requestStack;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onInteractiveLogin(InteractiveLoginEvent $event)
    {
        $user = $event
            ->getAuthenticationToken()
            ->getUser();

        if ($user instanceof Member) {
            $user->setLoginDate(new \DateTime());
            $this->em->persist($user);
            $this->em->flush();
        } elseif ($user instanceof Customer) {
            $this->cartService->mergeFromPersistedCart();
            foreach ($this->cartService->getCarts() as $Cart) {
                $this->purchaseFlow->validate($Cart, new PurchaseContext($Cart, $user));
            }
            $this->cartService->save();
            if (count($this->cartService->getCarts()) > 1) {
                // カートが分割されていればメッセージを表示
                $event->getRequest()->getSession()->set(OrderHelper::SESSION_CART_DIVIDE_FLAG, true);
            }
        }
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailure(AuthenticationFailureEvent $event)
    {
        $request = $this->requestStack->getCurrentRequest();
        $request->getSession()->set('_security.login_memory', (bool) $request->request->get('login_memory', 0));
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     * * The method name to call (priority defaults to 0)
     * * An array composed of the method name to call and the priority
     * * An array of arrays composed of the method names to call and respective
     *   priorities, or 0 if unset
     *
     * For instance:
     *
     * * array('eventName' => 'methodName')
     * * array('eventName' => array('methodName', $priority))
     * * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }
}
