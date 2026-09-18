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

namespace Eccube\Entity\Master;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Repository\Master\CountryIsoCodeRepository;

/**
 * CountryIsoCode.
 *
 * 国の ISO 3166-1 numeric (= mtb_country.id) と alpha-2 コードの対応マスタ。
 * 固定スキーマ (id/name/sort_no/discriminator_type) に従い、
 * id に ISO numeric、name に alpha-2 コード (例: US, JP) を格納する。
 */
#[ORM\Table(name: 'mtb_country_iso_code')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'discriminator_type', type: 'string', length: 255)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: CountryIsoCodeRepository::class)]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class CountryIsoCode extends AbstractMasterEntity
{
}
