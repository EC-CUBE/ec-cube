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
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Annotation\EntityExtension;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Zend\Code\Reflection\ClassReflection;

class EntityProxyService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * EntityProxyService constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * EntityのProxyを生成します。
     *
     * @param array $includesDirs Proxyに含めるTraitがあるディレクトリ一覧
     * @param array $excludeDirs Proxyから除外するTraitがあるディレクトリ一覧
     * @param string $outputDir 出力先
     * @param OutputInterface $output ログ出力
     *
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

            $entityTokens = Tokens::fromCode(file_get_contents($rc->getFileName()));

            if (isset($removeTrails[$targetEntity])) {
                foreach ($removeTrails[$targetEntity] as $trait) {
                    $this->removeTrait($entityTokens, $trait);
                }
            }

            foreach ($traits as $trait) {
                $this->addTrait($entityTokens, $trait);
            }

            $file = basename($rc->getFileName());

            $code = $entityTokens->generateCode();
            $generatedFiles[] = $outputFile = $outputDir.'/'.$file;
            file_put_contents($outputFile, $code);
            $output->writeln('gen -> '.$outputFile);
        }

        return $generatedFiles;
    }

    /**
     * 複数のディレクトリセットをスキャンしてディレクトリセットごとのEntityとTraitのマッピングを返します.
     *
     * @param $dirSets array スキャン対象ディレクトリリストの配列
     *
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

        $declaredTraits = array_map(function ($fqcn) {
            // FQCNが'\'で始まるように正規化
            return strpos($fqcn, '\\') === 0 ? $fqcn : '\\'.$fqcn;
        }, get_declared_traits());

        // ディレクトリセットに含まれるTraitの一覧を作成
        $traitSets = array_map(function () { return []; }, $dirSets);
        foreach ($declaredTraits as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            foreach ($includedFileSets as $index => $includedFiles) {
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
     * EntityにTraitを追加.
     *
     * @param $entityTokens Tokens Entityのトークン
     * @param $trait string 追加するTraitのFQCN
     */
    private function addTrait($entityTokens, $trait)
    {
        $newTraitTokens = $this->convertFQCNToTokens($trait);

        // Traitのuse句があるかどうか
        $useTraitIndex = $entityTokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);

        if ($useTraitIndex > 0) {
            $useTraitEndIndex = $entityTokens->getNextTokenOfKind($useTraitIndex, [';']);
            $alreadyUseTrait = $entityTokens->findSequence($newTraitTokens, $useTraitIndex, $useTraitEndIndex);
            if (is_null($alreadyUseTrait)) {
                $entityTokens->insertAt($useTraitEndIndex, array_merge(
                    [new Token(','), new Token([T_WHITESPACE, ' '])],
                    $newTraitTokens
                ));
            }
        } else {
            $useTraitTokens = array_merge(
                [
                    new Token([T_WHITESPACE, PHP_EOL.'    ']),
                    new Token([CT::T_USE_TRAIT, 'use']),
                    new Token([T_WHITESPACE, ' ']),
                ],
                $newTraitTokens,
                [new Token(';'), new Token([T_WHITESPACE, PHP_EOL])]);

            // `class X extens AbstractEntity {`の後にtraitを追加
            $classTokens = $entityTokens->findSequence([[T_CLASS], [T_STRING]]);
            $classTokenEnd = $entityTokens->getNextTokenOfKind(array_keys($classTokens)[0], ['{']);
            $entityTokens->insertAt($classTokenEnd + 1, $useTraitTokens);
        }
    }

    /**
     * EntityからTraitを削除.
     *
     * @param $entityTokens Tokens Entityのトークン
     * @param $trait string 削除するTraitのFQCN
     */
    private function removeTrait($entityTokens, $trait)
    {
        $useTraitIndex = $entityTokens->getNextTokenOfKind(0, [[CT::T_USE_TRAIT]]);
        if ($useTraitIndex > 0) {
            $useTraitEndIndex = $entityTokens->getNextTokenOfKind($useTraitIndex, [';']);
            $traitsTokens = array_slice($entityTokens->toArray(), $useTraitIndex + 1, $useTraitEndIndex - $useTraitIndex - 1);

            // Trait名の配列に変換
            $traitNames = explode(',', implode(array_map(function ($token) {
                return $token->getContent();
            }, array_filter($traitsTokens, function ($token) {
                return $token->getId() != T_WHITESPACE;
            }))));

            // 削除対象を取り除く
            array_splice($traitNames, array_search($trait, $traitNames), 1);

            // use句をすべて削除
            $entityTokens->clearRange($useTraitIndex, $useTraitEndIndex + 1);

            // traitを追加し直す
            foreach ($traitNames as $t) {
                $this->addTrait($entityTokens, $t);
            }
        }
    }

    private function convertFQCNToTokens($fqcn)
    {
        $result = [];
        foreach (explode('\\', $fqcn) as $part) {
            if ($part) {
                $result[] = new Token([T_NS_SEPARATOR, '\\']);
                $result[] = new Token([T_STRING, $part]);
            }
        }

        return $result;
    }
}
