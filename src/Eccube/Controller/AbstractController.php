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

class AbstractController
{
    /**
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * set application.
     *
     * @param \Eccube\Application $app
     */
    public function setApp(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * shortcut method "$app->redirect($app['url_generator']->generate(...))"
     *
     * @param $name
     * @param array $parameters
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function redirect($name, $parameters = array())
    {
        return $this->app->redirect($this->url($name, $parameters));
    }

    /**
     * shortcut method "$app['url_generator']->generate(...)"
     *
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    protected function url($name, $parameters = array())
    {
        return $this->app['url_generator']->generate($name, $parameters);
    }

    /**
     * shortcut method "$app['view']->render(...)"
     *
     * @param $name
     * @param array $parameters
     * @return mixed
     */
    protected function render($name, $parameters = array())
    {
        return $this->app['view']->render($name, $parameters);
    }
}
