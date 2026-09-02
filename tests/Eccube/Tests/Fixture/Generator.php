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

use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\BaseInfo;
use Eccube\Entity\Customer;
use Eccube\Entity\CustomerAddress;
use Eccube\Entity\Delivery;
use Eccube\Entity\DeliveryFee;
use Eccube\Entity\DeliveryTime;
use Eccube\Entity\LoginHistory;
use Eccube\Entity\Master\Authority;
use Eccube\Entity\Master\Country;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\Job;
use Eccube\Entity\Master\LoginHistoryStatus;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Master\Pref;
use Eccube\Entity\Master\ProductStatus;
use Eccube\Entity\Master\SaleType;
use Eccube\Entity\Master\Sex;
use Eccube\Entity\Master\TaxDisplayType;
use Eccube\Entity\Master\TaxType;
use Eccube\Entity\Master\Work;
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
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Util\StringUtil;
use Faker\Factory;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixture Object Generator.
 *
 * @author Kentaro Ohkouchi
 */
class Generator
{
    /**
     * @var PaymentRepository;
     */
    protected $paymentRepository;

    protected ?SessionInterface $session = null;

    public function __construct(
        protected ?EntityManagerInterface $entityManager,
        protected ?UserPasswordHasherInterface $passwordHasher,
        protected ?MemberRepository $memberRepository,
        private readonly ?CategoryRepository $categoryRepository,
        protected ?CustomerRepository $customerRepository,
        protected ?ClassNameRepository $classNameRepository,
        protected ?ClassCategoryRepository $classCategoryRepository,
        protected ?DeliveryDurationRepository $durationRepository,
        protected ?DeliveryFeeRepository $deliveryFeeRepository,
        PaymentRepository $paymentRepository,
        protected ?PageRepository $pageRepository,
        private readonly ?PrefRepository $prefRepository,
        private readonly ?TagRepository $tagRepository,
        protected ?TaxRuleRepository $taxRuleRepository,
        protected ?PurchaseFlow $orderPurchaseFlow,
        protected ?RequestStack $requestStack,
        protected $locale = 'ja_JP',
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Member オブジェクトを生成して返す.
     *
     * @param string $username . null の場合は, ランダムなユーザーIDが生成される.
     */
    public function createMember(?string $username = null): Member
    {
        $faker = $this->getFaker();
        $Member = new Member();
        if (is_null($username)) {
            $username = $faker->word();
            do {
                // 無限ループが発生したため、wordではなくuuidを使用する
                $loginId = $faker->uuid();
            } while ($this->memberRepository->findBy(['login_id' => $loginId]));
        } else {
            $loginId = $username;
        }
        $Work = $this->entityManager->find(Work::class, 1);
        $Authority = $this->entityManager->find(Authority::class, 0);
        $Creator = $this->entityManager->find(Member::class, 2);

        $password = 'password';
        $password = $this->passwordHasher->hashPassword($Member, $password);

        $Member
            ->setLoginId($loginId)
            ->setName($username)
            ->setPassword($password)
            ->setWork($Work)
            ->setAuthority($Authority)
            ->setCreator($Creator)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
        $this->memberRepository->save($Member);

        return $Member;
    }

    /**
     * Customer オブジェクトを生成して返す.
     *
     * @param string $email メールアドレス. null の場合は, ランダムなメールアドレスが生成される.
     *
     * NOTE: 複数件の Customer をテストで投入したい場合は本メソッドを
     *       ループ呼び出しせず、{@link createCustomers()} (DBAL bulk INSERT)
     *       か `EccubeTestCase::loadCsvFixtures()` (CSV シナリオ) を
     *       利用すること. 1 件ごとに Faker / persist / flush が走るため
     *       数件を超えると CI 全体のボトルネックになる. 詳細は https://github.com/EC-CUBE/ec-cube/issues/6768.
     */
    public function createCustomer(?string $email = null, bool $flush = true): Customer
    {
        /** @var Generator_Faker $faker */
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            do {
                $email = $faker->safeEmail;
            } while ($this->customerRepository->findBy(['email' => $email]));
        }
        $phoneNumber = str_replace('-', '', $faker->phoneNumber);
        $Status = $this->entityManager->find(CustomerStatus::class, CustomerStatus::REGULAR);
        $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
        $Sex = $this->entityManager->find(Sex::class, $faker->numberBetween(1, 2));
        $Job = $this->entityManager->find(Job::class, $faker->numberBetween(1, 18));

        $password = $this->passwordHasher->hashPassword($Customer, 'password');
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
            ->setSecretKey($this->customerRepository->getUniqueSecretKey())
            ->setStatus($Status)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setPoint($faker->randomNumber(5));
        $this->entityManager->persist($Customer);
        if ($flush) {
            $this->entityManager->flush();
        }

        return $Customer;
    }

