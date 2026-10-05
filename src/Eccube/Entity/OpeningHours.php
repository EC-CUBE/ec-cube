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
use Eccube\Repository\OpeningHoursRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * 営業時間（構造化データ schema.org OpeningHoursSpecification の 1 エントリ）.
 *
 * 1 レコード = 「曜日群 + 開店時刻 + 閉店時刻」で、店舗設定（BaseInfo）に複数紐づく.
 */
#[ORM\Table(name: 'dtb_opening_hours')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\Entity(repositoryClass: OpeningHoursRepository::class)]
class OpeningHours extends AbstractEntity
{
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    /**
     * 曜日（schema.org DayOfWeek。例: Monday）のリスト.
     *
     * @var array<int, string>|null
     */
    #[ORM\Column(name: 'day_of_week', type: Types::SIMPLE_ARRAY, nullable: true)]
    private ?array $day_of_week = null;

    #[ORM\Column(name: 'opens', type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $opens = null;

    #[ORM\Column(name: 'closes', type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTime $closes = null;

    #[ORM\Column(name: 'sort_no', type: Types::INTEGER, options: ['default' => 0])]
    private int $sort_no = 0;

    #[ORM\ManyToOne(targetEntity: BaseInfo::class, inversedBy: 'OpeningHours')]
    #[ORM\JoinColumn(name: 'base_info_id', referencedColumnName: 'id')]
    private ?BaseInfo $BaseInfo = null;

    /**
     * 営業時間 1 エントリの入力内容を検証する.
     *
     * - いずれかが入力されている行は、曜日・開店・閉店をすべて必須とする
     * - 開店時刻は閉店時刻より前でなければならない
     * （すべて空の行は未使用行として検証しない）
     */
    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        $hasDay = $this->day_of_week !== null && $this->day_of_week !== [];
        $hasOpens = $this->opens !== null;
        $hasCloses = $this->closes !== null;

        if (!$hasDay && !$hasOpens && !$hasCloses) {
            return;
        }

        if (!$hasDay) {
            $context->buildViolation('admin.setting.shop.opening_hours.error.day_required')
                ->atPath('day_of_week')
                ->addViolation();
        }
        if (!$hasOpens) {
            $context->buildViolation('admin.setting.shop.opening_hours.error.opens_required')
                ->atPath('opens')
                ->addViolation();
        }
        if (!$hasCloses) {
            $context->buildViolation('admin.setting.shop.opening_hours.error.closes_required')
                ->atPath('closes')
                ->addViolation();
        }
        if ($hasOpens && $hasCloses && $this->opens >= $this->closes) {
            $context->buildViolation('admin.setting.shop.opening_hours.error.opens_before_closes')
                ->atPath('closes')
                ->addViolation();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return array<int, string>|null
     */
    public function getDayOfWeek(): ?array
    {
        return $this->day_of_week;
    }

    /**
     * @param array<int, string>|null $dayOfWeek
     */
    public function setDayOfWeek(?array $dayOfWeek): OpeningHours
    {
        $this->day_of_week = $dayOfWeek;

        return $this;
    }

    public function getOpens(): ?\DateTime
    {
        return $this->opens;
    }

    public function setOpens(?\DateTime $opens): OpeningHours
    {
        $this->opens = $opens;

        return $this;
    }

    public function getCloses(): ?\DateTime
    {
        return $this->closes;
    }

    public function setCloses(?\DateTime $closes): OpeningHours
    {
        $this->closes = $closes;

        return $this;
    }

    public function getSortNo(): int
    {
        return $this->sort_no;
    }

    public function setSortNo(int $sortNo): OpeningHours
    {
        $this->sort_no = $sortNo;

        return $this;
    }

    public function getBaseInfo(): ?BaseInfo
    {
        return $this->BaseInfo;
    }

    public function setBaseInfo(?BaseInfo $baseInfo): OpeningHours
    {
        $this->BaseInfo = $baseInfo;

        return $this;
    }
}
