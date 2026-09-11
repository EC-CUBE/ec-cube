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
use Eccube\Repository\FaqRepository;

/**
 * Faq
 *
 * よくある質問（FAQ）。区分は Product / Category の設定状態で導出する。
 *  - Product / Category ともに null … サイト共通FAQ
 *  - Product のみ設定             … 商品ごとFAQ
 *  - Category のみ設定            … カテゴリごとFAQ
 */
#[ORM\Table(name: 'dtb_faq')]
#[ORM\Index(columns: ['product_id'], name: 'dtb_faq_product_id_idx')]
#[ORM\Index(columns: ['category_id'], name: 'dtb_faq_category_id_idx')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: FaqRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Faq extends AbstractEntity implements \Stringable
{
    /**
     * サイト共通FAQ.
     */
    public const FAQ_TYPE_COMMON = 'common';

    /**
     * 商品ごとFAQ.
     */
    public const FAQ_TYPE_PRODUCT = 'product';

    /**
     * カテゴリごとFAQ.
     */
    public const FAQ_TYPE_CATEGORY = 'category';

    #[\Override]
    public function __toString(): string
    {
        return (string) $this->getQuestion();
    }

    public function __clone()
    {
        $this->id = null;
    }

    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'question', type: Types::STRING, length: 255)]
    private ?string $question = null;

    #[ORM\Column(name: 'answer', type: Types::TEXT, nullable: true)]
    private ?string $answer = null;

    /**
     * 表示順.
     *
     * カラムは NOT NULL のため、既定値は 0 のままにして `new Faq()` を常に永続化可能に保つ。
     * フォームから明示的に null が入ったときだけ FaqRepository::save() が最大値 + 1 を採番する。
     */
    #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['default' => 0])]
    private ?int $sort_no = 0;

    #[ORM\Column(name: 'visible', type: Types::BOOLEAN, options: ['default' => true])]
    private bool $visible = true;

    #[ORM\Column(name: 'create_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $create_date = null;

    #[ORM\Column(name: 'update_date', type: Types::DATETIMETZ_MUTABLE)]
    private ?\DateTime $update_date = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'Faqs')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Product $Product = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'Faqs')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Category $Category = null;

    #[ORM\ManyToOne(targetEntity: Member::class)]
    #[ORM\JoinColumn(name: 'creator_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private ?Member $Creator = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setAnswer(?string $answer = null): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setSortNo(?int $sortNo): self
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    public function getSortNo(): ?int
    {
        return $this->sort_no;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setCreateDate(\DateTime $createDate): self
    {
        $this->create_date = $createDate;

        return $this;
    }

    public function getCreateDate(): ?\DateTime
    {
        return $this->create_date;
    }

    public function setUpdateDate(\DateTime $updateDate): self
    {
        $this->update_date = $updateDate;

        return $this;
    }

    public function getUpdateDate(): ?\DateTime
    {
        return $this->update_date;
    }

    /**
     * 商品ごとFAQ の紐付け先を設定する.
     *
     * Product と Category が同時に設定されることは想定しない（管理画面には両方を設定する経路が無く、
     * それぞれ Product::addFaq() / Category::addFaq() の一方だけを通る）。
     * 区分の導出は getFaqType() に集約している。
     */
    public function setProduct(?Product $product = null): self
    {
        $this->Product = $product;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->Product;
    }

    /**
     * カテゴリごとFAQ の紐付け先を設定する.
     *
     * Product との同時設定を想定しない点は setProduct() と同じ。
     */
    public function setCategory(?Category $category = null): self
    {
        $this->Category = $category;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCreator(?Member $creator = null): self
    {
        $this->Creator = $creator;

        return $this;
    }

    public function getCreator(): ?Member
    {
        return $this->Creator;
    }

    /**
     * FAQ の区分を返す（Product / Category の設定状態から導出）.
     */
    public function getFaqType(): string
    {
        if ($this->Product !== null) {
            return self::FAQ_TYPE_PRODUCT;
        }
        if ($this->Category !== null) {
            return self::FAQ_TYPE_CATEGORY;
        }

        return self::FAQ_TYPE_COMMON;
    }
}
