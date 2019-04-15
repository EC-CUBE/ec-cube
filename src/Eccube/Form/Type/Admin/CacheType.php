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


namespace Eccube\Form\Type\Admin;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class CacheType extends AbstractType
{

    private $config;

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // 対象となるキャッシュディレクトリを取得
        // eccubeディレクトリは削除対象外とする
        $finder = Finder::create()->notName('eccube')->depth(0);
        $cacheDir = $this->config['root_dir'].'/app/cache';

        $cacheDirs = array();
        foreach ($finder->in($cacheDir) as $file) {
            $cacheDirs[$file->getFilename()] = $file->getFilename();
        }

        $builder
            ->add('cache', 'choice', array(
                'label' => 'キャッシュディレクトリ',
                'choices' => $cacheDirs,
                'expanded' => true,
                'multiple' => true,
                'required' => true,
                // デフォルトでtwigはチェックをいれる
                'data' => array('twig'),
                'constraints' => array(
                    new Assert\NotBlank(),
                ),
            ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'admin_cache';
    }
}
