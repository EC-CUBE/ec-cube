<?php

namespace Eccube\Tests\Fixture;

use Eccube\Application;
use Eccube\Common\Constant;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\ShippingStatus;
use Eccube\Entity\Order;
use Eccube\Entity\Page;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Shipping;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Member;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Tests\EccubeTestCase;
use Faker\Factory as Faker;

/**
 * Fixture Object Generator.
 *
 * @author Kentaro Ohkouchi
 */
class Generator {

    protected $app;
    protected $locale;

    public function __construct($app, $locale = 'ja_JP') {
        $this->app = $app;
        $this->locale = $locale;
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

        $salt = bin2hex(openssl_random_pseudo_bytes(5));
        $password = 'password';
        $encoder = $this->app['security.encoder_factory']->getEncoder($Member);
        $password = $encoder->encodePassword($password, $salt);

        $Member
            ->setLoginId($username)
            ->setName($username)
            ->setSalt($salt)
            ->setPassword($password)
            ->setWork($Work)
            ->setAuthority($Authority)
            ->setCreator($Creator);
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
            $email = $faker->safeEmail;
        }
        $tel = explode('-', $faker->phoneNumber);
        $fax = explode('-', $faker->phoneNumber);
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $Pref = $this->app['eccube.repository.master.pref']->find($faker->numberBetween(1, 47));
        $Sex = $this->app['eccube.repository.master.sex']->find($faker->numberBetween(1, 2));
        $Job = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Job')->find($faker->numberBetween(1, 18));

        $encoder = $this->app['security.encoder_factory']->getEncoder($Customer);
        $salt = $encoder->createSalt();
        $password = $encoder->encodePassword('password', $salt);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01(isset($faker->lastKanaName) ? $faker->lastKanaName : '')
            ->setKana02(isset($faker->firstKanaName) ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setZip01(isset($faker->postcode1) ? $faker->postcode1 : null)
            ->setZip02(isset($faker->postcode2) ? $faker->postcode2 : null)
            ->setZipcode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02(isset($tel[1]) ? $tel[1] : null)
            ->setTel03(isset($tel[2]) ? $tel[2] : null)
            ->setFax01($fax[0])
            ->setFax02(isset($fax[1]) ? $fax[1] : null)
            ->setFax03(isset($fax[2]) ? $fax[2] : null)
            ->setBirth($faker->dateTimeThisDecade())
            ->setSex($Sex)
            ->setJob($Job)
            ->setPassword($password)
            ->setSalt($salt)
            ->setSecretKey($this->app['eccube.repository.customer']->getUniqueSecretKey())
            ->setStatus($Status)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime());
        $this->app['orm.em']->persist($Customer);
        $this->app['orm.em']->flush($Customer);

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($Customer);
        $CustomerAddress->copyProperties($Customer);
        $this->app['orm.em']->persist($CustomerAddress);
        $this->app['orm.em']->flush($CustomerAddress);

