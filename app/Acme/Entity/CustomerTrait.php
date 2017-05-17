<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityExtension("Eccube\Entity\Customer")
 */
trait CustomerTrait
{
    /**
     * @ORM\Column(
     *     name="company_name_vn",
     *     type="string",
     *     length=255,
     *     nullable=true,
     *     options={
     *          "eccube_form_options": {
     *              "auto_render": true,
     *              "form_theme": "Form/company_name_vn.twig"
     *          }
     *     })
     * @Assert\NotBlank(message="入力してくださいね！！！")
     */
    public $company_name_vn;
}
