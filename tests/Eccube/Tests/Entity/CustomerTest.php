<?php

namespace Eccube\Tests\Entity;

use Eccube\Entity\Member;
use Eccube\Tests\EccubeTestCase;

/**
 * Customer test cases.
 *
 * @author Kentaro Ohkouchi
 */
class CustomerTest extends EccubeTestCase
{
    protected $Customer;

    public function setUp()
    {
        parent::setUp();
        $this->Customer = $this->createCustomer();
    }

    public function testSerialize()
    {
        $this->expected = serialize(
            array(
                $this->Customer->getId(),
                $this->Customer->getUsername(),
                $this->Customer->getPassword(),
                $this->Customer->getSalt()
            )
        );
        $this->actual = $this->Customer->serialize();
        $this->verify();
    }

    public function testUnserialize()
    {
        $serializable = serialize(
            array(
                $this->Customer->getId(),
                $this->Customer->getUsername(),
                $this->Customer->getPassword(),
                $this->Customer->getSalt()
            )
        );
        $this->expected = clone $this->Customer;
        $this->Customer->unserialize($serializable);
        $this->actual = $this->Customer;
        $this->verify();
    }
}