        $Customer->addCustomerAddress($CustomerAddress);
        $this->app['orm.em']->flush($Customer);
        return $Customer;
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param boolean $is_nonmember 非会員の場合 true
     * @return CustomerAddress
     */
    public function createCustomerAddress(Customer $Customer, $is_nonmember = false)
    {
        $faker = $this->getFaker();
        $Pref = $this->app['eccube.repository.master.pref']->find($faker->numberBetween(1, 47));
        $tel = explode('-', $faker->phoneNumber);
        $fax = explode('-', $faker->phoneNumber);
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01(isset($faker->lastKanaName) ? $faker->lastKanaName : '')
            ->setKana02(isset($faker->firstKanaName) ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setZip01(isset($faker->postcode1) ? $faker->postcode1 : null)
            ->setZip02(isset($faker->postcode2) ? $faker->postcode2 : null)
            ->setZipcode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02(isset($tel[1]) ? $tel[1] : null)
            ->setTel03(isset($tel[2]) ? $tel[2] : null)
            ->setFax01($fax[0])
            ->setFax02(isset($fax[1]) ? $fax[1] : null)
            ->setFax03(isset($fax[2]) ? $fax[2] : null);
        if ($is_nonmember) {
            $Customer->addCustomerAddress($CustomerAddress);
            // TODO 外部でやった方がいい？
            $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';
            $customerAddresses = unserialize($this->app['session']->get($sessionCustomerAddressKey));
            if (!is_array($customerAddresses)) {
                $customerAddresses = array();
            }
            $customerAddresses[] = $CustomerAddress;
            $this->app['session']->set($sessionCustomerAddressKey, serialize($customerAddresses));
        } else {
            $this->app['orm.em']->persist($CustomerAddress);
            $this->app['orm.em']->flush($CustomerAddress);
        }

        return $CustomerAddress;
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
            $email = $faker->safeEmail;
        }
        $Pref = $this->app['eccube.repository.master.pref']->find($faker->numberBetween(1, 47));
        $tel = explode('-', $faker->phoneNumber);
        $fax = explode('-', $faker->phoneNumber);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01(isset($faker->lastKanaName) ? $faker->lastKanaName : '')
            ->setKana02(isset($faker->firstKanaName) ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setZip01(isset($faker->postcode1) ? $faker->postcode1 : null)
            ->setZip02(isset($faker->postcode2) ? $faker->postcode2 : null)
            ->setZipcode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02(isset($tel[1]) ? $tel[1] : null)
            ->setTel03(isset($tel[2]) ? $tel[2] : null)
            ->setFax01($fax[0])
            ->setFax02(isset($fax[1]) ? $fax[1] : null)
            ->setFax03(isset($fax[2]) ? $fax[2] : null);

        $CustomerAddress = new CustomerAddress();
        $CustomerAddress->setCustomer($Customer);
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
     * $product_class_num = 0 とすると商品規格の無い商品を生成する.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param integer $product_class_num 商品規格の生成数
     * @param string $image_type 生成する画像タイプ.
     *        abstract, animals, business, cats, city, food, night, life, fashion, people, nature, sports, technics, transport から選択可能
     *        null の場合は、画像を生成せずにファイル名のみを設定する.
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3, $image_type = null)
    {
        $faker = $this->getFaker();
        $Member = $this->app['eccube.repository.member']->find(2);
        $ProductStatus = $this->app['eccube.repository.master.product_status']->find(\Eccube\Entity\Master\ProductStatus::DISPLAY_SHOW);
        $SaleType = $this->app['eccube.repository.master.sale_type']->find(1);
        $DeliveryDates = $this->app['eccube.repository.delivery_date']->findAll();

        $Product = new Product();
        if (is_null($product_name)) {
            $product_name = $faker->realText($faker->numberBetween(10, 50));
        }
        $Product
            ->setName($product_name)
            ->setCreator($Member)
            ->setStatus($ProductStatus)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setDescriptionList($faker->paragraph())
            ->setDescriptionDetail($faker->realText());
        $Product->extendedParameter = "aaaa";

        $this->app['orm.em']->persist($Product);
        $this->app['orm.em']->flush($Product);

        for ($i = 0; $i < 3; $i++) {
            $ProductImage = new ProductImage();
            if ($image_type) {
                $image = $faker->image(
                    __DIR__.'/../../../../html/upload/save_image',
                    $faker->numberBetween(480, 640),
                    $faker->numberBetween(480, 640),
                    $image_type, false);
            } else {
                $image = $faker->word.'.jpg';
            }
            $ProductImage
                ->setCreator($Member)
                ->setFileName($image)
                ->setRank($i)
                ->setCreateDate(new \DateTime()) // FIXME
                ->setProduct($Product);
            $this->app['orm.em']->persist($ProductImage);
            $this->app['orm.em']->flush($ProductImage);
            $Product->addProductImage($ProductImage);
        }

        $ClassNames = $this->app['eccube.repository.class_name']->findAll();
        $ClassName1 = $ClassNames[$faker->numberBetween(0, count($ClassNames) - 1)];
        $ClassName2 = $ClassNames[$faker->numberBetween(0, count($ClassNames) - 1)];
        // 同じ ClassName が選択された場合は ClassName1 のみ
        if ($ClassName1->getId() === $ClassName2->getId()) {
            $ClassName2 = null;
        }
        $ClassCategories1 = $this->app['eccube.repository.class_category']->findBy(array('ClassName' => $ClassName1));
        $ClassCategories2 = array();
        if (is_object($ClassName2)) {
            $ClassCategories2 = $this->app['eccube.repository.class_category']->findBy(array('ClassName' => $ClassName2));
        }

        for ($i = 0; $i < $product_class_num; $i++) {
            $ProductStock = new ProductStock();
            $ProductStock
                ->setCreateDate(new \DateTime()) // FIXME
                ->setUpdateDate(new \DateTime())
                ->setCreator($Member)
                ->setStock($faker->randomNumber(3));
            $this->app['orm.em']->persist($ProductStock);
            $this->app['orm.em']->flush($ProductStock);
            $ProductClass = new ProductClass();
            $ProductClass
                ->setCode($faker->word)
                ->setCreator($Member)
                ->setStock($ProductStock->getStock())
                ->setProductStock($ProductStock)
                ->setProduct($Product)
                ->setSaleType($SaleType)
                ->setStockUnlimited(false)
                ->setPrice02($faker->randomNumber(5))
                ->setDeliveryDate($DeliveryDates[$faker->numberBetween(0, 8)])
                ->setCreateDate(new \DateTime()) // FIXME
                ->setUpdateDate(new \DateTime())
                ->setVisible(true);

            if (array_key_exists($i, $ClassCategories1)) {
                $ProductClass->setClassCategory1($ClassCategories1[$i]);
            }
            if (array_key_exists($i, $ClassCategories2)) {
                $ProductClass->setClassCategory2($ClassCategories2[$i]);
            }

            $this->app['orm.em']->persist($ProductClass);
            $this->app['orm.em']->flush($ProductClass);

            $ProductStock->setProductClass($ProductClass);
            $ProductStock->setProductClassId($ProductClass->getId());
            $this->app['orm.em']->flush($ProductStock);
            $Product->addProductClass($ProductClass);
        }

        // デフォルトの商品規格生成
        $ProductStock = new ProductStock();
        $ProductStock
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setCreator($Member)
            ->setStock($faker->randomNumber(3));
        $this->app['orm.em']->persist($ProductStock);
        $this->app['orm.em']->flush($ProductStock);
        $ProductClass = new ProductClass();
        if ($product_class_num > 0) {
            $ProductClass->setVisible(false);
        } else {
            $ProductClass->setVisible(true);
        }
        $ProductClass
            ->setCode($faker->word)
            ->setCreator($Member)
            ->setStock($ProductStock->getStock())
            ->setProductStock($ProductStock)
            ->setProduct($Product)
            ->setSaleType($SaleType)
            ->setPrice02($faker->randomNumber(5))
            ->setDeliveryDate($DeliveryDates[$faker->numberBetween(0, 8)])
            ->setStockUnlimited(false)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setProduct($Product);
        $this->app['orm.em']->persist($ProductClass);
        $this->app['orm.em']->flush($ProductClass);

        $ProductStock->setProductClass($ProductClass);
        $ProductStock->setProductClassId($ProductClass->getId());
        $this->app['orm.em']->flush($ProductStock);

        $Product->addProductClass($ProductClass);

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
     * @param \Eccube\Entity\Delivery $Delivery Delivery インスタンス
     * @param integer $add_charge Order に加算される手数料
     * @param integer $add_discount Order に加算される値引き額
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer, array $ProductClasses = array(), Delivery $Delivery = null, $add_charge = 0, $add_discount = 0, $statusType = null)
    {
        $faker = $this->getFaker();
        $quantity = $faker->randomNumber(2);
        $Pref = $this->app['eccube.repository.master.pref']->find($faker->numberBetween(1, 47));
        $Payments = $this->app['eccube.repository.payment']->findAll();
        if(!$statusType){
            $statusType = 'order_processing';
        }
        $OrderStatus = $this->app['eccube.repository.order_status']->find($this->app['config'][$statusType]);
        $Order = new Order($OrderStatus);
        $Order->setCustomer($Customer);
        $Order->copyProperties($Customer);
        $Order
            ->setPref($Pref)
            ->setPayment($Payments[$faker->numberBetween(0, count($Payments) - 1)])
            ->setPaymentMethod($Order->getPayment()->getMethod())
            ->setMessage($faker->realText())
            ->setNote($faker->realText());
        $this->app['orm.em']->persist($Order);
        $this->app['orm.em']->flush($Order);
        if (!is_object($Delivery)) {
            $Delivery = $this->createDelivery();
            foreach ($Payments as $Payment) {
                $PaymentOption = new PaymentOption();
                $PaymentOption
                    ->setDeliveryId($Delivery->getId())
                    ->setPaymentId($Payment->getId())
                    ->setDelivery($Delivery)
                    ->setPayment($Payment);
                $Payment->addPaymentOption($PaymentOption);
                $this->app['orm.em']->persist($PaymentOption);
                $this->app['orm.em']->flush($PaymentOption);
            }
            $this->app['orm.em']->flush($Payment);
        }
        $DeliveryFee = $this->app['eccube.repository.delivery_fee']->findOneBy(
            array(
                'Delivery' => $Delivery, 'Pref' => $Pref
            )
        );
        $fee = 0;
        if (is_object($DeliveryFee)) {
            $fee = $DeliveryFee->getFee();
        }
        $Shipping = new Shipping();
        $Shipping->copyProperties($Customer);
        $Shipping
            ->setPref($Pref)
            ->setDelivery($Delivery)
            ->setFeeId($DeliveryFee->getId())
            ->setShippingDeliveryFee($fee)
            ->setShippingDeliveryName($Delivery->getName());
        $ShippingStatus = $this->app['orm.em']->find(ShippingStatus::class, ShippingStatus::PREPARED);
        $Shipping->setShippingStatus($ShippingStatus);

        $this->app['orm.em']->persist($Shipping);
        $this->app['orm.em']->flush($Shipping);

        if (empty($ProductClasses)) {
            $Product = $this->createProduct();
            $ProductClasses = $Product->getProductClasses();
        }
        $Taxion = $this->app['orm.em']->getRepository(TaxType::class)->find(TaxType::TAXATION);
        $NonTaxable = $this->app['orm.em']->getRepository(TaxType::class)->find(TaxType::NON_TAXABLE);
        $TaxExclude = $this->app['orm.em']->getRepository(TaxDisplayType::class)->find(TaxDisplayType::EXCLUDED);
        $TaxInclude = $this->app['orm.em']->getRepository(TaxDisplayType::class)->find(TaxDisplayType::INCLUDED);
        $ItemProduct = $this->app['orm.em']->getRepository(OrderItemType::class)->find(OrderItemType::PRODUCT);
        $ItemDeliveryFee = $this->app['orm.em']->getRepository(OrderItemType::class)->find(OrderItemType::DELIVERY_FEE);
        $ItemCharge = $this->app['orm.em']->getRepository(OrderItemType::class)->find(OrderItemType::CHARGE);
        $ItemDiscount = $this->app['orm.em']->getRepository(OrderItemType::class)->find(OrderItemType::DISCOUNT);
        foreach ($ProductClasses as $ProductClass) {
            $Product = $ProductClass->getProduct();
            $TaxRule = $this->app['eccube.repository.tax_rule']->getByRule(); // デフォルト課税規則

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity)
                ->setTaxRule($TaxRule->getRoundingType()->getId())
                ->setTaxRate($TaxRule->getTaxRate())
                ->setTaxType($Taxion) // 課税
                ->setTaxDisplayType($TaxExclude) // 税別
                ->setOrderItemType($ItemProduct) // 商品明細
            ;
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
            $this->app['orm.em']->persist($OrderItem);
            $this->app['orm.em']->flush($OrderItem);
        }

        // TODO PurchaseFlow でやった方がよい
        $subTotal = array_reduce($Order->getProductOrderItems(), function ($total, $OrderItem) {
            return $total + $OrderItem->getPriceIncTax() * $OrderItem->getQuantity();
        }, 0);

        // TODO 送料無料条件は考慮していない. 必要であれば Order から再集計すること.
        $shipment_delivery_fee = $Shipping->getShippingDeliveryFee();
        $OrderItemDeliveryFee = new OrderItem();
        $OrderItemDeliveryFee->setShipping($Shipping)
            ->setOrder($Order)
            ->setProductName('送料')
            ->setPrice($shipment_delivery_fee)
            ->setQuantity(1)
            ->setTaxRate(8)
            ->setTaxType($Taxion) // 課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemDeliveryFee); // 送料明細
        $Shipping->addOrderItem($OrderItemDeliveryFee);
        $Order->addOrderItem($OrderItemDeliveryFee);
        $this->app['orm.em']->persist($OrderItemDeliveryFee);
        $this->app['orm.em']->flush($OrderItemDeliveryFee);

        $charge = $Order->getCharge() + $add_charge;
        $OrderItemCharge = new OrderItem();
        $OrderItemCharge
            // ->setShipping($Shipping) // Shipping には登録しない
            ->setOrder($Order)
            ->setProductName('手数料')
            ->setPrice($charge)
            ->setQuantity(1)
            ->setTaxRate(8)
            ->setTaxType($Taxion) // 課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemCharge); // 手数料明細
        // $Shipping->addOrderItem($OrderItemCharge); // Shipping には登録しない
        $Order->addOrderItem($OrderItemCharge);
        $this->app['orm.em']->persist($OrderItemCharge);
        $this->app['orm.em']->flush($OrderItemCharge);

        $discount = $Order->getDiscount() + $add_discount;
        $OrderItemDiscount = new OrderItem();
        $OrderItemDiscount
            // ->setShipping($Shipping) // Shipping には登録しない
            ->setOrder($Order)
            ->setProductName('値引き')
            ->setPrice($discount * -1)
            ->setQuantity(1)
            ->setTaxRate(0)
            ->setTaxType($NonTaxable) // 不課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemDiscount); // 値引き明細
        // $Shipping->addOrderItem($OrderItemDiscount); // Shipping には登録しない
        $Order->addOrderItem($OrderItemDiscount);
        $this->app['orm.em']->persist($OrderItemDiscount);
        $this->app['orm.em']->flush($OrderItemDiscount);

        $Order->setDeliveryFeeTotal($shipment_delivery_fee);
        $Order->setSubTotal($subTotal);
        $Order->setCharge($charge);
        $Order->setDiscount($discount);

        $total = $Order->getTotalPrice();
        $Order->setTotal($total);
        $Order->setPaymentTotal($total);

        // TODO PurchaseFlow でやった方がよい
        $tax = array_reduce($Order->getItems()->toArray(), function ($sum, $item) {
            return $sum += ($item->getPriceIncTax() - $item->getPrice()) * $item->getQuantity();
        }, 0);
        $Order->setTax($tax);

        $this->app['orm.em']->flush($Shipping);
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
            ->setVisible(true);
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

        $Delivery->addPaymentOption($PaymentOption);
        $this->app['orm.em']->flush($Delivery);
        return $Payment;
    }

