<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Tests\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Member;
use Eccube\Entity\Order;
use Eccube\Entity\OrderItem;
use Eccube\Entity\Page;
use Eccube\Entity\Payment;
use Eccube\Entity\PaymentOption;
use Eccube\Entity\Product;
use Eccube\Entity\ProductCategory;
use Eccube\Entity\ProductClass;
use Eccube\Entity\ProductImage;
use Eccube\Entity\ProductStock;
use Eccube\Entity\Shipping;
use Eccube\Util\StringUtil;
use Eccube\Repository\CategoryRepository;
use Eccube\Repository\ClassCategoryRepository;
use Eccube\Repository\ClassNameRepository;
use Eccube\Repository\CustomerRepository;
use Eccube\Repository\DeliveryDurationRepository;
use Eccube\Repository\DeliveryFeeRepository;
use Eccube\Repository\Master\PrefRepository;
use Eccube\Repository\MemberRepository;
use Eccube\Repository\PageRepository;
use Eccube\Repository\PaymentRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Faker\Factory as Faker;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Fixture Object Generator.
 *
 * @author Kentaro Ohkouchi
 */
class Generator
{
    protected $locale;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * @var MemberRepository
     */
    protected $memberRepository;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var ClassNameRepository
     */
    protected $classNameRepository;

    /**
     * @var ClassCategoryRepository
     */
    protected $classCategoryRepository;

    /**
     * @var DeliveryDurationRepository
     */
    protected $durationRepository;

    /**
     * @var DeliveryFeeRepository
     */
    protected $deliveryFeeRepository;

    /**
     * @var PaymentRepository;
     */
    protected $paymentRepository;

    /**
     * @var TaxRuleRepository
     */
    protected $taxRuleRepository;

    /**
     * @var PageRepository
     */
    protected $pageRepository;

    /**
     * @var PrefRepository
     */
    protected $PrefRepository;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @var PurchaseFlow
     */
    protected $orderPurchaseFlow;

