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

namespace Eccube\Tests\Fixture;

use bheller\ImagesGenerator\ImagesGeneratorProvider;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\LoginHistoryStatus;
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
use Eccube\Entity\ProductTag;
use Eccube\Entity\Shipping;
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
use Eccube\Repository\TagRepository;
use Eccube\Repository\TaxRuleRepository;
use Eccube\Security\Core\Encoder\PasswordEncoder;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Util\StringUtil;
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
     * @var CategoryRepository
     */
    private $categoryRepository;

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
     * @var TagRepository
     */
    private $tagRepository;

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
     * @var PrefRepository
     */
    private $prefRepository;

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
        TagRepository $tagRepository,
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
        $this->tagRepository = $tagRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->orderPurchaseFlow = $orderPurchaseFlow;
        $this->session = $session;
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
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
        $this->entityManager->flush();

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
            $this->entityManager->flush();
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
     *        cats の場合は猫の画像を生成する(時間がかかる).
     *        not null の場合はダミー画像を自動生成する(GD Extension が必要).
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
        $this->entityManager->flush();

        $faker2 = Faker::create($this->locale);
        $faker2->addProvider(new ImagesGeneratorProvider($faker2));
        for ($i = 0; $i < 3; $i++) {
            $ProductImage = new ProductImage();
            if ($image_type) {
                $width = $faker->numberBetween(480, 640);
                $height = $faker->numberBetween(480, 640);
                if ($image_type == 'cats') {
                    $image = $faker->uuid.'.jpg';
                    $src = file_get_contents('https://placekitten.com/'.$width.'/'.$height);
                    file_put_contents(__DIR__.'/../../../../html/upload/save_image/'.$image, $src);
                } else {
                    $image = $faker2->imageGenerator(
                        __DIR__.'/../../../../html/upload/save_image',
                        $width,
                        $height,
                        'png', false, true, '#cccccc', '#ffffff'
                    );
                }
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
            $this->entityManager->flush();
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
            $this->entityManager->flush();
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
            $this->entityManager->flush();

            $ProductStock->setProductClass($ProductClass);
            $ProductStock->setProductClassId($ProductClass->getId());
            $this->entityManager->flush();
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
        $this->entityManager->flush();
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
        $this->entityManager->flush();

        $ProductStock->setProductClass($ProductClass);
        $ProductStock->setProductClassId($ProductClass->getId());
        $this->entityManager->flush();

        $Product->addProductClass($ProductClass);

        $Categories = $this->categoryRepository->findAll();
        foreach ($Categories as $Category) {
            $ProductCategory = new ProductCategory();
            $ProductCategory
                ->setCategory($Category)
                ->setProduct($Product)
                ->setCategoryId($Category->getId())
                ->setProductId($Product->getId());
            $this->entityManager->persist($ProductCategory);
            $this->entityManager->flush();
            $Product->addProductCategory($ProductCategory);
        }

        $Tags = $this->tagRepository->findAll();
        foreach ($Tags as $Tag) {
            $ProductTag = new ProductTag();
            $ProductTag
                ->setProduct($Product)
                ->setTag($Tag)
                ->setCreateDate(new \DateTime()) // FIXME
                ->setCreator($Member);
            $this->entityManager->persist($ProductTag);
            $this->entityManager->flush();
            $Product->addProductTag($ProductTag);
        }

        $this->entityManager->flush();

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
            ->setOrderNo($faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000000, 9999999).'-'.$faker->numberBetween(1000000, 9999999))
        ;

        $this->entityManager->persist($Order);
        $this->entityManager->flush();
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
                $this->entityManager->flush();
            }
            $this->entityManager->flush();
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
            ->setShippingDeliveryName($Delivery->getName());

        $Order->addShipping($Shipping);

        $this->entityManager->persist($Shipping);
        $this->entityManager->flush();

        if (empty($ProductClasses)) {
            $Product = $this->createProduct();
            $ProductClasses = $Product->getProductClasses();
        }
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
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
                ->setTaxType($Taxation) // 課税
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

        $OrderItemDeliveryFee = new OrderItem();
        $OrderItemDeliveryFee->setShipping($Shipping)
            ->setOrder($Order)
            ->setProductName('送料')
            ->setPrice($fee)
            ->setQuantity(1)
            ->setTaxType($Taxation) // 課税
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
            ->setTaxType($Taxation) // 課税
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
        $this->entityManager->flush();

        $PaymentOption = new PaymentOption();
        $PaymentOption
            ->setDeliveryId($Delivery->getId())
            ->setPaymentId($Payment->getId())
            ->setDelivery($Delivery)
            ->setPayment($Payment);
        $Payment->addPaymentOption($PaymentOption);

        $this->entityManager->persist($PaymentOption);
        $this->entityManager->flush();

        $Delivery->addPaymentOption($PaymentOption);
        $this->entityManager->flush();

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
        $this->entityManager->flush();

        $delivery_time_patten = $faker->numberBetween(0, $delivery_time_max_pattern);
        for ($i = 0; $i < $delivery_time_patten; $i++) {
            $DeliveryTime = new DeliveryTime();
            $DeliveryTime
                ->setDelivery($Delivery)
                ->setDeliveryTime($faker->word)
                ->setSortNo($i + 1)
                ->setVisible(true);
            $this->entityManager->persist($DeliveryTime);
            $this->entityManager->flush();
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
            $this->entityManager->flush();
            $Delivery->addDeliveryFee($DeliveryFee);
        }

        $this->entityManager->flush();

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
        /** @var Page $Page */
        $Page = $this->pageRepository->newPage();
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
        $this->entityManager->flush();

        return $Page;
    }

    /**
     * ログイン履歴を生成する
     *
     * @param string $user_name
     * @param string|null $client_ip
     * @param int|null $status
     * @param Member|null $Member
     *
     * @return LoginHistory
     */
    public function createLoginHistory($user_name, $client_ip = null, $status = null, $Member = null)
    {
        $faker = $this->getFaker();
        $LoginHistory = new LoginHistory();
        $LoginHistory
            ->setUserName($user_name)
            ->setClientIp($client_ip ?? $faker->ipv4)
            ->setLoginUser($Member);

        $LoginHistory->setStatus(
            $this->entityManager->find(LoginHistoryStatus::class, $status ?? LoginHistoryStatus::FAILURE)
        );

        $this->entityManager->persist($LoginHistory);
        $this->entityManager->flush();

        return $LoginHistory;
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