    /**
     * 配送方法を生成する.
     *
     * @param integer $delivery_time_max_pattern 配送時間の最大パターン数
     * @return Delivery
     */
    public function createDelivery($delivery_time_max_pattern = 5)
    {
        $Member = $this->app['eccube.repository.member']->find(2);
        $SaleType = $this->app['eccube.repository.master.sale_type']->find(1);
        $faker = $this->getFaker();
        $Delivery = new Delivery();
        $Delivery
            ->setServiceName($faker->word)
            ->setName($faker->word)
            ->setDescription($faker->paragraph())
            ->setConfirmUrl($faker->url)
            ->setRank($faker->randomNumber(2))
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setCreator($Member)
            ->setSaleType($SaleType)
            ->setVisible(true);
        $this->app['orm.em']->persist($Delivery);
        $this->app['orm.em']->flush($Delivery);

        $delivery_time_patten = $faker->numberBetween(0, $delivery_time_max_pattern);
        for ($i = 0; $i < $delivery_time_patten; $i++) {
            $DeliveryTime = new DeliveryTime();
            $DeliveryTime
                ->setDelivery($Delivery)
                ->setDeliveryTime($faker->word);
            $this->app['orm.em']->persist($DeliveryTime);
            $this->app['orm.em']->flush($DeliveryTime);
            $Delivery->addDeliveryTime($DeliveryTime);
        }

        $Prefs = $this->app['eccube.repository.master.pref']->findAll();
        foreach ($Prefs as $Pref) {
            $DeliveryFee = new DeliveryFee();
            $DeliveryFee
                ->setFee($faker->randomNumber(4))
                ->setPref($Pref)
                ->setDelivery($Delivery);
            $this->app['orm.em']->persist($DeliveryFee);
            $this->app['orm.em']->flush($DeliveryFee);
            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $this->app['orm.em']->flush($Delivery);
        return $Delivery;
    }

    /**
     * ページを生成する
     *
     * @return Page
     */
    public function createPage()
    {
        $faker = $this->getFaker();
        $DeviceType = $this->app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
        /** @var Page $Page */
        $Page = $this->app['eccube.repository.page']->newPage($DeviceType);
        $Page
            ->setName($faker->word)
            ->setUrl($faker->word)
            ->setFileName($faker->word)
            ->setAuthor($faker->word)
            ->setDescription($faker->word)
            ->setKeyword($faker->word)
            ->setMetaRobots($faker->word)
        ;
        $this->app['orm.em']->persist($Page);
        $this->app['orm.em']->flush($Page);
        return $Page;
    }

    /**
     * Faker を生成する.
     *
     * @return Faker\Generator
     * @link https://github.com/fzaninotto/Faker
     */
    protected function getFaker()
    {
        return new Generator_Faker(Faker::create($this->locale));
    }
}

class Generator_Faker extends Faker
{
    private $faker;

    public function __construct(\Faker\Generator $faker)
    {
        $this->faker = $faker;
    }

    public function __get($attribute)
    {
        return $this->faker->$attribute;
    }

    public function __call($method, $attributes)
    {
        return call_user_func_array([$this->faker, $method], $attributes);
    }

    public function __isset($name)
    {
        if (isset($this->faker->$name)) {
            return true;
        }

        foreach ($this->faker->getProviders() as $provider) {
            if (method_exists($provider, $name)) {
                return true;
            }
        }

        return false;
    }
}

class Generator_FakerTest extends EccubeTestCase
{
    public function testKana01ShouldNotEmptyInJAJP()
    {
        $generator = new Generator($this->app, 'ja_JP');
        $Customer = $generator->createCustomer();
        self::assertNotEmpty($Customer->getKana01());
    }

    public function testKana01ShouldEmptyInENUS()
    {
        $generator = new Generator($this->app, 'en_US');
        $Customer = $generator->createCustomer();
        self::assertEmpty($Customer->getKana01());
    }
}
