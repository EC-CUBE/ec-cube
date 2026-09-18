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

use Doctrine\ORM\Mapping as ORM;
use Eccube\Attribute\EntityExtension;
use Eccube\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityExtension(Product::class)]
trait ProductUrlTrait
{
    /**
     * @var string|null
     */
    #[ORM\Column(name: 'url', type: 'string', length: 4000, nullable: true, options: ['eccube_form_options' => ['auto_render' => true, 'form_theme' => 'EntityForm/Form/product_url.twig']])]
    #[Assert\Url(message: '外部の商品ページURLを入力してください。')]
    public $url;
}