    public function __construct(
        EntityManagerInterface $entityManager,
        PasswordEncoder $passwordEncoder,
        MemberRepository $memberRepository,
        CategoryRepository $categoryRepository,
        CustomerRepository $customerRepository,
        ClassNameRepository $classNameRepository,
        ClassCategoryRepository $classCategoryRepository,
        DeliveryDurationRepository $durationRepository,
        DeliveryFeeRepository $deliveryFeeRepository,
        PaymentRepository $paymentRepository,
        PageRepository $pageRepository,
        PrefRepository $prefRepository,
        TaxRuleRepository $taxRuleRepository,
        PurchaseFlow $orderPurchaseFlow,
        SessionInterface $session,
        $locale = 'ja_JP'
    ) {
        $this->locale = $locale;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->memberRepository = $memberRepository;
        $this->categoryRepository = $categoryRepository;
        $this->customerRepository = $customerRepository;
        $this->classNameRepository = $classNameRepository;
        $this->classCategoryRepository = $classCategoryRepository;
        $this->durationRepository = $durationRepository;
        $this->deliveryFeeRepository = $deliveryFeeRepository;
        $this->paymentRepository = $paymentRepository;
        $this->pageRepository = $pageRepository;
        $this->prefRepository = $prefRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->orderPurchaseFlow = $orderPurchaseFlow;
        $this->session = $session;
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username. null の場合は, ランダムなユーザーIDが生成される.
     *
     * @return \Eccube\Entity\Member
     */
    public function createMember($username = null)
    {
        $faker = $this->getFaker();
        $Member = new Member();
        if (is_null($username)) {
            $username = $faker->word;
        }
        $Work = $this->entityManager->find(\Eccube\Entity\Master\Work::class, 1);
        $Authority = $this->entityManager->find(\Eccube\Entity\Master\Authority::class, 0);
        $Creator = $this->entityManager->find(\Eccube\Entity\Member::class, 2);

        $salt = bin2hex(openssl_random_pseudo_bytes(5));
        $password = 'password';
        $password = $this->passwordEncoder->encodePassword($password, $salt);

        $Member
            ->setLoginId($username)
            ->setName($username)
            ->setSalt($salt)
            ->setPassword($password)
            ->setWork($Work)
            ->setAuthority($Authority)
            ->setCreator($Creator);
        $this->memberRepository->save($Member);

        return $Member;
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     *
     * @return \Eccube\Entity\Customer
     */
    public function createCustomer($email = null)
    {
        /** @var Generator_Faker $faker */
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            $email = $faker->safeEmail;
        }
        $phoneNumber = str_replace('-', '', $faker->phoneNumber);
        $Status = $this->entityManager->find(\Eccube\Entity\Master\CustomerStatus::class, CustomerStatus::ACTIVE);
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, $faker->numberBetween(1, 47));
        $Sex = $this->entityManager->find(\Eccube\Entity\Master\Sex::class, $faker->numberBetween(1, 2));
        $Job = $this->entityManager->find(\Eccube\Entity\Master\Job::class, $faker->numberBetween(1, 18));

        $salt = $this->passwordEncoder->createSalt();
        $password = $this->passwordEncoder->encodePassword('password', $salt);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01($this->locale === 'ja_JP' ? $faker->lastKanaName : '')
            ->setKana02($this->locale === 'ja_JP' ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setPostalcode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setPhoneNumber($phoneNumber)
            ->setBirth($faker->dateTimeThisDecade())
            ->setSex($Sex)
            ->setJob($Job)
            ->setPassword($password)
            ->setSalt($salt)
            ->setSecretKey($this->customerRepository->getUniqueSecretKey())
            ->setStatus($Status)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setPoint($faker->randomNumber(5));
        $this->entityManager->persist($Customer);
        $this->entityManager->flush($Customer);

        $this->entityManager->flush($Customer);

        return $Customer;
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param boolean $is_nonmember 非会員の場合 true
     *
     * @return CustomerAddress
     */
    public function createCustomerAddress(Customer $Customer, $is_nonmember = false)
    {
        $faker = $this->getFaker();
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, $faker->numberBetween(1, 47));
        $phoneNumber = str_replace('-', '', $faker->phoneNumber);
        $CustomerAddress = new CustomerAddress();
        $CustomerAddress
            ->setCustomer($Customer)
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01($this->locale === 'ja_JP' ? $faker->lastKanaName : '')
            ->setKana02($this->locale === 'ja_JP' ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setPostalCode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setPhoneNumber($phoneNumber);
        if ($is_nonmember) {
            $Customer->addCustomerAddress($CustomerAddress);
            // TODO 外部でやった方がいい？
            $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';
            $customerAddresses = unserialize($this->session->get($sessionCustomerAddressKey));
            if (!is_array($customerAddresses)) {
                $customerAddresses = [];
            }
            $customerAddresses[] = $CustomerAddress;
            $this->session->set($sessionCustomerAddressKey, serialize($customerAddresses));
        } else {
            $this->entityManager->persist($CustomerAddress);
            $this->entityManager->flush($CustomerAddress);
        }

        return $CustomerAddress;
    }

    /**
     * 非会員の Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     *
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
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, $faker->numberBetween(1, 47));
        $phoneNumber = str_replace('-', '', $faker->phoneNumber);
        $Customer
            ->setName01($faker->lastName)
            ->setName02($faker->firstName)
            ->setKana01($this->locale === 'ja_JP' ? $faker->lastKanaName : '')
            ->setKana02($this->locale === 'ja_JP' ? $faker->firstKanaName : '')
            ->setCompanyName($faker->company)
            ->setEmail($email)
            ->setPostalCode($faker->postcode)
            ->setPref($Pref)
            ->setAddr01($faker->city)
            ->setAddr02($faker->streetAddress)
            ->setPhoneNumber($phoneNumber);

        $nonMember = [];
        $nonMember['customer'] = $Customer;
        $nonMember['pref'] = $Customer->getPref()->getId();
        $this->session->set($sessionKey, $nonMember);

        $customerAddresses = [];
        $this->session->set($sessionCustomerAddressKey, serialize($customerAddresses));

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
     *
     * @return \Eccube\Entity\Product
     */
    public function createProduct($product_name = null, $product_class_num = 3, $image_type = null)
    {
        $faker = $this->getFaker();
        $Member = $this->entityManager->find(\Eccube\Entity\Member::class, 2);
        $ProductStatus = $this->entityManager->find(\Eccube\Entity\Master\ProductStatus::class, \Eccube\Entity\Master\ProductStatus::DISPLAY_SHOW);
        $SaleType = $this->entityManager->find(\Eccube\Entity\Master\SaleType::class, 1);
        $DeliveryDurations = $this->durationRepository->findAll();

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
        $Product->extendedParameter = 'aaaa';

        $this->entityManager->persist($Product);
        $this->entityManager->flush($Product);

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
                ->setSortNo($i)
                ->setCreateDate(new \DateTime()) // FIXME
                ->setProduct($Product);
            $this->entityManager->persist($ProductImage);
            $this->entityManager->flush($ProductImage);
            $Product->addProductImage($ProductImage);
        }

        $ClassNames = $this->classNameRepository->findAll();
        $ClassName1 = $ClassNames[$faker->numberBetween(0, count($ClassNames) - 1)];
        $ClassName2 = $ClassNames[$faker->numberBetween(0, count($ClassNames) - 1)];
        // 同じ ClassName が選択された場合は ClassName1 のみ
        if ($ClassName1->getId() === $ClassName2->getId()) {
            $ClassName2 = null;
        }
        $ClassCategories1 = $this->classCategoryRepository->findBy(['ClassName' => $ClassName1]);
        $ClassCategories2 = [];
        if (is_object($ClassName2)) {
            $ClassCategories2 = $this->classCategoryRepository->findBy(['ClassName' => $ClassName2]);
        }

        for ($i = 0; $i < $product_class_num; $i++) {
            $ProductStock = new ProductStock();
            $ProductStock
                ->setCreateDate(new \DateTime()) // FIXME
                ->setUpdateDate(new \DateTime())
                ->setCreator($Member)
                ->setStock($faker->numberBetween(100, 999));
            $this->entityManager->persist($ProductStock);
            $this->entityManager->flush($ProductStock);
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
                ->setDeliveryDuration($DeliveryDurations[$faker->numberBetween(0, 8)])
                ->setCreateDate(new \DateTime()) // FIXME
                ->setUpdateDate(new \DateTime())
                ->setVisible(true);

            if (array_key_exists($i, $ClassCategories1)) {
                $ProductClass->setClassCategory1($ClassCategories1[$i]);
            }
            if (array_key_exists($i, $ClassCategories2)) {
                $ProductClass->setClassCategory2($ClassCategories2[$i]);
            }

            $this->entityManager->persist($ProductClass);
            $this->entityManager->flush($ProductClass);

            $ProductStock->setProductClass($ProductClass);
            $ProductStock->setProductClassId($ProductClass->getId());
            $this->entityManager->flush($ProductStock);
            $Product->addProductClass($ProductClass);
        }

        // デフォルトの商品規格生成
        $ProductStock = new ProductStock();
        $ProductStock
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setCreator($Member)
            ->setStock($faker->randomNumber(3));
        $this->entityManager->persist($ProductStock);
        $this->entityManager->flush($ProductStock);
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
            ->setDeliveryDuration($DeliveryDurations[$faker->numberBetween(0, 8)])
            ->setStockUnlimited(false)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setProduct($Product);
        $this->entityManager->persist($ProductClass);
        $this->entityManager->flush($ProductClass);

        $ProductStock->setProductClass($ProductClass);
        $ProductStock->setProductClassId($ProductClass->getId());
        $this->entityManager->flush($ProductStock);

        $Product->addProductClass($ProductClass);

        $Categories = $this->categoryRepository->findAll();
        $i = 0;
        foreach ($Categories as $Category) {
            $ProductCategory = new ProductCategory();
            $ProductCategory
                ->setCategory($Category)
                ->setProduct($Product)
                ->setCategoryId($Category->getId())
                ->setProductId($Product->getId())
                ->setSortNo($i);
            $this->entityManager->persist($ProductCategory);
            $this->entityManager->flush($ProductCategory);
            $Product->addProductCategory($ProductCategory);
            $i++;
        }

        $this->entityManager->flush($Product);

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
     * @param integer $statusTypeId OrderStatus:id
     *
     * @return \Eccube\Entity\Order
     */
    public function createOrder(Customer $Customer, array $ProductClasses = [], Delivery $Delivery = null, $add_charge = 0, $add_discount = 0, $statusTypeId = null)
    {
        $faker = $this->getFaker();
        $quantity = $faker->randomNumber(2);
        $Pref = $this->entityManager->find(\Eccube\Entity\Master\Pref::class, $faker->numberBetween(1, 47));
        $Payments = $this->paymentRepository->findAll();
        if ($statusTypeId === null) {
            $statusTypeId = \Eccube\Entity\Master\OrderStatus::PROCESSING;
        }
        $OrderStatus = $this->entityManager->find(\Eccube\Entity\Master\OrderStatus::class, $statusTypeId);
        $Order = new Order($OrderStatus);
        $Order->setCustomer($Customer);
        $Order->copyProperties($Customer);
        $Order
            ->setPreOrderId(sha1(StringUtil::random(32)))
            ->setPref($Pref)
            ->setPayment($Payments[$faker->numberBetween(0, count($Payments) - 1)])
            ->setPaymentMethod($Order->getPayment()->getMethod())
            ->setMessage($faker->realText())
            ->setNote($faker->realText())
            ->setAddPoint(0)    // TODO
            ->setUsePoint(0)    // TODO
            ->setOrderNo(sha1(StringUtil::random()))
        ;
        $this->entityManager->persist($Order);
        $this->entityManager->flush($Order);
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
                $this->entityManager->persist($PaymentOption);
                $this->entityManager->flush($PaymentOption);
            }
            $this->entityManager->flush($Payment);
        }
        $DeliveryFee = $this->deliveryFeeRepository->findOneBy(
            [
                'Delivery' => $Delivery, 'Pref' => $Pref,
            ]
        );
        $fee = 0;
        if (is_object($DeliveryFee)) {
            $fee = $DeliveryFee->getFee();
        }
        $Shipping = new Shipping();
        $Shipping->copyProperties($Customer);
        $Shipping
            ->setOrder($Order)
            ->setPref($Pref)
            ->setDelivery($Delivery)
            ->setFeeId($DeliveryFee->getId())
            ->setShippingDeliveryFee($fee)
            ->setShippingDeliveryName($Delivery->getName());

        $Order->addShipping($Shipping);

        $this->entityManager->persist($Shipping);
        $this->entityManager->flush($Shipping);

        if (empty($ProductClasses)) {
            $Product = $this->createProduct();
            $ProductClasses = $Product->getProductClasses();
        }
        $Taxion = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $ItemProduct = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $ItemDeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $ItemCharge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
        $ItemDiscount = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);
        /** @var ProductClass $ProductClass */
        foreach ($ProductClasses as $ProductClass) {
            if (!$ProductClass->isVisible()) {
                continue;
            }
            $Product = $ProductClass->getProduct();

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($quantity)
                ->setTaxType($Taxion) // 課税
                ->setTaxDisplayType($TaxExclude) // 税別
                ->setOrderItemType($ItemProduct) // 商品明細
            ;
            if ($ProductClass->hasClassCategory1()) {
                $OrderItem
                    ->setClassName1($ProductClass->getClassCategory1()->getClassName()->getName())
                    ->setClassCategoryName1($ProductClass->getClassCategory1()->getName())
                ;
            }
            if ($ProductClass->hasClassCategory2()) {
                $OrderItem
                    ->setClassName2($ProductClass->getClassCategory2()->getClassName()->getName())
                    ->setClassCategoryName2($ProductClass->getClassCategory2()->getName())
                ;
            }
            $Shipping->addOrderItem($OrderItem);
            $Order->addOrderItem($OrderItem);
        }

