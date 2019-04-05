<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

use Eccube\Application;
use Eccube\Common\Constant;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;

class AbstractController
{
    public function __construct()
    {
    }

    /**
     * getBoundForm
     * 
     * @deprecated 
     */
    protected function getBoundForm(Application $app, $type)
    {
        @trigger_error('The '.__METHOD__.' method is deprecated.', E_USER_DEPRECATED);

        $form = $app['form.factory']
            ->createBuilder($app['eccube.form.type.' . $type], $app['eccube.entity.' . $type])
            ->getForm();
        $form->handleRequest($app['request']);

        return $form;
    }

    protected function getSecurity($app)
    {
        return $app['security.token_storage'];
    }

    protected function isTokenValid($app)
    {
        $csrf = $app['form.csrf_provider'];
        $name = Constant::TOKEN_NAME;

        if (!$csrf->isTokenValid(new CsrfToken($name, $app['request']->request->get($name)))) {
            throw new AccessDeniedHttpException('CSRF token is invalid.');
        }

        return true;
    }

}
