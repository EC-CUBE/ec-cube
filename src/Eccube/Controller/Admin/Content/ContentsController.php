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

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated 3.1 delete. use NewsController
 */
class ContentsController extends NewsController
{
    /**
     * @deprecated 3.1 delete. use NewsController
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * (non-PHPdoc)
     * @see \Eccube\Controller\Admin\Content\NewsController::index()
     * @deprecated 3.1 delete. use NewsController
     * @param Application $app
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Application $app, Request $request = null)
    {
        return parent::index($app, $request);
    }

    /**
     * (non-PHPdoc)
     * @see \Eccube\Controller\Admin\Content\NewsController::edit()
     * @deprecated 3.1 delete. use NewsController
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function edit(Application $app, Request $request, $id = null)
    {
        return parent::edit($app, $request, $id);
    }

    /**
     * (non-PHPdoc)
     * @see \Eccube\Controller\Admin\Content\NewsController::up()
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function up(Application $app, Request $request, $id)
    {
        return parent::up($app, $request, $id);
    }

    /**
     * (non-PHPdoc)
     * @see \Eccube\Controller\Admin\Content\NewsController::down()
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function down(Application $app, Request $request, $id)
    {
        return parent::down($app, $request, $id);
    }

    /**
     * (non-PHPdoc)
     * @see \Eccube\Controller\Admin\Content\NewsController::delete()
     * @param Application $app
     * @param Request $request
     * @param integer $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function delete(Application $app, Request $request, $id)
    {
        return parent::delete($app, $request, $id);
    }
}