    /**
     * CustomerAddress を生成して返す.
     *
     * @param Customer $Customer 対象の Customer インスタンス
     * @param bool $is_nonmember 非会員の場合 true
     */
    public function createCustomerAddress(Customer $Customer, bool $is_nonmember = false): CustomerAddress
    {
        $faker = $this->getFaker();
        $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
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
            $customerAddresses = unserialize($this->requestStack->getSession()->get($sessionCustomerAddressKey), ['allowed_classes' => [CustomerAddress::class, Customer::class, Pref::class, Country::class]]);
            if (!is_array($customerAddresses)) {
                $customerAddresses = [];
            }
            $customerAddresses[] = $CustomerAddress;
            $this->requestStack->getSession()->set($sessionCustomerAddressKey, serialize($customerAddresses));
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
     */
    public function createNonMember(?string $email = null): Customer
    {
        $sessionKey = 'eccube.front.shopping.nonmember';
        $sessionCustomerAddressKey = 'eccube.front.shopping.nonmember.customeraddress';
        $faker = $this->getFaker();
        $Customer = new Customer();
        if (is_null($email)) {
            do {
                $email = $faker->safeEmail;
            } while ($this->customerRepository->findBy(['email' => $email]));
        }
        $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
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
        $this->requestStack->getSession()->set($sessionKey, $nonMember);

        $customerAddresses = [];
        $this->requestStack->getSession()->set($sessionCustomerAddressKey, serialize($customerAddresses));

        return $Customer;
    }

    /**
     * Product オブジェクトを生成して返す.
     *
     * $product_class_num = 0 とすると商品規格の無い商品を生成する.
     *
     * @param string $product_name 商品名. null の場合はランダムな文字列が生成される.
     * @param int $product_class_num 商品規格の生成数
     * @param bool $with_image 画像を生成する場合 true, 生成しない場合 false
     *
     * NOTE: 複数件の Product をテストで投入したい場合は本メソッドを
     *       ループ呼び出しせず、{@link createProducts()} (DBAL bulk INSERT,
     *       4 テーブルまとめ) か `EccubeTestCase::loadCsvFixtures()`
     *       (CSV シナリオ) を利用すること. 1 件あたり Product /
     *       ProductImage / ProductClass / ProductStock + Faker /
     *       ClassName 参照が走るため数件を超えると CI 全体のボトルネック
     *       になる. 詳細は https://github.com/EC-CUBE/ec-cube/issues/6768.
     */
    public function createProduct(?string $product_name = null, int $product_class_num = 3, bool $with_image = false, bool $flush = true, bool $simple_mode = false): Product
    {
        $faker = $this->getFaker();
        $Member = $this->entityManager->find(Member::class, 2);
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_SHOW);
        $SaleType = $this->entityManager->find(SaleType::class, 1);
        $DeliveryDurations = $this->durationRepository->findAll();
        $ProductCodesGenerated = [];

        $Product = new Product();
        $product_name ??= $faker->realText($faker->numberBetween(10, 50));
        $Product
            ->setName($product_name)
            ->setCreator($Member)
            ->setStatus($ProductStatus)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setDescriptionList($faker->paragraph())
            ->setDescriptionDetail($faker->realText());

        $this->entityManager->persist($Product);
        if ($flush) {
            $this->entityManager->flush();
        }

        Factory::create($this->locale);

        for ($i = 0; $i < 3; $i++) {
            $ProductImage = new ProductImage();
            if ($with_image) {
                $image = $faker->uuid.'.png';
                $src = __DIR__.'/../../../../html/upload/save_image/no_image_product.png';
                $dist = __DIR__.'/../../../../html/upload/save_image/'.$image;
                $fs = new Filesystem();
                $fs->copy($src, $dist);
            } else {
                $image = $faker->word().'.jpg';
            }
            $ProductImage
                ->setCreator($Member)
                ->setFileName($image)
                ->setSortNo($i)
                ->setCreateDate(new \DateTime()) // FIXME
                ->setProduct($Product);
            $this->entityManager->persist($ProductImage);
            if ($flush) {
                $this->entityManager->flush();
            }
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
            if ($flush) {
                $this->entityManager->flush();
            }
            $ProductClass = new ProductClass();
            do {
                $ProductCode = $faker->word();
            } while (in_array($ProductCode, $ProductCodesGenerated));
            $ProductCodesGenerated[] = $ProductCode;
            $ProductClass
                ->setCode($ProductCode)
                ->setCreator($Member)
                ->setStock($ProductStock->getStock())
                ->setProductStock($ProductStock)
                ->setProduct($Product)
                ->setSaleType($SaleType)
                ->setStockUnlimited(false)
                ->setPrice02((string) $faker->numberBetween(100, 10000))
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
            if ($flush) {
                $this->entityManager->flush();
            }

            $ProductStock->setProductClass($ProductClass);
            $ProductStock->setProductClassId($ProductClass->getId());
            if ($flush) {
                $this->entityManager->flush();
            }
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
        if ($flush) {
            $this->entityManager->flush();
        }
        $ProductClass = new ProductClass();
        if ($product_class_num > 0) {
            $ProductClass->setVisible(false);
        } else {
            $ProductClass->setVisible(true);
        }
        do {
            $ProductCode = $faker->word();
        } while (in_array($ProductCode, $ProductCodesGenerated));
        $ProductCodesGenerated[] = $ProductCode;
        $ProductClass
            ->setCode($ProductCode)
            ->setCreator($Member)
            ->setStock($ProductStock->getStock())
            ->setProductStock($ProductStock)
            ->setProduct($Product)
            ->setSaleType($SaleType)
            ->setPrice02((string) $faker->numberBetween(100, 10000))
            ->setDeliveryDuration($DeliveryDurations[$faker->numberBetween(0, 8)])
            ->setStockUnlimited(false)
            ->setCreateDate(new \DateTime()) // FIXME
            ->setUpdateDate(new \DateTime())
            ->setProduct($Product);
        $this->entityManager->persist($ProductClass);
        if ($flush) {
            $this->entityManager->flush();
        }

        $ProductStock->setProductClass($ProductClass);
        $ProductStock->setProductClassId($ProductClass->getId());
        if ($flush) {
            $this->entityManager->flush();
        }

        $Product->addProductClass($ProductClass);

        // simple_modeの場合はProductCategoryとProductTagをスキップ（高速化）
        if (!$simple_mode) {
            // ProductCategoryとProductTagにはProduct IDが必要なので、ここでflush
            if (!$flush) {
                $this->entityManager->flush();
            }

            $Categories = $this->categoryRepository->findAll();
            foreach ($Categories as $Category) {
                $ProductCategory = new ProductCategory();
                $ProductCategory
                    ->setCategory($Category)
                    ->setProduct($Product)
                    ->setCategoryId($Category->getId())
                    ->setProductId($Product->getId());
                $this->entityManager->persist($ProductCategory);
                if ($flush) {
                    $this->entityManager->flush();
                }
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
                if ($flush) {
                    $this->entityManager->flush();
                }
                $Product->addProductTag($ProductTag);
            }
        }

        if ($flush) {
            $this->entityManager->flush();
        }

        return $Product;
    }

    /**
     * Order オブジェクトを生成して返す.
     *
     * @param Customer $Customer Customer インスタンス
     * @param array $ProductClasses 明細行となる ProductClass の配列
     * @param Delivery $Delivery Delivery インスタンス
     * @param int $add_charge Order に加算される手数料
     * @param int $add_discount Order に加算される値引き額
     * @param int $statusTypeId OrderStatus:id
     *
     * NOTE: 複数件の Order をテストで投入したい場合は本メソッドを
     *       ループ呼び出しせず、{@link createOrders()} (DBAL bulk INSERT,
     *       Order/Shipping/OrderItem まとめ + Product/Delivery 共有) か
     *       `EccubeTestCase::loadCsvFixtures()` (CSV シナリオ) を利用
     *       すること. 1 件あたり createProduct + createDelivery +
     *       OrderItem 4 種類の persist が走るため、N 件ループは N 倍以上の
     *       コストになる (Order ループは最大の CI ボトルネックだった).
     *       詳細は https://github.com/EC-CUBE/ec-cube/issues/6768.
     */
    public function createOrder(Customer $Customer, array $ProductClasses = [], ?Delivery $Delivery = null, int $add_charge = 0, int $add_discount = 0, ?int $statusTypeId = null, bool $flush = true, bool $randomizeOrderItems = false): Order
    {
        $faker = $this->getFaker();
        $quantity = $faker->numberBetween(1, 10);
        $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
        $Payments = $this->paymentRepository->findAll();
        $statusTypeId ??= OrderStatus::PROCESSING;
        $OrderStatus = $this->entityManager->find(OrderStatus::class, $statusTypeId);
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
        if ($flush) {
            $this->entityManager->flush();
        }
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
                if ($flush) {
                    $this->entityManager->flush();
                }
            }
            if ($flush) {
                $this->entityManager->flush();
            }
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
            ->setShippingDeliveryName($Delivery->getName())
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());

