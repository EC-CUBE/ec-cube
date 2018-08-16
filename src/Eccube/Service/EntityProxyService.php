<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
            $fileName = $rc->getFileName();
            $entityTokens = Tokens::fromCode(file_get_contents($fileName));

            if (strpos(str_replace('\\', '/', $fileName), 'app/proxy/entity') === false) {
                $this->removeClassExistsBlock($entityTokens); // remove class_exists block
            }

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
     * @param Tokens $entityTokens Tokens Entityのトークン
     * @param $trait string 追加するTraitのFQCN
     */
    private function addTrait($entityTokens, $trait)
    {
        $newTraitTokens = $this->convertTraitNameToTokens($trait);

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
     * @param Tokens $entityTokens Tokens Entityのトークン
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
            foreach ($traitNames as $i => $name) {
                if ($name === $trait) {
                    unset($traitNames[$i]);
                }
            }

            // use句をすべて削除
            $entityTokens->clearRange($useTraitIndex, $useTraitEndIndex + 1);

            // traitを追加し直す
            foreach ($traitNames as $t) {
                $this->addTrait($entityTokens, $t);
            }
        }
    }

    /**
     * trait名をトークンに変換する
     *
     * trait名は以下の2形式で引数に渡される
     * - プラグインのTrait -> \Plugin\Xxx\Entity\XxxTrait
     * - 本体でuseされているTrait -> PointTrait
     *
     * @param $name
     *
     * @return array|Token[]
     */
    private function convertTraitNameToTokens($name)
    {
        $result = [];
        $i = 0;
        foreach (explode('\\', $name) as $part) {
            // プラグインのtraitの場合は、0番目は空文字
            // 本体でuseされているtraitは0番目にtrait名がくる
            if ($part) {
                // プラグインのtraitの場合はFQCNにする
                if ($i > 0) {
                    $result[] = new Token([T_NS_SEPARATOR, '\\']);
                }
                $result[] = new Token([T_STRING, $part]);
            }
            $i++;
        }

        return $result;
    }

    /**
     * remove block to 'if (!class_exists(<class name>)) { }'
     *
     * @param Tokens $entityTokens
     */
    private function removeClassExistsBlock(Tokens $entityTokens)
    {
        $startIndex = $entityTokens->getNextTokenOfKind(0, [[T_IF]]);
        $classIndex = $entityTokens->getNextTokenOfKind(0, [[T_CLASS]]);
        if ($startIndex > 0 && $startIndex < $classIndex) { // if statement before class
            $blockStartIndex = $entityTokens->getNextTokenOfKind($startIndex, ['{']);
            $blockEndIndex = $entityTokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $blockStartIndex);

            $entityTokens->clearRange($startIndex, $blockStartIndex);
            $entityTokens->clearRange($blockEndIndex, $blockEndIndex + 1);
        }
    }
}
