<?php

namespace Eccube\Tests\Service\PurchaseFlow\Comparer;

use Eccube\Entity\CartItem;
use Eccube\Entity\ItemInterface;
use Eccube\Service\PurchaseFlow\Comparer\ItemComparerCollection;
use Eccube\Tests\EccubeTestCase;

class ItemComparerCollectionTest extends EccubeTestCase
{
    /**
     * @var ItemInterface
     */
    protected $Item1;

    /**
     * @var ItemInterface
     */
    protected $Item2;

    /**
     * @var ItemComparerCollection
     */
    protected $comparerCollection;

    /**
     * @var TrueComparer
     */
    protected $trueComparer;

    /**
     * @var FalseComparer
     */
    protected $falseComparer;

    public function setUp()
    {
        parent::setUp();

        $this->Item1 = new CartItem();
        $this->Item2 = new CartItem();
        $this->comparerCollection = new ItemComparerCollection();
        $this->trueComparer = new TrueComparer();
        $this->falseComparer = new FalseComparer();
    }

    public function testCompare_empty()
    {
        $this->verifyComparison(false);
    }

    public function testCompare_trueAll()
    {
        $this->comparerCollection->add($this->trueComparer);
        $this->comparerCollection->add($this->trueComparer);
        $this->verifyComparison(true);
    }

    public function testCompare_partiallyTrue()
    {
        $this->comparerCollection->add($this->trueComparer);
        $this->comparerCollection->add($this->falseComparer);
        $this->verifyComparison(false);
    }

    public function testCompare_falseAll()
    {
        $this->comparerCollection->add($this->falseComparer);
        $this->comparerCollection->add($this->falseComparer);
        $this->verifyComparison(false);
    }

    public function testSet_invalidArgument()
    {
        $values = [
            1,
            'hoge',
            $this,
        ];

        foreach ($values as $value) {
            try {
                $this->comparerCollection->add($value);
                $this->fail();
            } catch (\InvalidArgumentException $e) {
            } catch (\Exception $e) {
                $this->fail();
            }
        }
    }

    /**
     * @param bool $expect
     */
    public function verifyComparison($expect)
    {
        $result = $this->comparerCollection->compare($this->Item1, $this->Item2);

        $expect ?
            $this->assertTrue($result) :
            $this->assertFalse($result);
    }
}
