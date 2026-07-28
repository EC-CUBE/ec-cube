#!/usr/bin/env php
<?php
/**
 * Playwright E2E テスト用フィクスチャ生成スクリプト
 *
 * Codeception の _bootstrap.php と同等のテストデータを生成する。
 * Playwright の globalSetup から child_process.execSync で呼び出す。
 *
 * Usage: php e2e/setup-fixtures.php
 */

require_once __DIR__.'/../vendor/autoload.php';

use Eccube\Entity\Customer;
use Eccube\Entity\Master\CustomerStatus;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Kernel;
use Faker\Factory as Faker;

if (file_exists(__DIR__.'/../.env')
    || file_exists(__DIR__.'/../.env.local')
    || file_exists(__DIR__.'/../.env.local.php')) {
    boot_env(__DIR__.'/../.env', true);
}

$appEnv = getenv('APP_ENV') ?: 'e2e';
$kernel = new Kernel($appEnv, false);
$kernel->boot();

$container = $kernel->getContainer();
$entityManager = $container->get('doctrine')->getManager();
$generator = $container->get(\Eccube\Tests\Fixture\Generator::class);
$faker = Faker::create('ja_JP');

// 設定
$customerNum = (int) (getenv('FIXTURE_CUSTOMER_NUM') ?: 5);
$productNum = (int) (getenv('FIXTURE_PRODUCT_NUM') ?: 23);
$orderNum = (int) (getenv('FIXTURE_ORDER_NUM') ?: 10);

echo "Creating test fixtures...\n";

// --- 会員作成 ---
$existingCustomers = (int) $entityManager->getRepository(Customer::class)
    ->createQueryBuilder('o')->select('count(o.id)')->getQuery()->getSingleScalarResult();

if ($existingCustomers < $customerNum) {
    $needed = $customerNum - $existingCustomers;
    for ($i = 0; $i < $needed; $i++) {
        $email = microtime(true).'.'.$faker->safeEmail;
        $Customer = $generator->createCustomer($email);
        $Status = $entityManager->getRepository(CustomerStatus::class)->find(CustomerStatus::REGULAR);
        $Customer->setStatus($Status);
        $entityManager->flush($Customer);
    }
    // 仮会員も1名作成
    $nonActiveCustomer = $generator->createCustomer(microtime(true).'.'.$faker->safeEmail);
    $nonActiveStatus = $entityManager->getRepository(CustomerStatus::class)->find(CustomerStatus::PROVISIONAL);
    $nonActiveCustomer->setStatus($nonActiveStatus);
    $entityManager->flush($nonActiveCustomer);
    echo "  Created ".($needed + 1)." customers\n";
} else {
    echo "  Customers already exist ({$existingCustomers})\n";
}

// --- 商品作成 ---
$existingProducts = (int) $entityManager->getRepository(\Eccube\Entity\Product::class)
    ->createQueryBuilder('o')->select('count(o.id)')->getQuery()->getSingleScalarResult();

if ($existingProducts < ($productNum + 2)) {
    for ($i = 0; $i < $productNum - 1; $i++) {
        $generator->createProduct();
    }
    // 規格なし商品
    $generator->createProduct('規格なし商品', 0);
    echo "  Created {$productNum} products\n";
} else {
    echo "  Products already exist ({$existingProducts})\n";
}

// --- 受注作成 ---
$existingOrders = (int) $entityManager->getRepository(\Eccube\Entity\Order::class)
    ->createQueryBuilder('o')->select('count(o.id)')->getQuery()->getSingleScalarResult();

if ($existingOrders < $orderNum) {
    $Customers = $entityManager->getRepository(Customer::class)->findAll();
    $Products = $entityManager->getRepository(\Eccube\Entity\Product::class)->findAll();
    $Deliveries = $entityManager->getRepository(\Eccube\Entity\Delivery::class)->findAll();
    $randomStatuses = [
        OrderStatus::NEW, OrderStatus::CANCEL, OrderStatus::IN_PROGRESS,
        OrderStatus::DELIVERED, OrderStatus::PAID, OrderStatus::PENDING,
        OrderStatus::PROCESSING, OrderStatus::RETURNED,
    ];

    $created = 0;
    foreach ($Customers as $Customer) {
        if ($created >= $orderNum) break;
        $Delivery = $Deliveries[array_rand($Deliveries)];
        $Product = $Products[array_rand($Products)];
        $Status = $entityManager->getRepository(OrderStatus::class)->find(
            $faker->randomElement($randomStatuses)
        );

        $Order = $generator->createOrder($Customer, $Product->getProductClasses()->toArray(), $Delivery);
        $Order->setOrderStatus($Status);
        $Order->setOrderDate($faker->dateTimeThisYear());
        $entityManager->flush($Order);
        $created++;
    }
    echo "  Created {$created} orders\n";
} else {
    echo "  Orders already exist ({$existingOrders})\n";
}

// --- 在庫・販売制限付き商品 ---
$stockProductName = '在庫テスト商品';
$existingStockProduct = $entityManager->getRepository(\Eccube\Entity\Product::class)
    ->findOneBy(['name' => $stockProductName]);
if (!$existingStockProduct) {
    $stockProduct = $generator->createProduct($stockProductName, 0);
    $pc = $entityManager->getRepository(\Eccube\Entity\ProductClass::class)
        ->findOneBy(['Product' => $stockProduct, 'ClassCategory1' => null]);
    if ($pc) {
        $pc->setStock(10);
        $pc->setStockUnlimited(false);
        $pc->setSaleLimit(5);
        $ps = $entityManager->getRepository(\Eccube\Entity\ProductStock::class)
            ->findOneBy(['ProductClass' => $pc]);
        if ($ps) $ps->setStock(10);
        $entityManager->flush();
    }
    echo "  Created stock-limited product: {$stockProductName}\n";
} else {
    echo "  Stock-limited product already exists\n";
}

