<?php


namespace Eccube\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Trikoder\Bundle\OAuth2Bundle\Event\AuthorizationRequestResolveEvent;
use Trikoder\Bundle\OAuth2Bundle\OAuth2Events;

final class AuthorizationRequestResolveListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            OAuth2Events::AUTHORIZATION_REQUEST_RESOLVE => 'onAuthorizationRequestResolve',
        ];
    }

    public function onAuthorizationRequestResolve(AuthorizationRequestResolveEvent $event): void
    {
        $user = $event->getUser();

        if (null === $user) {
            return;
        }

        $event->resolveAuthorization(AuthorizationRequestResolveEvent::AUTHORIZATION_APPROVED);
    }
}
