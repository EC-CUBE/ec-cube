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
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\MappingException;

/**
 * Hybrid mapping driver that reads both PHP 8 attributes and legacy annotations.
 *
 * Core entities use #[ORM\*] attributes (primary).
 * Plugin entities may still use @ORM\* annotations (fallback with deprecation warning).
 */
class HybridMappingDriver implements MappingDriver
{
    protected ?array $classNames = null;
    protected string $trait_proxies_directory = '';
    protected string $fileExtension = '.php';
    protected array $paths;
    protected array $excludePaths = [];

    private AttributeDriver $attributeDriver;

    public function __construct(array $paths)
    {
        $this->paths = $paths;
        $this->attributeDriver = new AttributeDriver($paths);
    }

    /**
     * Get the paths where entity classes are located.
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get the paths to exclude from entity scanning.
     *
     * @return array
     */
    public function getExcludePaths(): array
    {
        return $this->excludePaths;
    }

    public function setTraitProxiesDirectory(string $dir): void
    {
        $this->trait_proxies_directory = $dir;
    }

    /**
     * {@inheritdoc}
     */
    public function loadMetadataForClass($className, \Doctrine\Persistence\Mapping\ClassMetadata $metadata): void
    {
        $this->attributeDriver->loadMetadataForClass($className, $metadata);
    }

    /**
     * {@inheritdoc}
     */
    public function isTransient($className): bool
    {
        return $this->attributeDriver->isTransient($className);
    }

    /**
     * {@inheritdoc}
     *
     * Scans entity paths with proxy file resolution (same logic as the original AnnotationDriver).
     */
    public function getAllClassNames(): array
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

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                foreach ($this->excludePaths as $excludePath) {
                    $exclude = str_replace('\\', '/', realpath($excludePath));
                    $current = str_replace('\\', '/', $sourceFile);

                    if (strpos($current, $exclude) !== false) {
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
                if ($this->trait_proxies_directory) {
                    $proxyFile = str_replace($projectDir, $this->trait_proxies_directory, $path).'/'.basename($sourceFile);
                    if (file_exists($proxyFile)) {
                        require_once $proxyFile;
                        $sourceFile = $proxyFile;
                    } else {
                        require_once $sourceFile;
                    }
                } else {
                    require_once $sourceFile;
                }

                $includedFiles[] = realpath($sourceFile);
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc = new \ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (in_array($sourceFile, $includedFiles) && !$this->isTransient($className)) {
                $classes[] = $className;
            }
        }

        $this->classNames = $classes;

        return $classes;
    }
}