// --- テスト用会員 (固定メールアドレス) ---
$testEmail = 'playwright@test.test';
$existing = $entityManager->getRepository(Customer::class)->findOneBy(['email' => $testEmail]);
if (!$existing) {
    $testCustomer = $generator->createCustomer($testEmail);
    $Status = $entityManager->getRepository(CustomerStatus::class)->find(CustomerStatus::REGULAR);
    $testCustomer->setStatus($Status);
    $entityManager->flush($testCustomer);
    echo "  Created test customer: $testEmail\n";
} else {
    echo "  Test customer already exists: $testEmail\n";
}

// --- 規格あり商品 (ID=1) に販売制限を設定 ---
$product1 = $entityManager->getRepository(\Eccube\Entity\Product::class)->find(1);
if ($product1) {
    $productClasses = $entityManager->getRepository(\Eccube\Entity\ProductClass::class)
        ->findBy(['Product' => $product1]);
    $updated = 0;
    foreach ($productClasses as $pc) {
        if ($pc->getSaleLimit() === null && $pc->getClassCategory1() !== null) {
            $pc->setSaleLimit(2);
            $pc->setStock(10);
            $pc->setStockUnlimited(false);
            $ps = $entityManager->getRepository(\Eccube\Entity\ProductStock::class)
                ->findOneBy(['ProductClass' => $pc]);
            if ($ps) $ps->setStock(10);
            $updated++;
        }
    }
    if ($updated > 0) {
        $entityManager->flush();
        echo "  Set sale limit on product 1: {$updated} classes\n";
    } else {
        echo "  Product 1 sale limits already set\n";
    }
}

// --- 一括削除エラーテスト用商品 ---
$bulkDeletePrefix = '一括削除エラーテスト';
$existingBulkDelete = $entityManager->getRepository(\Eccube\Entity\Product::class)
    ->findOneBy(['name' => $bulkDeletePrefix.'_受注あり_1']);
if (!$existingBulkDelete) {
    for ($i = 1; $i <= 3; $i++) {
        $generator->createProduct($bulkDeletePrefix.'_受注なし_'.$i, 0);
    }
    $bulkCustomer = $entityManager->getRepository(Customer::class)->findAll()[0];
    $Delivery = $entityManager->getRepository(\Eccube\Entity\Delivery::class)->findAll()[0];
    for ($i = 1; $i <= 2; $i++) {
        $Product = $generator->createProduct($bulkDeletePrefix.'_受注あり_'.$i, 0);
        $Order = $generator->createOrder($bulkCustomer, $Product->getProductClasses()->toArray(), $Delivery);
        $Order->setOrderStatus($entityManager->getRepository(OrderStatus::class)->find(OrderStatus::NEW));
        $Order->setOrderDate(new \DateTime());
        $entityManager->flush();
    }
    echo "  Created bulk delete error test products\n";
} else {
    echo "  Bulk delete error test products already exist\n";
}

// --- 複数カートテスト用商品 (Sale Type 2) ---
$multiCartProductName = '複数カートテスト商品';
$existingMultiCartProduct = $entityManager->getRepository(\Eccube\Entity\Product::class)
    ->findOneBy(['name' => $multiCartProductName]);
if (!$existingMultiCartProduct) {
    $SaleType2 = $entityManager->getRepository(\Eccube\Entity\Master\SaleType::class)->find(2);
    if ($SaleType2) {
        $Product = $generator->createProduct($multiCartProductName, 0);
        foreach ($Product->getProductClasses() as $pc) {
            if ($pc->isVisible()) {
                $pc->setSaleType($SaleType2);
            }
        }
        $entityManager->flush();
        echo "  Created multi-cart test product: {$multiCartProductName}\n";
    }
} else {
    echo "  Multi-cart test product already exists\n";
}

// --- 受注管理用メモ確認用の受注 (#6821) ---
// 注文確定時に商品メモが受注明細へコピーされた状態を再現し、
// 受注編集画面のメモアイコン/モーダル表示を E2E で検証できるようにする。
$orderMemoName01 = '受注メモ確認用';
$orderMemoText = "梱包時は割れ物注意\n同梱物: 取扱説明書";
$existingMemoOrder = $entityManager->getRepository(\Eccube\Entity\Order::class)
    ->findOneBy(['name01' => $orderMemoName01]);
if (!$existingMemoOrder) {
    $memoProduct = $generator->createProduct('受注管理用メモ商品', 0);
    $memoProduct->setOrderMemo($orderMemoText);
    $memoCustomer = $entityManager->getRepository(Customer::class)->findAll()[0];
    $Delivery = $entityManager->getRepository(\Eccube\Entity\Delivery::class)->findAll()[0];
    $Order = $generator->createOrder($memoCustomer, $memoProduct->getProductClasses()->toArray(), $Delivery);
    $Order->setName01($orderMemoName01);
    $Order->setName02('太郎');
    $Order->setOrderStatus($entityManager->getRepository(OrderStatus::class)->find(OrderStatus::NEW));
    $Order->setOrderDate(new \DateTime());
    // 注文確定時のコピーを再現: 商品明細にのみメモを設定する
    foreach ($Order->getProductOrderItems() as $item) {
        $item->setOrderMemo($memoProduct->getOrderMemo());
    }
    $entityManager->flush();
    echo "  Created order-memo test order\n";
} else {
    echo "  Order-memo test order already exists\n";
}

echo "Fixtures setup complete.\n";
$kernel->shutdown();
