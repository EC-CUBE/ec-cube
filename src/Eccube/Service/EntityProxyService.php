<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Service;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManager;
use Eccube\Annotation\EntityExtension;
use Eccube\Annotation\Inject;
use Eccube\Annotation\Service;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Reflection\ClassReflection;

/**
 * @Service
 */
class EntityProxyService
{
    /**
     * @Inject("orm.em")
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * EntityのProxyを生成します。
     *
     * @param array $includesDirs Proxyに含めるTraitがあるディレクトリ一覧
     * @param array $excludeDirs Proxyから除外するTraitがあるディレクトリ一覧
     * @param string $outputDir 出力先
     * @param OutputInterface $output ログ出力
     * @return array 生成したファイルのリスト
     */
    public function generate($includesDirs, $excludeDirs, $outputDir, OutputInterface $output = null)
    {
        if (is_null($output)) {
            $output = new ConsoleOutput();
        }

        $generatedFiles = [];

        list($addTraits, $removeTrails) = $this->scanTraits([$includesDirs, $excludeDirs]);
        $targetEntities = array_unique(array_merge(array_keys($addTraits), array_keys($removeTrails)));

        // プロキシファイルの生成
        foreach ($targetEntities as $targetEntity) {
            $traits = isset($addTraits[$targetEntity]) ? $addTraits[$targetEntity] : [];
            $rc = new ClassReflection($targetEntity);
            $generator = ClassGenerator::fromReflection($rc);
            $uses = FileGenerator::fromReflectedFileName($rc->getFileName())->getUses();

            foreach ($uses as $use) {
                $generator->addUse($use[0], $use[1]);
            }

            if (isset($removeTrails[$targetEntity])) {
                foreach ($removeTrails[$targetEntity] as $trait) {
                    $this->removePropertiesFromProxy($trait, $generator);
                }
            }

            foreach ($traits as $trait) {
                $this->removePropertiesFromProxy($trait, $generator);
                $generator->addTrait('\\' . $trait);
            }

            // extendしたクラスが相対パスになるので
            $extendClass = $generator->getExtendedClass();
            $generator->setExtendedClass('\\'.$extendClass);

            // interfaceが相対パスになるので
            $interfaces = $generator->getImplementedInterfaces();
            foreach ($interfaces as &$interface) {
                $interface = '\\'.$interface;
            }
            $generator->setImplementedInterfaces($interfaces);

            $file = basename($rc->getFileName());

            $code = $generator->generate();
            $generatedFiles[] = $outputFile = $outputDir.'/'.$file;
            file_put_contents($outputFile, '<?php '.PHP_EOL.$code);
            $output->writeln('gen -> '.$outputFile);
        }

        return $generatedFiles;
    }

    /**
     * 複数のディレクトリセットをスキャンしてディレクトリセットごとのEntityとTraitのマッピングを返します.
     * @param $dirSets array スキャン対象ディレクトリリストの配列
     * @return array ディレクトリセットごとのEntityとTraitのマッピング
     */
    private function scanTraits($dirSets)
    {
        // ディレクトリセットごとのファイルをロードしつつ一覧を作成
        $includedFileSets = [];
        foreach ($dirSets as $dirSet) {
            $includedFiles = [];
            $dirs = array_filter($dirSet, 'file_exists');
            if (!empty($dirs)) {
                $files = Finder::create()
                    ->in($dirs)
                    ->name('*.php')
                    ->files();

                foreach ($files as $file) {
                    require_once $file->getRealPath();
                    $includedFiles[] = $file->getRealPath();
                }
            }
            $includedFileSets[] = $includedFiles;
        }

        $declaredTraits = get_declared_traits();

        // ディレクトリセットに含まれるTraitの一覧を作成
        $traitSets = array_map(function() { return []; }, $dirSets);
        foreach ($declaredTraits as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            foreach ($includedFileSets as $index=>$includedFiles) {
                if (in_array($sourceFile, $includedFiles)) {
                    $traitSets[$index][] = $className;
                }
            }
        }

        // TraitをEntityごとにまとめる
        $reader = new AnnotationReader();
        $proxySets = [];
        foreach ($traitSets as $traits) {
            $proxies = [];
            foreach ($traits as $trait) {
                $anno = $reader->getClassAnnotation(new \ReflectionClass($trait), EntityExtension::class);
                if ($anno) {
                    $proxies[$anno->value][] = $trait;
                }
            }
            $proxySets[] = $proxies;
        }

        return $proxySets;
    }

    /**
     * @param $trait
     * @param $generator
     */
    private function removePropertiesFromProxy($trait, $generator)
    {
        $rt = new ClassReflection($trait);
        foreach ($rt->getProperties() as $prop) {
            // すでにProxyがある場合, $generatorにuse XxxTrait;が存在せず,
            // traitに定義されているフィールド,メソッドがクラス側に追加されてしまう
            if ($generator->hasProperty($prop->getName())) {
                // $generator->removeProperty()はzend-code 2.6.3 では未実装なのでリフレクションで削除.
                $generatorRefObj = new \ReflectionObject($generator);
                $generatorRefProp = $generatorRefObj->getProperty('properties');
                $generatorRefProp->setAccessible(true);
                $properies = $generatorRefProp->getValue($generator);
                unset($properies[$prop->getName()]);
                $generatorRefProp->setValue($generator, $properies);
            }
        }
        foreach ($rt->getMethods() as $method) {
            if ($generator->hasMethod($method->getName())) {
                $generator->removeMethod($method->getName());
            }
        }
    }
}
