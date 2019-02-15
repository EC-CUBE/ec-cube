<?php

namespace Eccube\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CustomerLogoutHandler implements LogoutHandlerInterface{

    private $session;
    
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    
    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if($sessions = $this->session->all()) {
            unset($sessions["_security_admin"]);
            
            foreach ($sessions as $key => $value) {
                if($this->session->has($key)) {
                    $this->session->remove($key);
                }
            }
        }
    }

}
