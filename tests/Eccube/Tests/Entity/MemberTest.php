<?php

namespace Eccube\Tests\Entity;

use Eccube\Entity\Member;
use Eccube\Tests\EccubeTestCase;

/**
 * Member test cases.
 *
 * @author Kentaro Ohkouchi
 */
class MemberTest extends EccubeTestCase
{
    protected $Member;

    public function setUp()
    {
        parent::setUp();
        $this->Member = $this->createMember();
    }

    public function testSerialize()
    {
        $this->expected = serialize(
            array(
                $this->Member->getId(),
                $this->Member->getUsername(),
                $this->Member->getPassword(),
                $this->Member->getSalt()
            )
        );
        $this->actual = $this->Member->serialize();
        $this->verify();
    }

    public function testUnserialize()
    {
        $serializable = serialize(
            array(
                $this->Member->getId(),
                $this->Member->getUsername(),
                $this->Member->getPassword(),
                $this->Member->getSalt()
            )
        );
        $this->expected = clone $this->Member;
        $this->Member->unserialize($serializable);
        $this->actual = $this->Member;
        $this->verify();
    }
}
