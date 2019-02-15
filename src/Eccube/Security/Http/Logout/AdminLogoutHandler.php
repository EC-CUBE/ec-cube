<?php

namespace Eccube\Security\Http\Logout;

use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AdminLogoutHandler implements LogoutHandlerInterface {

    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function logout(Request $request, Response $response, TokenInterface $token)
    {
        if ($this->session->has("_security_admin")) {
            $this->session->remove("_security_admin");
        }
    }

}
