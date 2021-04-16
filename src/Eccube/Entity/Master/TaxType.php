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

if (!class_exists(TaxType::class, false)) {
    /**
     * TaxType
     *
     * 消費税の課税区分
     *
     * @ORM\Table(name="mtb_tax_type")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Eccube\Repository\Master\TaxTypeRepository")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     *
     * @see https://www.nta.go.jp/taxanswer/shohi/6209.htm
     */
    class TaxType extends \Eccube\Entity\Master\AbstractMasterEntity
    {
        /**
         * 課税.
         *
         * 消費税は、国内において事業者が事業として対価を得て行う取引を課税の対象としています。
         *
         * @var integer
         */
        const TAXATION = 1;

        /**
         * 不課税.
         *
         * 消費税の課税の対象は、国内において事業者が事業として対価を得て行う資産の譲渡等と輸入取引です。
         * これに当たらない取引には消費税はかかりません。
         * 例えば、国外取引、対価を得て行うことに当たらない寄附や単なる贈与、出資に対する配当などがこれに当たります。
         *
         * @var integer
         */
        const NON_TAXABLE = 2;

        /**
         * 非課税.
         *
         * 国内において事業者が事業として対価を得て行う資産の譲渡等であっても、課税対象になじまないものや社会政策的配慮から消費税を課税しない取引があります。
         * これを非課税取引といいます。
         * 例えば、土地、有価証券、商品券などの譲渡、預貯金の利子や社会保険医療などがこれに当たります。
         *
         * @var integer
         */
        const TAX_EXEMPT = 3;
    }
}
