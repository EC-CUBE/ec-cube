<?php

namespace Eccube\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LogoutHandler implements LogoutHandlerInterface{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if($this->session->has('cart_keys')) {
            $this->session->remove('cart_keys');
        }
    }

}
