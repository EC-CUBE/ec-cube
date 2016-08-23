<?php

namespace Eccube\Tests\Fixture;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Delivery;
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Shipping;
use Eccube\Entity\ShipmentItem;
use Eccube\Entity\Member;
use Eccube\Entity\Master\CustomerStatus;
use Faker\Factory as Faker;

/**
 * Fixture Object Generator.
 *
 * @author Kentaro Ohkouchi
 */
class Generator {

    protected $app;

    public function __construct($app) {
        $this->app = $app;
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username. null の場合は, ランダムなユーザーIDが生成される.
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        $faker = $this->getFaker();
        $Member = new Member();
        if (is_null($username)) {
            $username = $faker->word;
        }
        $Work = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Work')->find(1);
        $Authority = $this->app['eccube.repository.master.authority']->find(0);
        $Creator = $this->app['eccube.repository.member']->find(2);
        $salt = $this->app['eccube.repository.member']->createSalt(5);

        $Member
            ->setPassword('password')
            ->setLoginId($username)
            ->setName($username)
            ->setSalt($salt)
            ->setWork($Work)
            ->setAuthority($Authority)
            ->setCreator($Creator);
        $password = $this->app['eccube.repository.member']->encryptPassword($Member);
        $Member->setPassword($password);
        $this->app['eccube.repository.member']->save($Member);
        return $Member;
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->email;
        }
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setEmail($email)
            ->setPref($Pref)
            ->setPassword('password')
            ->setSalt($this->app['eccube.repository.customer']->createSalt(5))
            ->setSecretKey($this->app['eccube.repository.customer']->getUniqueSecretKey($this->app))
            ->setStatus($Status)
            ->setDelFlg(Constant::DISABLED);
        $Customer->setPassword($this->app['eccube.repository.customer']->encryptPassword($this->app, $Customer));
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush($Customer);

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setDelFlg(Constant::DISABLED);
        $CustomerAddress->copyProperties($Customer);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush($CustomerAddress);

        return $Customer;
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     * @return \Eccube\Entity\Customer
     */
    public function createNonMember($email = null)
    {
        $sessionKey = 'eccube.front.shopping.nonmember';
        $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->email;
        }
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setEmail($email)
            ->setPref($Pref)
            ->setDelFlg(Constant::DISABLED);

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setDelFlg(Constant::DISABLED);
        $CustomerAddress->copyProperties($Customer);
        $Customer->addCustomerAddress($CustomerAddress);

        $nonMember = array();
        $nonMember['customer'] = $Customer;
        $nonMember['pref'] = $Customer->getPref()->getId();
        $this->app['session']->set($sessionKey, $nonMember);

