<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Doctrine\ORM\Mapping\Driver;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Doctrine\Persistence\Mapping\MappingException;

class TraitProxyAttributeDriver extends AttributeDriver
{
    protected string $trait_proxies_directory;

    public function setTraitProxiesDirectory(string $dir): void
    {
        $this->trait_proxies_directory = $dir;
    }

    /**
     * {@inheritdoc}
     */
    #[\Override]
    public function getAllClassNames(): ?array
    {
        if ($this->classNames !== null) {
            return $this->classNames;
        }

        if ($this->paths === []) {
            throw MappingException::pathRequiredForDriver(static::class);
        }

        $classes = [];
        $includedFiles = [];

        foreach ($this->paths as $path) {
            if (!is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+'.preg_quote($this->fileExtension).'$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', (string) $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                foreach ($this->excludePaths as $excludePath) {
                    $exclude = str_replace('\\', '/', realpath($excludePath));
                    $current = str_replace('\\', '/', $sourceFile);

                    if (str_contains($current, $exclude)) {
                        continue 2;
                    }
                }
                $projectDir = realpath(__DIR__.'/../../../../../../');
                if ('\\' === DIRECTORY_SEPARATOR) {
                    $path = str_replace('\\', '/', $path);
                    $this->trait_proxies_directory = str_replace('\\', '/', $this->trait_proxies_directory);
                    $sourceFile = str_replace('\\', '/', $sourceFile);
                    $projectDir = str_replace('\\', '/', $projectDir);
                }
                // Replace /path/to/ec-cube to proxies path
                $proxyFile = str_replace($projectDir, $this->trait_proxies_directory, $path).'/'.basename((string) $sourceFile);
                if (file_exists($proxyFile)) {
                    $sourceFile = $proxyFile;
                }

                // ソースファイルからFQCNを取得し、未宣言のクラスだけをロードする.
                // Proxy(app/proxy/entity)と元ソースは同一FQCNを持つため、既にロード済み
                // (例: Kernel::loadEntityProxies)の状態で require_once すると
                // "Cannot redeclare class" になる. 旧実装では各Entityの if (!class_exists())
                // ガードがこれを吸収していた.
                $classNames = $this->extractClassNames($sourceFile);
                if ($classNames === []) {
                    // interface / trait 等 (Entity以外) はそのままロードする
                    require_once $sourceFile;
                    $includedFiles[] = realpath($sourceFile);
                    continue;
                }

                $undeclared = array_filter($classNames, static fn ($fqcn) => !class_exists($fqcn, false));
                if ($undeclared !== []) {
                    require_once $sourceFile;
                }
                $includedFiles[] = realpath($sourceFile);

                foreach ($classNames as $className) {
                    if (class_exists($className) && !$this->isTransient($className)) {
                        $classes[] = $className;
                    }
                }
            }
        }

        $this->classNames = array_values(array_unique($classes));

        return $this->classNames;
    }

    /**
     * ソースファイルを字句解析して定義されているクラスのFQCNを返す.
     *
     * interface / trait / enum は対象外 (Entityではないため).
     *
     * @return array<int, string>
     */
    protected function extractClassNames(string $file): array
    {
        $contents = file_get_contents($file);
        if ($contents === false) {
            return [];
        }

        $tokens = \PhpToken::tokenize($contents);
        $count = count($tokens);
        $namespace = '';
        $classNames = [];

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];

            if ($token->is(T_NAMESPACE)) {
                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j]->is([T_STRING, T_NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED])) {
                        $namespace = ltrim($tokens[$j]->text, '\\');
                        break;
                    }
                    if (';' === $tokens[$j]->text || '{' === $tokens[$j]->text) {
                        break;
                    }
                }

                continue;
            }

            if ($token->is(T_CLASS)) {
                // ::class や無名クラスを除外する
                $prev = null;
                for ($j = $i - 1; $j >= 0; $j--) {
                    if ($tokens[$j]->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                        continue;
                    }
                    $prev = $tokens[$j];
                    break;
                }
                if ($prev !== null && $prev->is([T_DOUBLE_COLON, T_NEW])) {
                    continue;
                }

                for ($j = $i + 1; $j < $count; $j++) {
                    if ($tokens[$j]->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                        continue;
                    }
                    if ($tokens[$j]->is(T_STRING)) {
                        $classNames[] = '' !== $namespace ? $namespace.'\\'.$tokens[$j]->text : $tokens[$j]->text;
                    }
                    break;
                }
            }
        }

        return $classNames;
    }

    /** @return string[] */
    #[\Override]
    public function getPaths(): array
    {
        return $this->paths;
    }

    /** @return string[] */
    #[\Override]
    public function getExcludePaths(): array
    {
        return $this->excludePaths;
    }
}