        $shipment_delivery_fee = $Shipping->getShippingDeliveryFee();
        $OrderItemDeliveryFee = new OrderItem();
        $OrderItemDeliveryFee->setShipping($Shipping)
            ->setOrder($Order)
            ->setProductName('送料')
            ->setPrice($shipment_delivery_fee)
            ->setQuantity(1)
            ->setTaxType($Taxion) // 課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemDeliveryFee); // 送料明細
        $Shipping->addOrderItem($OrderItemDeliveryFee);
        $Order->addOrderItem($OrderItemDeliveryFee);

        $charge = $Order->getCharge() + $add_charge;
        $OrderItemCharge = new OrderItem();
        $OrderItemCharge
            // ->setShipping($Shipping) // Shipping には登録しない
            ->setOrder($Order)
            ->setProductName('手数料')
            ->setPrice($charge)
            ->setQuantity(1)
            ->setTaxType($Taxion) // 課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemCharge); // 手数料明細
        // $Shipping->addOrderItem($OrderItemCharge); // Shipping には登録しない
        $Order->addOrderItem($OrderItemCharge);

        $discount = $Order->getDiscount() + $add_discount;
        $OrderItemDiscount = new OrderItem();
        $OrderItemDiscount
            // ->setShipping($Shipping) // Shipping には登録しない
            ->setOrder($Order)
            ->setProductName('値引き')
            ->setPrice($discount * -1)
            ->setQuantity(1)
            ->setTaxType($NonTaxable) // 不課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemDiscount); // 値引き明細
        // $Shipping->addOrderItem($OrderItemDiscount); // Shipping には登録しない
        $Order->addOrderItem($OrderItemDiscount);