        $customerAddresses = array();
        $customerAddresses[] = $CustomerAddress;
        $this->app['session']->set($sessionCustomerAddressKey, serialize($customerAddresses));
        return $Customer;
    }

    /**
     * Product オブジェクトを生成して返す.
     *
     * $product_class_num = 0 としても、商品規格の無い商品を生成できない. 単に ProductClass が生成されないだけなので注意すること.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param integer $product_class_num 商品規格の生成数
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3)
    {
        $faker = $this->getFaker();
        $Member = $this->app['eccube.repository.member']->find(2);
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        $ProductType = $this->app['eccube.repository.master.product_type']->find(1);
        $Product = new Product();
        if (is_null($product_name)) {
            $product_name = $faker->word;
        }

        $Product
            ->setName($product_name)
            ->setCreator($Member)
            ->setStatus($Disp)
            ->setDelFlg(Constant::DISABLED)
            ->setDescriptionList($faker->paragraph())
            ->setDescriptionDetail($faker->text());

        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush($Product);

        for ($i = 0; $i < 3; $i++) {
            $ProductImage = new ProductImage();
            $ProductImage
                ->setCreator($Member)
                ->setFileName($faker->word.'.jpg')
                ->setRank($i)
                ->setProduct($Product);
            $this->app['orm.em']->persist($ProductImage);
            $this->app['orm.em']->flush($ProductImage);
            $Product->addProductImage($ProductImage);
        }

        for ($i = 0; $i < $product_class_num; $i++) {
            $ProductStock = new ProductStock();
            $ProductStock
                ->setCreator($Member)
                ->setStock($faker->randomNumber());
            $this->app['orm.em']->persist($ProductStock);
            $this->app['orm.em']->flush($ProductStock);
            $ProductClass = new ProductClass();
            $ProductClass
                ->setCreator($Member)
                ->setProductStock($ProductStock)
                ->setProduct($Product)
                ->setProductType($ProductType)
                ->setStockUnlimited(false)
                ->setPrice02($faker->randomNumber(5))
                ->setDelFlg(Constant::DISABLED);
            $this->app['orm.em']->persist($ProductClass);
            $this->app['orm.em']->flush($ProductClass);
            $Product->addProductClass($ProductClass);
        }

        $Categories = $this->app['eccube.repository.category']->findAll();
        $i = 0;
        foreach ($Categories as $Category) {
            $ProductCategory = new ProductCategory();
            $ProductCategory
                ->setCategory($Category)
                ->setProduct($Product)
                ->setCategoryId($Category->getId())
                ->setProductId($Product->getId())
                ->setRank($i);
            $this->app['orm.em']->persist($ProductCategory);
            $this->app['orm.em']->flush($ProductCategory);
            $Product->addProductCategory($ProductCategory);
            $i++;
        }

        $this->app['orm.em']->flush($Product);
        return $Product;
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param \Eccube\Entity\Customer $Customer Customer インスタンス
     * @param array $ProductClasses 明細行となる ProductClass の配列
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer, array $ProductClasses = array())
    {
        $faker = $this->getFaker();
        $quantity = $faker->randomNumber(2);
        $Pref = $this->app['eccube.repository.master.pref']->find(1);
        $Order = new Order($this->app['eccube.repository.order_status']->find($this->app['config']['order_processing']));
        $Order->setCustomer($Customer);
        $Order->copyProperties($Customer);
        $Order->setPref($Pref);
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush($Order);

        $Delivery = $this->app['eccube.repository.delivery']->find(1);
        $Shipping = new Shipping();
        $Shipping->copyProperties($Customer);
        $Shipping
            ->setPref($Pref)
            ->setDelivery($Delivery);
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);
        $this->app['orm.em']->persist($Shipping);
        $this->app['orm.em']->flush($Shipping);

        if (empty($ProductClassess)) {
            $Product = $this->createProduct();
            $ProductClasses = $Product->getProductClasses();
        }

        $subTotal = 0;
        foreach ($ProductClasses as $ProductClass) {
            $Product = $ProductClass->getProduct();
            $OrderDetail = new OrderDetail();
            $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則
            $OrderDetail->setProduct($Product)
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity)
                ->setTaxRule($TaxRule->getCalcRule()->getId())
                ->setTaxRate($TaxRule->getTaxRate());
            $this->app['orm.em']->persist($OrderDetail);
            $OrderDetail->setOrder($Order);
            $this->app['orm.em']->flush($OrderDetail);
            $Order->addOrderDetail($OrderDetail);

            $ShipmentItem = new ShipmentItem();
            $ShipmentItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity);
            $Shipping->addShipmentItem($ShipmentItem);
            $this->app['orm.em']->persist($ShipmentItem);
            $this->app['orm.em']->flush($ShipmentItem);
            $subTotal += $OrderDetail->getPriceIncTax() * $OrderDetail->getQuantity();
        }

        // TODO 送料, 手数料の加算
        $Order->setSubTotal($subTotal);
        $Order->setTotal($subTotal);
        $Order->setPaymentTotal($subTotal);

        $this->app['orm.em']->flush($Order);
        return $Order;
    }

    /**
     * Payment オプジェクトを生成して返す.
     *
     * @param Delivery $Delivery デフォルトで設定する配送オブジェクト
     * @param string $method 支払い方法名称
     * @param integer $charge 手数料
     * @param integer $rule_min 下限金額
     * @param integer $rule_max 上限金額
     * @return \Eccube\Entity\Payment
     */
    public function createPayment(Delivery $Delivery, $method, $charge = 0, $rule_min = 0, $rule_max = 999999999)
    {
        $Member = $this->app['eccube.repository.member']->find(2);
        $Payment = new Payment();
        $Payment
            ->setMethod($method)
            ->setCharge($charge)
            ->setRuleMin($rule_min)
            ->setRuleMax($rule_max)
            ->setCreator($Member)
            ->setDelFlg(Constant::DISABLED);
        $this->app['orm.em']->persist($Payment);
        $this->app['orm.em']->flush($Payment);

        $PaymentOption = new PaymentOption();
        $PaymentOption
            ->setDeliveryId($Delivery->getId())
            ->setPaymentId($Payment->getId())
            ->setDelivery($Delivery)
            ->setPayment($Payment);
        $Payment->addPaymentOption($PaymentOption);

        $this->app['orm.em']->persist($PaymentOption);
        $this->app['orm.em']->flush($PaymentOption);
        return $Payment;
    }

    /**
     * Faker を生成する.
     *
     * @param string $locale ロケールを指定する. デフォルト ja_JP
     * @return Faker\Generator
     * @link https://github.com/fzaninotto/Faker
     */
    protected function getFaker($locale = 'ja_JP')
    {
        return Faker::create($locale);
    }
}
