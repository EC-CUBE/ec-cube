<?php

namespace Acme\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExt;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @EntityExt(target="Eccube\Entity\BaseInfo")
 */
trait BaseInfoTrait
{
    /**
     * @ORM\Column(name="company_name_vn", type="string", length=255, nullable=true, options={"eccube_form_render":true})
     * @Assert\NotBlank(message="にゅうりょくしてくださいね！！！")
     */
    public $company_name_vn;
}