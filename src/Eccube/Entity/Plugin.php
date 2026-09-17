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

namespace Eccube\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\PluginRepository;

/**
 * Plugin
 */
#[ORM\Table(name: 'dtb_plugin')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PluginRepository::class)]
class Plugin extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'name', type: Types::STRING, length: 255)]
    private ?string $name = null;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 255)]
    private ?string $code = null;

    #[ORM\Column(name: 'enabled', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $enabled = false;

    #[ORM\Column(name: 'version', type: Types::STRING, length: 255)]
    private ?string $version = null;

    #[ORM\Column(name: 'source', type: Types::STRING, length: 255)]
    private ?string $source = null;

    #[ORM\Column(name: 'initialized', type: Types::BOOLEAN, options: ['default' => false])]
    private bool $initialized = false;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set name.
     */
    public function setName(string $name): Plugin
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Set code.
     */
    public function setCode(string $code): Plugin
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code.
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set enabled.
     */
    public function setEnabled(bool $enabled): Plugin
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Set version.
     */
    public function setVersion(string $version): Plugin
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Set source.
     */
    public function setSource(string|int $source): Plugin
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * Get initialized.
     */
    public function isInitialized(): bool
    {
        return $this->initialized;
    }

    /**
     * Set initialized.
     */
    public function setInitialized(bool $initialized): Plugin
    {
        $this->initialized = $initialized;

        return $this;
    }

    /**
     * Set createDate.
     */
    public function setCreateDate(\DateTime $createDate): Plugin
    {
        $this->create_date = $createDate;

        return $this;
    }

    /**
     * Get createDate.
     */
    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    /**
     * Set updateDate.
     */
    public function setUpdateDate(\DateTime $updateDate): Plugin
    {
        $this->update_date = $updateDate;

        return $this;
    }

    /**
     * Get updateDate.
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }
}
