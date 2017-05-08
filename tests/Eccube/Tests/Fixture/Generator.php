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
use Eccube\Entity\Order;
use Eccube\Entity\OrderDetail;
use Eccube\Entity\PageLayout;
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
            $email = $faker->safeEmail;
        }
        $tel = explode('-', $faker->phoneNumber);
        $fax = explode('-', $faker->phoneNumber);
        $Status = $this->app['orm.em']->getRepository('Eccube\Entity\Master\CustomerStatus')->find(CustomerStatus::ACTIVE);
        $Pref = $this->app['eccube.repository.master.pref']->find($faker->numberBetween(1, 47));
        $Sex = $this->app['eccube.repository.master.sex']->find($faker->numberBetween(1, 2));
        $Job = $this->app['orm.em']->getRepository('Eccube\Entity\Master\Job')->find($faker->numberBetween(1, 18));
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01($faker->lastKanaName)
            ->setKana02($faker->firstKanaName)
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setZip01($faker->postcode1())
            ->setZip02($faker->postcode2())
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02($tel[1])
            ->setTel03($tel[2])
            ->setFax01($fax[0])
            ->setFax02($fax[1])
            ->setFax03($fax[2])
            ->setBirth($faker->dateTimeThisDecade())
            ->setSex($Sex)
            ->setJob($Job)
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
            ->setDelFlg(Constant::DISABLED)
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01($faker->lastKanaName)
            ->setKana02($faker->firstKanaName)
            ->setCompanyName($faker->company)
            ->setZip01($faker->postcode1())
            ->setZip02($faker->postcode2())
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02($tel[1])
            ->setTel03($tel[2])
            ->setFax01($fax[0])
            ->setFax02($fax[1])
            ->setFax03($fax[2]);
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
            ->setKana01($faker->lastKanaName)
            ->setKana02($faker->firstKanaName)
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setZip01($faker->postcode1())
            ->setZip02($faker->postcode2())
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setTel01($tel[0])
            ->setTel02($tel[1])
            ->setTel03($tel[2])
            ->setFax01($fax[0])
            ->setFax02($fax[1])
            ->setFax03($fax[2])
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
        $Disp = $this->app['eccube.repository.master.disp']->find(\Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        $ProductType = $this->app['eccube.repository.master.product_type']->find(1);
        $DeliveryDates = $this->app['eccube.repository.delivery_date']->findAll();
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
                ->setProductType($ProductType)
                ->setStockUnlimited(false)
                ->setPrice02($faker->randomNumber(5))
                ->setDeliveryDate($DeliveryDates[$faker->numberBetween(0, 8)])
                ->setDelFlg(Constant::DISABLED);

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
            ->setCreator($Member)
            ->setStock($faker->randomNumber(3));
        $this->app['orm.em']->persist($ProductStock);
        $this->app['orm.em']->flush($ProductStock);
        $ProductClass = new ProductClass();
        if ($product_class_num > 0) {
            $ProductClass->setDelFlg(Constant::ENABLED);
        } else {
            $ProductClass->setDelFlg(Constant::DISABLED);
        }
        $ProductClass
            ->setCode($faker->word)
            ->setCreator($Member)
            ->setStock($ProductStock->getStock())
            ->setProductStock($ProductStock)
            ->setProduct($Product)
            ->setProductType($ProductType)
            ->setPrice02($faker->randomNumber(5))
            ->setDeliveryDate($DeliveryDates[$faker->numberBetween(0, 8)])
            ->setStockUnlimited(false)
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
            ->setMessage($faker->text())
            ->setNote($faker->text());
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
            ->setDeliveryFee($DeliveryFee)
            ->setShippingDeliveryFee($fee)
            ->setShippingDeliveryName($Delivery->getName());
        $Order->addShipping($Shipping);
        $Shipping->setOrder($Order);
        $this->app['orm.em']->persist($Shipping);
        $this->app['orm.em']->flush($Shipping);

        if (empty($ProductClasses)) {
            $Product = $this->createProduct();
            $ProductClasses = $Product->getProductClasses();
        }

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
        }

        $subTotal = $Order->calculateSubTotal();
        // TODO 送料無料条件は考慮していない. 必要であれば Order から再集計すること.
        $Order->setDeliveryFeeTotal($Shipping->getShippingDeliveryFee());
        $Order->setSubTotal($subTotal);

        $Order->setCharge($Order->getCharge() + $add_charge);
        $Order->setDiscount($Order->getDiscount() + $add_discount);

        $total = $Order->getTotalPrice();
        $Order->setTotal($total);
        $Order->setPaymentTotal($total);

        $tax = $Order->calculateTotalTax();
        $Order->setTax($tax);

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
        $ProductType = $this->app['eccube.repository.master.product_type']->find(1);
        $faker = $this->getFaker();
        $Delivery = new Delivery();
        $Delivery
            ->setServiceName($faker->word)
            ->setName($faker->word)
            ->setDescription($faker->paragraph())
            ->setConfirmUrl($faker->url)
            ->setRank($faker->randomNumber(2))
            ->setCreator($Member)
            ->setProductType($ProductType)
            ->setDelFlg(Constant::DISABLED);
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
     * @return PageLayout
     */
    public function createPageLayout()
    {
        $faker = $this->getFaker();
        $DeviceType = $this->app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
        /** @var PageLayout $PageLayout */
        $PageLayout = $this->app['eccube.repository.page_layout']->newPageLayout($DeviceType);
        $PageLayout
            ->setName($faker->word)
            ->setUrl($faker->word)
            ->setFileName($faker->word)
            ->setAuthor($faker->word)
            ->setDescription($faker->word)
            ->setKeyword($faker->word)
            ->setMetaRobots($faker->word)
            ->setMetaTags('<meta name="meta_tags_test" content="' . str_replace('\'', '', $faker->word) . '" />')
        ;
        $this->app['orm.em']->persist($PageLayout);
        $this->app['orm.em']->flush($PageLayout);
        return $PageLayout;
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
