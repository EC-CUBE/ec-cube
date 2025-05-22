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

namespace Plugin\Emperor\Entity;


use Doctrine\ORM\Mapping as ORM;

if (!class_exists('Plugin\Emperor\Entity\Foo')) {
    /**
     * Plugin
     *
     * @ORM\Table(name="dtb_foo")
     * @ORM\InheritanceType("SINGLE_TABLE")
     * @ORM\DiscriminatorColumn(name="discriminator_type", type="string", length=255)
     * @ORM\HasLifecycleCallbacks()
     * @ORM\Entity(repositoryClass="Plugin\Emperor\Repository\FooRepository")
     */
    class Foo
    {
        /**
         * @var int
         *
         * @ORM\Column(name="id", type="integer", options={"unsigned":true})
         * @ORM\Id
         * @ORM\GeneratedValue(strategy="IDENTITY")
         */
        public $id;

        /**
         * @var string
         *
         * @ORM\Column(name="name", type="string", length=255)
         */
        public $name;
    }
}
