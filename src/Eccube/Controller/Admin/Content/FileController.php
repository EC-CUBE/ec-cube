<?php

namespace Eccube\Controller\Admin\Content;

use Eccube\Application;
use Symfony\Component\Finder\Finder;

class FileController
{
    public function index(Application $app)
    {
        $mainTitle = 'コンテンツ管理';
        $subTitle = 'ファイル管理';

        $topDir = $app['request']->server->get('DOCUMENT_ROOT') . $app['config']['root'] . 'user_data';
        $defaultRank = count(explode('/', $topDir));
        
        $finder = Finder::create()->in($topDir)
            ->directories()
            ->sortByName();
        $dirs = iterator_to_array($finder);

        $tree[] = array(
            'path' => $topDir,
            'parent' => '',
            'type' => '_parent',
            'rank' => 0,
            'open' => true,
        );

        foreach ($finder as $dirs ) {
            $path = $dirs->getRealPath();
            $parent = substr($path, 0, strrpos($path, '/'));
            $type = (iterator_count(Finder::create()->in($path)->directories())) ? '_parent' : '_child';
            $rank = count(explode('/',$path)) - $defaultRank;

            $tree[] = array(
                'path' => $path,
                'parent' => $parent,
                'type' => $type,
                'rank' => $rank,
                'open' => false,
            );
        }

        $now_dir = $app['request']->get('now_dir');

        $javascript = "arrTree = new Array();\n";
        foreach ($tree as $key => $val) {
            $javascript .= 'arrTree[' . $key . "] = new Array(" . $key . ", '" . $val['type'] . "', '" . $val['path'] . "', " . $val['rank'] . ',';
            if ($val['open']) {
                $javascript .= "true);\n";
            } else {
                $javascript .= "false);\n";
            }
        }
        $onload = "eccube.fileManager.viewFileTree('tree', arrTree, '$now_dir', 'tree_select_file', 'tree_status', 'move');";


        return $app['twig']->render('Admin/Content/index.twig', array(
            'tpl_maintitle' => $mainTitle,
            'tpl_subtitle' => $subTitle,
            'tpl_onload' => $onload,
            'tpl_javascript' => $javascript,
            'top_dir' => $topDir,
        ));
    }

}