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

namespace Eccube\Entity;


use Doctrine\Common\Annotations\AnnotationReader;
use Eccube\Annotation\EntityExtension;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\FileGenerator;
use Zend\Code\Reflection\ClassReflection;

class ProxyGenerator
{
    /**
     * @param array $scanDirs スキャン対象ディレクトリ
     * @param string $outputDir 出力先
     * @param OutputInterface $output ログ出力
     * @return array 生成したファイルのリスト
     */
    public function generate($scanDirs, $outputDir, OutputInterface $output = null)
    {
        if (is_null($output)) {
            $output = new ProxyGeneratorLoggerOutput();
        }
//        // プロキシのクリア
//        $files = Finder::create()
//            ->in($app['config']['root_dir'].'/app/cache/doctrine/entity-proxies')
//            ->name('*.php')
//            ->files();
//        $fs = new Filesystem();
//        foreach ($files as $file) {
//            $output->writeln('remove -> '.$file->getRealPath());
//            $fs->remove($file->getRealPath());
//        }

        // Acmeからファイルを抽出
        $files = Finder::create()
            ->in(array_filter($scanDirs, 'file_exists'))
            ->name('*.php')
            ->files();

        // traitの一覧を取得
        $traits = [];
        $includedFiles = [];
        foreach ($files as $file) {
            require_once $file->getRealPath();
            $includedFiles[] = $file->getRealPath();
        }

        $declared = get_declared_traits();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (in_array($sourceFile, $includedFiles)) {
                $traits[] = $className;
            }
        }

        // traitから@EntityExtensionを抽出
        $reader = new AnnotationReader();
        $proxies = [];
        foreach ($traits as $trait) {
            $anno = $reader->getClassAnnotation(new \ReflectionClass($trait), EntityExtension::class);
            if ($anno) {
                $proxies[$anno->value][] = $trait;
            }
        }

        $generatedFiles = [];

        // プロキシファイルの生成
        foreach ($proxies as $targetEntity => $traits) {
            $rc = new ClassReflection($targetEntity);
            $generator = ClassGenerator::fromReflection($rc);
            $uses = FileGenerator::fromReflectedFileName($rc->getFileName())->getUses();

            foreach ($uses as $use) {
                $generator->addUse($use[0], $use[1]);
            }

            foreach ($traits as $trait) {
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
                $generator->addTrait('\\'.$trait);
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
}

class ProxyGeneratorLoggerOutput extends Output
{
    /**
     * Writes a message to the output.
     *
     * @param string $message A message to write to the output
     * @param bool $newline Whether to add a newline or not
     */
    protected function doWrite($message, $newline)
    {
        log_info($newline);
    }
}