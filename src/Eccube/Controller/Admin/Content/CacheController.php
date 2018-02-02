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


namespace Eccube\Controller\Admin\Content;

use Eccube\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class CacheController extends AbstractController
{
    /**
     * @Route("/%eccube_admin_route%/content/cache", name="admin_content_cache")
     * @Template("@admin/Content/cache.twig")
     */
    public function index(Request $request, KernelInterface $kernel)
    {
        $result = '';

        $builder = $this->formFactory->createBuilder(FormType::class);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $result = $this->processCacheClearCommand($kernel);

            $this->addSuccess('admin.content.cache.save.complete', 'admin');
        }

        return [
            'form' => $form->createView(),
            'result' => $result,
        ];
    }

    /**
     * @param KernelInterface $kernel
     * @return mixed|string
     */
    protected function processCacheClearCommand(KernelInterface $kernel)
    {
        $console = new Application($kernel);
        $console->setAutoExit(false);

        $input = new ArrayInput(array(
            'command' => 'cache:clear',
            '--no-warmup' => null,
            '--no-ansi' => null,
        ));

        $output = new BufferedOutput(
            OutputInterface::VERBOSITY_DEBUG,
            true
        );

        $console->run($input, $output);

        return $output->fetch();
    }
}
