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
 * @Eccube\EntityExtension("Eccube\Entity\Customer")
 */
trait CustomerTrait
{
    /**
     * @ORM\Column(name="company_name_vn", type="string", length=255, nullable=true)
     * @Assert\NotBlank(message="入力してくださいね！！！")
     * @Eccube\FormAppend(
     *     auto_render=true,
     *     form_theme="Form/company_name_vn.twig",
     *     type="\Symfony\Component\Form\Extension\Core\Type\TextareaType",
     *     options={
     *          "required": true,
     *          "label": "会社名(VN)"
     *     })
     */
    public $company_name_vn;
}