        $this->orderPurchaseFlow->validate($Order, new PurchaseContext($Order));

        $this->entityManager->flush();

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
     *
     * @return \Eccube\Entity\Payment
     */
    public function createPayment(Delivery $Delivery, $method, $charge = 0, $rule_min = 0, $rule_max = 999999999)
    {
        $Member = $this->entityManager->find(\Eccube\Entity\Member::class, 2);
        $Payment = new Payment();
        $Payment
            ->setMethod($method)
            ->setCharge($charge)
            ->setRuleMin($rule_min)
            ->setRuleMax($rule_max)
            ->setCreator($Member)
            ->setVisible(true);
        $this->entityManager->persist($Payment);
        $this->entityManager->flush($Payment);

        $PaymentOption = new PaymentOption();
        $PaymentOption
            ->setDeliveryId($Delivery->getId())
            ->setPaymentId($Payment->getId())
            ->setDelivery($Delivery)
            ->setPayment($Payment);
        $Payment->addPaymentOption($PaymentOption);

        $this->entityManager->persist($PaymentOption);
        $this->entityManager->flush($PaymentOption);

        $Delivery->addPaymentOption($PaymentOption);
        $this->entityManager->flush($Delivery);

        return $Payment;
    }

    /**
     * 配送方法を生成する.
     *
     * @param integer $delivery_time_max_pattern 配送時間の最大パターン数
     *
     * @return Delivery
     */
    public function createDelivery($delivery_time_max_pattern = 5)
    {
        $Member = $this->entityManager->find(\Eccube\Entity\Member::class, 2);
        $SaleType = $this->entityManager->find(\Eccube\Entity\Master\SaleType::class, 1);

        $faker = $this->getFaker();
        $Delivery = new Delivery();
        $Delivery
            ->setServiceName($faker->word)
            ->setName($faker->word)
            ->setDescription($faker->paragraph())
            ->setConfirmUrl($faker->url)
            ->setSortNo($faker->randomNumber(2))
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setCreator($Member)
            ->setSaleType($SaleType)
            ->setVisible(true);
        $this->entityManager->persist($Delivery);
        $this->entityManager->flush($Delivery);

        $delivery_time_patten = $faker->numberBetween(0, $delivery_time_max_pattern);
        for ($i = 0; $i < $delivery_time_patten; $i++) {
            $DeliveryTime = new DeliveryTime();
            $DeliveryTime
                ->setDelivery($Delivery)
                ->setDeliveryTime($faker->word)
                ->setSortNo($i + 1);
            $this->entityManager->persist($DeliveryTime);
            $this->entityManager->flush($DeliveryTime);
            $Delivery->addDeliveryTime($DeliveryTime);
        }

        $Prefs = $this->prefRepository->findAll();

        foreach ($Prefs as $Pref) {
            $DeliveryFee = new DeliveryFee();
            $DeliveryFee
                ->setFee($faker->randomNumber(4))
                ->setPref($Pref)
                ->setDelivery($Delivery);
            $this->entityManager->persist($DeliveryFee);
            $this->entityManager->flush($DeliveryFee);
            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $this->entityManager->flush($Delivery);

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
        $DeviceType = $this->entityManager->find(DeviceType::class, DeviceType::DEVICE_TYPE_PC);
        /** @var Page $Page */
        $Page = $this->pageRepository->newPage($DeviceType);
        $Page
            ->setName($faker->word)
            ->setUrl($faker->word)
            ->setFileName($faker->word)
            ->setAuthor($faker->word)
            ->setDescription($faker->word)
            ->setKeyword($faker->word)
            ->setMetaRobots($faker->word)
            ->setMetaTags('<meta name="meta_tags_test" content="'.str_replace('\'', '', $faker->word).'" />')
        ;
        $this->entityManager->persist($Page);
        $this->entityManager->flush($Page);

        return $Page;
    }

    /**
     * Faker を生成する.
     *
     * @return Faker\Generator
     *
     * @see https://github.com/fzaninotto/Faker
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

// class Generator_FakerTest extends EccubeTestCase
// {
//     public function testKana01ShouldNotEmptyInJAJP()
//     {
//         $generator = new Generator($this->app, 'ja_JP');
//         $Customer = $generator->createCustomer();
//         self::assertNotEmpty($Customer->getKana01());
//     }

//     public function testKana01ShouldEmptyInENUS()
//     {
//         $generator = new Generator($this->app, 'en_US');
//         $Customer = $generator->createCustomer();
//         self::assertEmpty($Customer->getKana01());
//     }
// }
