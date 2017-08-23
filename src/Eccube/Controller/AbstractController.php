<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */


namespace Eccube\Controller;

use Eccube\Annotation\Inject;
use Eccube\Common\Constant;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManager;

class AbstractController
{
    /**
     * @Inject("csrf.token_manager")
     * @var CsrfTokenManager
     */
    protected $csrfTokenManager;

    /**
     * @Inject("security.token_storage")
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * @Inject("request_stack")
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @Inject("form.factory")
     * @var FormFactory
     */
    protected $formFactory;

    protected function getSecurity($app)
    {
        return $this->tokenStorage;
    }

    protected function isTokenValid($app)
    {
        $csrf = $this->csrfTokenManager;
        $name = Constant::TOKEN_NAME;
        if (!$csrf->isTokenValid(new CsrfToken($name, $this->requestStack->getCurrentRequest()->get($name)))) {
            throw new AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }
}