        $Order->addShipping($Shipping);

        $this->entityManager->persist($Shipping);
        if ($flush) {
            $this->entityManager->flush();
        }

        if (empty($ProductClasses)) {
            // 注文生成時は高速化のためsimple_mode=trueを使用
            $Product = $this->createProduct(null, 3, false, $flush, true);
            $ProductClasses = $Product->getProductClasses()->toArray();
        }
        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $ItemProduct = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $ItemDeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $ItemCharge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
        $ItemDiscount = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);
        $ItemPoint = $this->entityManager->find(OrderItemType::class, OrderItemType::POINT);
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();

        // OrderItemを1-2個にランダム化（高速化のため、GenerateDummyDataCommandからのみ使用）
        if ($randomizeOrderItems) {
            $visibleProductClasses = array_filter($ProductClasses, fn ($pc) => $pc->isVisible());
            $numOrderItems = min($faker->numberBetween(1, 2), count($visibleProductClasses));
            $selectedProductClasses = $faker->randomElements($visibleProductClasses, $numOrderItems);
        } else {
            // visible=trueのProductClassのみ使用（デフォルト規格を除外）
            $selectedProductClasses = array_filter($ProductClasses, fn ($pc) => $pc->isVisible());
        }

        /** @var ProductClass $ProductClass */
        foreach ($selectedProductClasses as $ProductClass) {
            $Product = $ProductClass->getProduct();

            $OrderItem = new OrderItem();
            $OrderItem->setShipping($Shipping)
                ->setOrder($Order)
                ->setProductClass($ProductClass)
                ->setProduct($Product)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice((string) $ProductClass->getPrice02())
                ->setQuantity((string) $quantity)
                ->setTaxType($Taxation) // 課税
                ->setTaxDisplayType($TaxExclude) // 税別
                ->setOrderItemType($ItemProduct) // 商品明細
                ->setPointRate($BaseInfo->getBasicPointRate())
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
            ->setPrice((string) $fee)
            ->setQuantity('1')
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
            ->setPrice((string) $charge)
            ->setQuantity('1')
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
            ->setPrice((string) ($discount * -1))
            ->setQuantity('1')
            ->setTaxType($NonTaxable) // 不課税
            ->setTaxDisplayType($TaxInclude) // 税込
            ->setOrderItemType($ItemDiscount); // 値引き明細
        // $Shipping->addOrderItem($OrderItemDiscount); // Shipping には登録しない
        $Order->addOrderItem($OrderItemDiscount);

        if (($point = mt_rand(0, min($Customer->getPoint(), $Order->getPaymentTotal()))) > 0) {
            $OrderItemPoint = new OrderItem();
            $OrderItemPoint
                ->setOrder($Order)
                ->setProductName('ポイント')
                ->setPrice((string) ($point * -1))
                ->setQuantity('1')
                ->setTaxType($NonTaxable)
                ->setTaxDisplayType($TaxInclude)
                ->setOrderItemType($ItemPoint);
            $Order->addOrderItem($OrderItemPoint);
        }

        $this->orderPurchaseFlow->validate($Order, new PurchaseContext($Order));

        if ($flush) {
            $this->entityManager->flush();
        }

        return $Order;
    }

    /**
     * Payment オプジェクトを生成して返す.
     *
     * @param Delivery $Delivery デフォルトで設定する配送オブジェクト
     * @param string $method 支払い方法名称
     * @param int $charge 手数料
     * @param int $rule_min 下限金額
     * @param int $rule_max 上限金額
     */
    public function createPayment(Delivery $Delivery, string $method, int $charge = 0, int $rule_min = 0, int $rule_max = 999999999): Payment
    {
        $Member = $this->entityManager->find(Member::class, 2);
        $Payment = new Payment();
        $Payment
            ->setMethod($method)
            ->setCharge($charge)
            ->setRuleMin($rule_min)
            ->setRuleMax($rule_max)
            ->setCreator($Member)
            ->setVisible(true)
            ->setCreateDate(new \DateTime())
            ->setUpdateDate(new \DateTime());
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
     * @param int $delivery_time_max_pattern 配送時間の最大パターン数
     */
    public function createDelivery(int $delivery_time_max_pattern = 5): Delivery
    {
        $Member = $this->entityManager->find(Member::class, 2);
        $SaleType = $this->entityManager->find(SaleType::class, 1);

        $faker = $this->getFaker();
        $Delivery = new Delivery();
        $Delivery
            ->setServiceName($faker->word())
            ->setName($faker->word())
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
                ->setDeliveryTime($faker->word())
                ->setSortNo($i + 1)
                ->setVisible(true)
                ->setCreateDate(new \DateTime())
                ->setUpdateDate(new \DateTime());
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
     */
    public function createPage(): Page
    {
        $faker = $this->getFaker();
        $Page = $this->pageRepository->newPage();
        do {
            $url = $faker->word();
        } while ($this->pageRepository->findBy(['url' => $url]));
        do {
            $filename = $faker->word();
        } while ($this->pageRepository->findBy(['file_name' => $filename]));
        $Page
            ->setName($faker->word())
            ->setUrl($url)
            ->setFileName($filename)
            ->setAuthor($faker->word())
            ->setDescription($faker->word())
            ->setKeyword($faker->word())
            ->setMetaRobots($faker->word())
            ->setMetaTags('<meta name="meta_tags_test" content="'.str_replace('\'', '', $faker->word()).'" />')
        ;
        $this->entityManager->persist($Page);
        $this->entityManager->flush();

        return $Page;
    }

    /**
     * ログイン履歴を生成する
     */
    public function createLoginHistory(
        string $user_name,
        ?string $client_ip = null,
        int|LoginHistoryStatus|null $status = null,
        ?Member $Member = null): LoginHistory
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
     * 複数の Customer をまとめて高速に生成する.
     *
     * DBAL の prepared statement で bulk INSERT を行い、
     * createCustomer() のループより大幅に高速化する.
     *
     * @param int   $count   生成する件数
     * @param array $options {
     *
     *     @var Sex|null            $sex            全 Customer に設定する Sex
     *     @var CustomerStatus|null $status         全 Customer に設定する CustomerStatus (デフォルト: REGULAR)
     *     @var callable|null       $emailTemplate  function(int $i): string でメールアドレスを生成
     * }
     *
     * @return Customer[] 生成された Customer の配列
     */
    public function createCustomers(int $count, array $options = []): array
    {
        if ($count < 1) {
            return [];
        }

        $faker = $this->getFaker();
        /** @var Sex|null $Sex */
        $Sex = $options['sex'] ?? null;
        /** @var CustomerStatus $Status */
        $Status = $options['status']
            ?? $this->entityManager->find(CustomerStatus::class, CustomerStatus::REGULAR);
        $emailTemplate = $options['emailTemplate']
            ?? fn (int $i): string => sprintf('bulk-user-%d-%s@example.com', $i, $faker->uuid);

        $discriminator = $this->entityManager
            ->getClassMetadata(Customer::class)
            ->discriminatorValue;

        // bcrypt は計算コストが高いため 1 回だけ計算して使い回す
        $passwordHash = $this->passwordHasher->hashPassword(new Customer(), 'password');
        $nowStr = $this->formatDateTime(new \DateTime());

        $rows = [];
        for ($i = 0; $i < $count; $i++) {
            $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
            $rows[] = [
                'name01' => $faker->lastName,
                'name02' => $faker->firstName,
                'kana01' => $this->locale === 'ja_JP' ? $faker->lastKanaName : '',
                'kana02' => $this->locale === 'ja_JP' ? $faker->firstKanaName : '',
                'company_name' => $faker->company,
                'postal_code' => $faker->postcode,
                'addr01' => $faker->city,
                'addr02' => $faker->streetAddress,
                'email' => $emailTemplate($i),
                'phone_number' => str_replace('-', '', $faker->phoneNumber),
                'birth' => $this->formatDateTime($faker->dateTimeThisDecade()),
                'password' => $passwordHash,
                'secret_key' => $faker->uuid,
                'point' => (string) $faker->randomNumber(5),
                'create_date' => $nowStr,
                'update_date' => $nowStr,
                'customer_status_id' => $Status?->getId(),
                'sex_id' => $Sex?->getId(),
                'pref_id' => $Pref?->getId(),
                'discriminator_type' => $discriminator,
            ];
        }

        $ids = $this->bulkInsert('dtb_customer', $rows);

        return $this->customerRepository->findBy(['id' => $ids], ['id' => 'ASC']);
    }

    /**
     * 複数の Order をまとめて高速に生成する.
     *
     * Product / Delivery / PaymentOption は 1 個ずつ作って共有し、
     * Order / Shipping / OrderItem は DBAL の bulk INSERT で投入する.
     *
     * @param Customer[] $customers 各 Order に紐付ける Customer の配列
     * @param array      $options   {
     *
     *     @var OrderStatus|null $orderStatus     全 Order の OrderStatus (デフォルト: PROCESSING)
     *     @var Payment|null     $payment         全 Order の Payment (デフォルト: 1 件目の Payment)
     *     @var callable|null    $orderNoTemplate function(int $i): string で order_no を生成
     *     @var Delivery|null    $delivery        共有する Delivery (null なら createDelivery で 1 回生成)
     *     @var Product|null     $product         共有する Product (null なら createProduct で 1 回生成)
     * }
     *
     * @return Order[] 生成された Order の配列
     */
    public function createOrders(array $customers, array $options = []): array
    {
        if (empty($customers)) {
            return [];
        }

        $faker = $this->getFaker();
        $nowStr = $this->formatDateTime(new \DateTime());

        /** @var Product $Product */
        $Product = $options['product'] ?? $this->createProduct(null, 3, false, true, true);
        $ProductClasses = array_values(array_filter(
            $Product->getProductClasses()->toArray(),
            fn (ProductClass $pc): bool => $pc->isVisible()
        ));

        /** @var Delivery $Delivery */
        $Delivery = $options['delivery'] ?? $this->createDelivery();

        /** @var Payment $Payment */
        $Payment = $options['payment'] ?? $this->paymentRepository->findOneBy([], ['id' => 'ASC']);

        // Payment と Delivery の紐付け (PaymentOption) が無ければ作成
        if ($Delivery->getPaymentOptions()->isEmpty()) {
            foreach ($this->paymentRepository->findAll() as $p) {
                $PaymentOption = new PaymentOption();
                $PaymentOption
                    ->setDeliveryId($Delivery->getId())
                    ->setPaymentId($p->getId())
                    ->setDelivery($Delivery)
                    ->setPayment($p);
                $p->addPaymentOption($PaymentOption);
                $this->entityManager->persist($PaymentOption);
            }
            $this->entityManager->flush();
        }

        /** @var OrderStatus $OrderStatus */
        $OrderStatus = $options['orderStatus']
            ?? $this->entityManager->find(OrderStatus::class, OrderStatus::PROCESSING);
        $orderNoTemplate = $options['orderNoTemplate']
            ?? fn (int $i): string => $faker->numberBetween(100, 999).'-'.$faker->numberBetween(1000000, 9999999).'-'.$faker->numberBetween(1000000, 9999999);

        $Taxation = $this->entityManager->find(TaxType::class, TaxType::TAXATION);
        $NonTaxable = $this->entityManager->find(TaxType::class, TaxType::NON_TAXABLE);
        $TaxExclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $ItemProduct = $this->entityManager->find(OrderItemType::class, OrderItemType::PRODUCT);
        $ItemDeliveryFee = $this->entityManager->find(OrderItemType::class, OrderItemType::DELIVERY_FEE);
        $ItemCharge = $this->entityManager->find(OrderItemType::class, OrderItemType::CHARGE);
        $ItemDiscount = $this->entityManager->find(OrderItemType::class, OrderItemType::DISCOUNT);
        $BaseInfo = $this->entityManager->getRepository(BaseInfo::class)->get();

        $orderDiscriminator = $this->entityManager->getClassMetadata(Order::class)->discriminatorValue;
        $shippingDiscriminator = $this->entityManager->getClassMetadata(Shipping::class)->discriminatorValue;
        $orderItemDiscriminator = $this->entityManager->getClassMetadata(OrderItem::class)->discriminatorValue;

        // 1. Order を bulk INSERT
        $orderRows = [];
        $perOrder = [];
        foreach (array_values($customers) as $i => $Customer) {
            $Pref = $this->entityManager->find(Pref::class, $faker->numberBetween(1, 47));
            $DeliveryFee = $this->deliveryFeeRepository->findOneBy(['Delivery' => $Delivery, 'Pref' => $Pref]);
            $fee = is_object($DeliveryFee) ? (int) $DeliveryFee->getFee() : 0;

            $orderRows[] = [
                'pre_order_id' => sha1(StringUtil::random(32)),
                'order_no' => $orderNoTemplate($i),
                'message' => null,
                'name01' => $Customer->getName01(),
                'name02' => $Customer->getName02(),
                'kana01' => $Customer->getKana01(),
                'kana02' => $Customer->getKana02(),
                'company_name' => $Customer->getCompanyName(),
                'email' => $Customer->getEmail(),
                'phone_number' => $Customer->getPhoneNumber(),
                'postal_code' => $Customer->getPostalCode(),
                'addr01' => $Customer->getAddr01(),
                'addr02' => $Customer->getAddr02(),
                'birth' => $Customer->getBirth() !== null ? $this->formatDateTime($Customer->getBirth()) : null,
                'subtotal' => '0',
                'discount' => '0',
                'delivery_fee_total' => (string) $fee,
                'charge' => '0',
                'tax' => '0',
                'total' => (string) $fee,
                'payment_total' => (string) $fee,
                'payment_method' => $Payment->getMethod(),
                'note' => null,
                'create_date' => $nowStr,
                'update_date' => $nowStr,
                'add_point' => '0',
                'use_point' => '0',
                'customer_id' => $Customer->getId(),
                'pref_id' => $Pref?->getId(),
                'sex_id' => $Customer->getSex()?->getId(),
                'job_id' => $Customer->getJob()?->getId(),
                'payment_id' => $Payment->getId(),
                'order_status_id' => $OrderStatus->getId(),
                'discriminator_type' => $orderDiscriminator,
            ];
            $perOrder[] = ['customer' => $Customer, 'pref' => $Pref, 'fee' => $fee];
        }
        $orderIds = $this->bulkInsert('dtb_order', $orderRows);

        // 2. Shipping を bulk INSERT (各 Order に 1 件)
        $shippingRows = [];
        foreach ($orderIds as $idx => $orderId) {
            $Customer = $perOrder[$idx]['customer'];
            $Pref = $perOrder[$idx]['pref'];
            $shippingRows[] = [
                'name01' => $Customer->getName01(),
                'name02' => $Customer->getName02(),
                'kana01' => $Customer->getKana01(),
                'kana02' => $Customer->getKana02(),
                'company_name' => $Customer->getCompanyName(),
                'phone_number' => $Customer->getPhoneNumber(),
                'postal_code' => $Customer->getPostalCode(),
                'addr01' => $Customer->getAddr01(),
                'addr02' => $Customer->getAddr02(),
                'delivery_name' => $Delivery->getName(),
                'create_date' => $nowStr,
                'update_date' => $nowStr,
                'order_id' => $orderId,
                'pref_id' => $Pref?->getId(),
                'delivery_id' => $Delivery->getId(),
                'discriminator_type' => $shippingDiscriminator,
            ];
        }
        $shippingIds = $this->bulkInsert('dtb_shipping', $shippingRows);

        // 3. OrderItem を bulk INSERT (各 Order について 商品×N + 送料 + 手数料 + 値引き)
        $orderItemRows = [];
        foreach ($orderIds as $idx => $orderId) {
            $shippingId = $shippingIds[$idx];
            $fee = $perOrder[$idx]['fee'];

            $quantity = $faker->numberBetween(1, 10);
            foreach ($ProductClasses as $ProductClass) {
                $p = $ProductClass->getProduct();
                $orderItemRows[] = [
                    'product_name' => $p->getName(),
                    'product_code' => $ProductClass->getCode(),
                    'class_name1' => $ProductClass->hasClassCategory1() ? $ProductClass->getClassCategory1()->getClassName()->getName() : null,
                    'class_name2' => $ProductClass->hasClassCategory2() ? $ProductClass->getClassCategory2()->getClassName()->getName() : null,
                    'class_category_name1' => $ProductClass->hasClassCategory1() ? $ProductClass->getClassCategory1()->getName() : null,
                    'class_category_name2' => $ProductClass->hasClassCategory2() ? $ProductClass->getClassCategory2()->getName() : null,
                    'price' => (string) $ProductClass->getPrice02(),
                    'quantity' => (string) $quantity,
                    'tax' => '0',
                    'tax_rate' => '0',
                    'tax_adjust' => '0',
                    'order_id' => $orderId,
                    'product_id' => $p->getId(),
                    'product_class_id' => $ProductClass->getId(),
                    'shipping_id' => $shippingId,
                    'tax_type_id' => $Taxation->getId(),
                    'tax_display_type_id' => $TaxExclude->getId(),
                    'order_item_type_id' => $ItemProduct->getId(),
                    'point_rate' => (string) $BaseInfo->getBasicPointRate(),
                    'discriminator_type' => $orderItemDiscriminator,
                ];
            }

            $orderItemRows[] = $this->buildNonProductOrderItemRow(
                '送料', (string) $fee, $orderId, $shippingId,
                $Taxation->getId(), $TaxInclude->getId(), $ItemDeliveryFee->getId(),
                $orderItemDiscriminator,
            );
            $orderItemRows[] = $this->buildNonProductOrderItemRow(
                '手数料', '0', $orderId, null,
                $Taxation->getId(), $TaxInclude->getId(), $ItemCharge->getId(),
                $orderItemDiscriminator,
            );
            $orderItemRows[] = $this->buildNonProductOrderItemRow(
                '値引き', '0', $orderId, null,
                $NonTaxable->getId(), $TaxInclude->getId(), $ItemDiscount->getId(),
                $orderItemDiscriminator,
            );
        }
        $this->bulkInsert('dtb_order_item', $orderItemRows);

        return $this->entityManager->getRepository(Order::class)
            ->findBy(['id' => $orderIds], ['id' => 'ASC']);
    }

    /**
     * 複数の Product をまとめて高速に生成する.
     *
     * Product / ProductClass / ProductStock / ProductImage を DBAL の bulk INSERT で投入する.
     * デフォルトでは createProduct() の simple_mode=true 相当の挙動となり、
     * ProductCategory / ProductTag は生成しない (検索結果の母数を増やす目的のテストに最適).
     *
     * @param int   $count   生成する件数
     * @param array $options {
     *
     *     @var int           $productClassNum        visible な ProductClass の数 (デフォルト: 3)
     *     @var int           $imagesPerProduct       各 Product あたりの ProductImage 数 (デフォルト: 3)
     *     @var bool          $withCategoriesAndTags  全 Category / Tag を関連付ける場合 true (デフォルト: false)
     *     @var callable|null $nameTemplate           function(int $i): string で各 Product の name を生成
     * }
     *
     * @return Product[] 生成された Product の配列
     */
    public function createProducts(int $count, array $options = []): array
    {
        if ($count < 1) {
            return [];
        }

        $faker = $this->getFaker();
        $productClassNum = $options['productClassNum'] ?? 3;
        $imagesPerProduct = $options['imagesPerProduct'] ?? 3;
        $withCategoriesAndTags = $options['withCategoriesAndTags'] ?? false;
        $nameTemplate = $options['nameTemplate'] ?? null;

        $Member = $this->entityManager->find(Member::class, 2);
        $ProductStatus = $this->entityManager->find(ProductStatus::class, ProductStatus::DISPLAY_SHOW);
        $SaleType = $this->entityManager->find(SaleType::class, 1);
        $DeliveryDurations = $this->durationRepository->findAll();
        $ClassNames = $this->classNameRepository->findAll();

        $productDiscriminator = $this->entityManager->getClassMetadata(Product::class)->discriminatorValue;
        $productImageDiscriminator = $this->entityManager->getClassMetadata(ProductImage::class)->discriminatorValue;
        $productClassDiscriminator = $this->entityManager->getClassMetadata(ProductClass::class)->discriminatorValue;
        $productStockDiscriminator = $this->entityManager->getClassMetadata(ProductStock::class)->discriminatorValue;
        $nowStr = $this->formatDateTime(new \DateTime());

        // 1. Product を bulk INSERT
        $productRows = [];
        for ($i = 0; $i < $count; $i++) {
            $productRows[] = [
                'name' => $nameTemplate ? $nameTemplate($i) : $faker->realText($faker->numberBetween(10, 50)),
                'note' => null,
                'description_list' => $faker->paragraph(),
                'description_detail' => $faker->realText(),
                'search_word' => null,
                'free_area' => null,
                'create_date' => $nowStr,
                'update_date' => $nowStr,
                'creator_id' => $Member?->getId(),
                'product_status_id' => $ProductStatus?->getId(),
                'discriminator_type' => $productDiscriminator,
            ];
        }
        $productIds = $this->bulkInsert('dtb_product', $productRows);

        // 2. ProductImage を bulk INSERT (各 Product × N)
        if ($imagesPerProduct > 0) {
            $imageRows = [];
            foreach ($productIds as $productId) {
                for ($j = 0; $j < $imagesPerProduct; $j++) {
                    $imageRows[] = [
                        'file_name' => $faker->word().'.jpg',
                        'sort_no' => $j,
                        'create_date' => $nowStr,
                        'product_id' => $productId,
                        'creator_id' => $Member?->getId(),
                        'discriminator_type' => $productImageDiscriminator,
                    ];
                }
            }
            $this->bulkInsert('dtb_product_image', $imageRows);
        }

        // 3. ProductClass を bulk INSERT
        // 各 Product あたり (productClassNum + 1) 行: visible × productClassNum + デフォルト 1 (productClassNum>0 なら invisible)
        $classRows = [];
        $productCodesUsed = [];
        foreach ($productIds as $productId) {
            $ClassName1 = $ClassNames[$faker->numberBetween(0, count($ClassNames) - 1)];
            $ClassCategories1 = $this->classCategoryRepository->findBy(['ClassName' => $ClassName1]);

            for ($j = 0; $j < $productClassNum; $j++) {
                do {
                    $code = $faker->word();
                } while (in_array($code, $productCodesUsed, true));
                $productCodesUsed[] = $code;
                $cc1Id = array_key_exists($j, $ClassCategories1) ? $ClassCategories1[$j]->getId() : null;
                $classRows[] = $this->buildProductClassRow(
                    $code, (string) $faker->numberBetween(100, 999), 1,
                    (string) $faker->numberBetween(100, 10000), $productId,
                    $SaleType?->getId(), $cc1Id, null,
                    $DeliveryDurations[$faker->numberBetween(0, max(0, count($DeliveryDurations) - 1))]?->getId(),
                    $Member?->getId(), $nowStr, $productClassDiscriminator,
                );
            }
            // デフォルト規格
            do {
                $code = $faker->word();
            } while (in_array($code, $productCodesUsed, true));
            $productCodesUsed[] = $code;
            $classRows[] = $this->buildProductClassRow(
                $code, (string) $faker->randomNumber(3), $productClassNum > 0 ? 0 : 1,
                (string) $faker->numberBetween(100, 10000), $productId,
                $SaleType?->getId(), null, null,
                $DeliveryDurations[$faker->numberBetween(0, max(0, count($DeliveryDurations) - 1))]?->getId(),
                $Member?->getId(), $nowStr, $productClassDiscriminator,
            );
        }
        $classIds = $this->bulkInsert('dtb_product_class', $classRows);

        // 4. ProductStock を bulk INSERT (各 ProductClass に対して 1 件)
        $stockRows = [];
        foreach ($classIds as $classId) {
            $stockRows[] = [
                'stock' => (string) $faker->numberBetween(100, 999),
                'create_date' => $nowStr,
                'update_date' => $nowStr,
                'product_class_id' => $classId,
                'creator_id' => $Member?->getId(),
                'discriminator_type' => $productStockDiscriminator,
            ];
        }
        $this->bulkInsert('dtb_product_stock', $stockRows);

        // 5. オプション: Category / Tag を関連付け
        if ($withCategoriesAndTags) {
            $Categories = $this->categoryRepository->findAll();
            $Tags = $this->tagRepository->findAll();
            $productCategoryDiscriminator = $this->entityManager->getClassMetadata(ProductCategory::class)->discriminatorValue;
            $productTagDiscriminator = $this->entityManager->getClassMetadata(ProductTag::class)->discriminatorValue;

            $catRows = [];
            foreach ($productIds as $productId) {
                foreach ($Categories as $Category) {
                    $catRows[] = [
                        'category_id' => $Category->getId(),
                        'product_id' => $productId,
                        'discriminator_type' => $productCategoryDiscriminator,
                    ];
                }
            }
            if (!empty($catRows)) {
                $this->bulkInsert('dtb_product_category', $catRows);
            }

            $tagRows = [];
            foreach ($productIds as $productId) {
                foreach ($Tags as $Tag) {
                    $tagRows[] = [
                        'product_id' => $productId,
                        'tag_id' => $Tag->getId(),
                        'create_date' => $nowStr,
                        'creator_id' => $Member?->getId(),
                        'discriminator_type' => $productTagDiscriminator,
                    ];
                }
            }
            if (!empty($tagRows)) {
                $this->bulkInsert('dtb_product_tag', $tagRows);
            }
        }

        return $this->entityManager->getRepository(Product::class)
            ->findBy(['id' => $productIds], ['id' => 'ASC']);
    }

    /**
     * dtb_product_class への INSERT 用 1 行を構築する.
     */
    private function buildProductClassRow(
        string $productCode,
        string $stock,
        int $visible,
        string $price02,
        int $productId,
        ?int $saleTypeId,
        ?int $classCategoryId1,
        ?int $classCategoryId2,
        ?int $deliveryDurationId,
        ?int $creatorId,
        string $nowStr,
        string $discriminator,
    ): array {
        return [
            'product_code' => $productCode,
            'stock' => $stock,
            'stock_unlimited' => 0,
            'sale_limit' => null,
            'price01' => null,
            'price02' => $price02,
            'delivery_fee' => null,
            'visible' => $visible,
            'create_date' => $nowStr,
            'update_date' => $nowStr,
            'currency_code' => null,
            'point_rate' => null,
            'product_id' => $productId,
            'sale_type_id' => $saleTypeId,
            'class_category_id1' => $classCategoryId1,
            'class_category_id2' => $classCategoryId2,
            'delivery_duration_id' => $deliveryDurationId,
            'creator_id' => $creatorId,
            'discriminator_type' => $discriminator,
        ];
    }

    /**
     * 商品以外 (送料 / 手数料 / 値引き) の OrderItem 用行を生成する.
     */
    private function buildNonProductOrderItemRow(
        string $productName,
        string $price,
        int $orderId,
        ?int $shippingId,
        int $taxTypeId,
        int $taxDisplayTypeId,
        int $orderItemTypeId,
        string $discriminator,
    ): array {
        return [
            'product_name' => $productName,
            'product_code' => null,
            'class_name1' => null,
            'class_name2' => null,
            'class_category_name1' => null,
            'class_category_name2' => null,
            'price' => $price,
            'quantity' => '1',
            'tax' => '0',
            'tax_rate' => '0',
            'tax_adjust' => '0',
            'order_id' => $orderId,
            'product_id' => null,
            'product_class_id' => null,
            'shipping_id' => $shippingId,
            'tax_type_id' => $taxTypeId,
            'tax_display_type_id' => $taxDisplayTypeId,
            'order_item_type_id' => $orderItemTypeId,
            'point_rate' => null,
            'discriminator_type' => $discriminator,
        ];
    }

    /**
     * UTCDateTimeTzType が DB から読み戻せるフォーマットで datetime 値を生成する.
     *
     * UTC に変換してから接続中のプラットフォームの DateTimeTzFormatString
     * (SQLite/MySQL: "Y-m-d H:i:s", PostgreSQL: "Y-m-d H:i:sO") で整形する.
     */
    private function formatDateTime(\DateTimeInterface $dt): string
    {
        $dateFormat = $this->entityManager->getConnection()
            ->getDatabasePlatform()
            ->getDateTimeTzFormatString();

        return \DateTimeImmutable::createFromInterface($dt)
            ->setTimezone(new \DateTimeZone('UTC'))
            ->format($dateFormat);
    }

    /**
     * 連想配列の行を DBAL の prepared statement で順次 INSERT する.
     *
     * 既存トランザクション内 (DAMA DoctrineTestBundle のテストなど) では
     * 新たにトランザクションを開始しない.
     *
     * @param string                      $tableName 対象テーブル名
     * @param array<int, array<string, mixed>> $rows  すべて同じキー集合を持つ連想配列の配列
     *
     * @return int[] 各 INSERT で採番された ID の配列
     */
    private function bulkInsert(string $tableName, array $rows): array
    {
        if (empty($rows)) {
            return [];
        }

        $conn = $this->entityManager->getConnection();
        $platform = $conn->getDatabasePlatform();

        $columns = array_keys($rows[0]);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $tableName,
            implode(', ', $columns),
            implode(', ', array_fill(0, count($columns), '?')),
        );

        $startedTransaction = false;
        if (!$conn->isTransactionActive()) {
            $conn->beginTransaction();
            $startedTransaction = true;
        }

        if ($platform instanceof AbstractMySQLPlatform) {
            $conn->executeStatement("SET SESSION sql_mode='NO_AUTO_VALUE_ON_ZERO'");
        }

        $stmt = $conn->prepare($sql);
        $ids = [];
        foreach ($rows as $row) {
            $idx = 1;
            foreach ($row as $value) {
                $stmt->bindValue($idx++, $value);
            }
            $stmt->executeStatement();
            // DBAL 4 では PDO mysql の lastInsertId() が '0' を返すと NoIdentityValue 例外になる
            // (native prepared statement 経由では AUTO_INCREMENT 値が PDO::lastInsertId() に
            //  反映されないことがある)。mysql では SELECT LAST_INSERT_ID() で確実に取得する。
            if ($platform instanceof AbstractMySQLPlatform) {
                $ids[] = (int) $conn->fetchOne('SELECT LAST_INSERT_ID()');
            } else {
                $ids[] = (int) $conn->lastInsertId();
            }
        }

        if ($startedTransaction) {
            $conn->commit();
        }

        return $ids;
    }

    /**
     * Faker を生成する.
     */
    protected function getFaker(): \Faker\Generator
    {
        return Factory::create($this->locale);
    }
}
