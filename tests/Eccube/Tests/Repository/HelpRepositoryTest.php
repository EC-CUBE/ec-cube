<?php

namespace Eccube\Tests\Repository;

use Eccube\Repository\HelpRepository;
use Eccube\Tests\EccubeTestCase;

/**
 * HelpRepository test cases.
 *
 * @author Kentaro Ohkouchi
 */
class HelpRepositoryTest extends EccubeTestCase
{
    /**
     * @var HelpRepository
     */
    protected $helpRepo;

    public function setUp()
    {
        parent::setUp();
        $this->helpRepo = $this->container->get(HelpRepository::class);
    }

    public function testGet()
    {
        $Help = $this->helpRepo->get();

        $this->expected = 1;
        $this->actual = $Help->getId();
        $this->verify();
    }

    public function testGetWithId()
    {
        $Help = $this->helpRepo->get(1);

        $this->expected = 1;
        $this->actual = $Help->getId();
        $this->verify();

        // MySQL では成功するが, PostgreSQL ではエラーになってしまう
        // $Help = $this->helpRepo->get('a');
        // $this->assertNull($Help);

        $Help = $this->helpRepo->get(5);
        $this->assertNull($Help);
    }
}
