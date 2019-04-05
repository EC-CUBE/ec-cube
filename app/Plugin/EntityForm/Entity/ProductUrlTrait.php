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

namespace Plugin\EntityForm\Entity;

use Eccube\Annotation\EntityExtension;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityExtension("Eccube\Entity\Product")
 */
trait ProductUrlTrait
{
    /**
     * @ORM\Column(type="string", nullable=true, options={ "eccube_form_options": { "auto_render": true, "form_theme": "EntityForm/Form/product_url.twig" } })
     * @Assert\Url(message="外部の商品ページURLを入力してください。")
     */
    public $url;
}
