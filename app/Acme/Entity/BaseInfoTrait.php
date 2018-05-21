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

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation as Eccube;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Eccube\EntityExtension("Eccube\Entity\BaseInfo")
 */
trait BaseInfoTrait
{
    /**
     * @ORM\Column(name="company_name_vn", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="にゅうりょくしてくださいね！！！")
     * @Eccube\FormAppend(
     *     auto_render=false,
     *     form_theme="Form/company_name_vn.twig",
     *     type="\Symfony\Component\Form\Extension\Core\Type\TextType",
     *     options={
     *          "required": true,
     *          "label": "会社名(VN)"
     *     })
     */
    public $company_name_vn;
}
