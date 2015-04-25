<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use \Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;
use \Symfony\Component\HttpKernel\Exception;

class CssController {

    protected $title = 'コンテンツ管理';

    protected $subtitle = 'CSS管理';

    // todo
    protected $cssDir = '/vagrant/ec-cube/html/user_data/packages/default/css/';

    public function index(Application $app)
    {
        $builder = $app['form.factory']->createBuilder();
        $builder->add('file_name', 'text');
        $builder->add('content', 'textarea');
        $builder->add('save', 'submit', array('label' => '登録'));
        $form = $builder->getForm();

        // 登録実行(新規/編集)
        if ('POST' === $app['request']->getMethod()) {
            $form->handleRequest($app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                $fs = new Filesystem();
                $fs->dumpFile($this->cssDir . $data['file_name'], $data['content']);
                $app['session']->getFlashBag()->add('admin.content.css.complete', 'admin.register.complete');
                return $app->redirect($app['url_generator']->generate('admin_content_css'));
            }
        }

        // 編集初期表示
        $target = $app['request']->get('target');
        if ($target) {
            $finder = Finder::create();
            $finder->in($this->cssDir)->name($target);
            if ($finder->count() === 1) {
                $data = null;
                foreach ($finder as $file) {
                    $data = array(
                        'file_name' => $file->getFileName(),
                        'content' => file_get_contents($file->getPathName()));
                }
                $form->setData($data);
            }
        }

        return $app['twig']->render('Admin/Content/css.twig', array(
            'form' => $form->createView(),
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'filenames' => $this->getFileNames(),
        ));
    }

    public function delete(Application $app)
    {
        $target = $app['request']->get('target');

        $finder = Finder::create();
        $finder->in($this->cssDir)->name($target);

        if ($finder->count() == 1) {
            foreach ($finder->files() as $file) {
                $fs = new Filesystem();
                //$fs->remove($file->getPathName());
            }
            $app['session']->getFlashBag()->add('admin.content.css.complete', 'admin.register.complete');
        }
        return $app->redirect($app['url_generator']->generate('admin_content_css'));
    }

    protected function getFileNames()
    {
        $finder = Finder::create();
        $finder
            ->in($this->cssDir)
            ->files()
            ->sortByName()
            ->depth(0);

        $fileNames = array();
        foreach ($finder as $file) {
            $fileNames[] = $file->getFileName();
        }

        return $fileNames;
    }
}