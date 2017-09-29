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

namespace Eccube\Doctrine\ORM\Mapping\Driver;



use Doctrine\ORM\Mapping\MappingException;
use Eccube\Util\Str;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * 同じプロセス内で新しく生成されたProxyクラスからマッピングメタデータを抽出するためのAnnotationDriver.
 *
 * 同じプロセス内で、Proxy元のEntityがロードされた後に同じFQCNを持つProxyをロードしようとすると、Fatalエラーが発生する.
 * このエラーを回避するために、新しく生成されたProxyクラスは一時的にクラス名を変更してからロードして、マッピングメタデータを抽出する.
 */
class ReloadSafeAnnotationDriver extends AnnotationDriver
{
    /**
     * @var array 新しく生成されたProxyファイルのリスト
     */
    protected $newProxyFiles;

    protected $outputDir;

    public function setNewProxyFiles($newProxyFiles)
    {
        $this->newProxyFiles = $newProxyFiles;
    }

    /**
     * @param string $outputDir
     */
    public function setOutputDir($outputDir)
    {
        $this->outputDir = $outputDir;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames()
    {
        if ($this->classNames !== null) {
            return $this->classNames;
        }

        if (!$this->paths) {
            throw MappingException::pathRequired();
        }

        foreach ($this->paths as $path) {
            if ( ! is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+' . preg_quote($this->fileExtension) . '$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if ( ! preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                foreach ($this->excludePaths as $excludePath) {
                    $exclude = str_replace('\\', '/', realpath($excludePath));
                    $current = str_replace('\\', '/', $sourceFile);

                    if (strpos($current, $exclude) !== false) {
                        continue 2;
                    }
                }

                $proxyFile = str_replace($path, $this->trait_proxies_directory, $sourceFile);
                if (file_exists($proxyFile)) {
                    $sourceFile = $proxyFile;
                }

                $this->classNames = array_merge($this->classNames ?: [], $this->getClassNamesFromTokens($sourceFile));
            }
        }

        return $this->classNames;
    }

    /**
     * ソースコードを字句解析してクラス名を解決します.
     * 新しく生成されたProxyクラスの場合は、一時的にクラス名を変更したクラスを生成してロードします.
     *
     * @param $sourceFile string ソースファイル
     * @return array ソースファイルに含まれるクラス名のリスト
     */
    private function getClassNamesFromTokens($sourceFile)
    {
        $tokens = Tokens::fromCode(file_get_contents($sourceFile));
        $results = [];
        $currentIndex = 0;
        while ($currentIndex = $tokens->getNextTokenOfKind($currentIndex, [[T_CLASS]])) {
            if ($currentIndex >= 0) {
                $classNameTokenIndex = $tokens->getNextMeaningfulToken($currentIndex);
                if ($classNameTokenIndex) {
                    $namespaceIndex = $tokens->getNextTokenOfKind(0, [[T_NAMESPACE]]);
                    if ($namespaceIndex) {
                        $namespaceEndIndex = $tokens->getNextTokenOfKind($namespaceIndex, [';']);
                        $namespace = $tokens->generatePartialCode($tokens->getNextMeaningfulToken($namespaceIndex), $tokens->getPrevMeaningfulToken($namespaceEndIndex));
                        $className = $tokens[$classNameTokenIndex]->getContent();
                        $fqcn = $namespace . '\\' . $className;
                        if (class_exists($fqcn) && ! $this->isTransient($fqcn)) {
                            if (in_array($sourceFile, $this->newProxyFiles)) {
                                $newClassName = $className . Str::random(12);
                                $tokens[$classNameTokenIndex] = new Token([T_STRING, $newClassName]);
                                $newFilePath = $this->outputDir."${newClassName}.php";
                                file_put_contents($newFilePath, $tokens->generateCode());
                                require_once $newFilePath;
                                $results[] = $namespace . "\\${newClassName}";
                            } else {
                                $results[] = $fqcn;
                            }
                        }
                    }
                }
            }
            $currentIndex++;
        }
        return $results;
    }
}