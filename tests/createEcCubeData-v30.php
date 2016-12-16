#!/usr/local/bin/php -q
<?php
/*
 * EC-CUBE データ生成スクリプト
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 * @auther Kentaro Ohkouchi
 * @version $Id$
 */

// {{{ requires
/** 適宜、htmlディレクトリへのrequire.phpを読み込めるよう パスを書き換えて下さい */
require __DIR__.'/../autoload.php';

// }}}
// {{{ constants

/** 大カテゴリの生成数 */
define('TOP_CATEGORIES_VOLUME', 5);

/** 中カテゴリの生成数 */
define('MIDDLE_CATEGORIES_VOLUME', 2);

/** 小カテゴリの生成数 */
define('SMALL_CATEGORIES_VOLUME', 3);

/** 規格1の生成数 */
define('CLASSCATEGORY1_VOLUME', 10);

/** 規格2の生成数 */
define('CLASSCATEGORY2_VOLUME', 10);

/** 商品の生成数 */
define('PRODUCTS_VOLUME', 100);

/** flushの間隔 */
define('ENTITY_MANAGER_FLUSH_INTERVAL', 1000);

// }}}
// {{{ Logic
set_time_limit(0);
while (@ob_end_flush());

$objData = new CreateEcCubeData();
$start = microtime(true);
//$objData->objQuery->begin();

// 初期データ削除
if($objData->delete)
{
    $objData->init_delete();
}

// カテゴリ生成
$objData->createCategories();
// 規格生成
$objData->createClassData();
// 商品生成
$objData->createProducts();
// 商品と規格の関連づけ
$objData->relateClass();
// 商品とカテゴリの関連づけ
$objData->relateProductsCategories();

$end = microtime(true);
print("データの生成が完了しました!\n");
printf("所要時間 %f 秒\n", $end - $start);

// }}}
// {{{ classes

/**
 * EC-CUBE のデータを生成する
 */
class CreateEcCubeData
{
    /** アプリ */
    private $app;

    /** entity manager */
    private $en;

    /** persist実行数をカウント */
    private $persist_count = 0;

    /** 大カテゴリID の配列 */
    private $arrCategory1  = array();

    /** 中カテゴリID の配列 */
    private $arrCategory2  = array();

    /** 小カテゴリID の配列 */
    private $arrCategory3  = array();

    /** 規格名 */
    private $arrClassName = array();

    /** 商品一覧 */
    private $arrProduct = array();

    /** 規格1 */
    private $arrClassCategory1 = array();

    /** 規格2 */
    private $arrClassCategory2 = array();

    /** 削除するか */
    public $delete = false;
    
    private $arrSize = array(
        "m11(29cm)",
        "m10 1/2(28.5cm)",
        "m10(28cm)",
        "m9 1/2(27.5cm)",
        "m9(27cm)",
        "m8 1/2(26.5cm)",
        "m8(26cm)",
        '43',
        '42',
        '41',
        "43(27.0cm～27.5cm)",
        "42(26.5cm～27.0cm)",
        "37(ladies 23.5～24cm)",
        "42(約27.5cm)",
        "41(約26.5cm)",
        'W36',
        'W34',
        'W32',
        '43',
        '42',
        '41',
        'm11',
        'm10',
        "m9.5",
        'm9',
        'm8',
        'FREE',
        'XS',
        'S',
        'M',
        'L',
        'XL',
        "25-27",
        "27-29",
        'W28',
        'W29',
        'W30',
        'W31',
        'W32',
        'W33',
        'W34',
        'W35',
        'W36',
        '4',
        '6',
        '8',
        '10',
        '12',
        '10cm',
        '12cm',
        '14cm',
        '16cm',
        '18cm',
        '20cm',
        '22cm',
        '24cm',
        '26cm',
        '28cm',
        '30cm',
        '32cm',
        '34cm',
        '36cm',
        '38cm',
        '40cm',
        '10g',
        '20g',
        '30g',
        '40g',
        '50g',
        '60g',
        '70g',
        '80g',
        '90g',
        '100g',
        '110g',
        '120g',
        '130g',
        '140g',
        '150g',
        '160g',
        '170g',
        '180g',
        '190g',
        '200g',
        '8inch',
        '10inch',
        '12inch',
        '14inch',
        '16inch',
        '18inch',
        '20inch',
        '22inch',
        '24inch',
        '26inch',
        '28inch',
        '30inch',
        '32inch',
        '34inch',
        '36inch',
        '38inch',
    );

    /** 規格2 */
    private $arrColor = array(
        'white',
        'whitesmoke',
        'snow',
        'ghostwhite',
        'mintcream',
        'azure',
        'ivory',
        'floralwhite',
        'aliceblue',
        'lavenderblush',
        'seashell',
        'honeydew',
        'lightyellow',
        'oldlace',
        'cornsilk',
        'linen',
        'lemonchiffon',
        'lavender',
        'beige',
        'lightgoldenrodyellow',
        'mistyrose',
        'papayawhip',
        'antiquewhite',
        'lightcyan',
        'cyan',
        'aqua',
        'darkcyan',
        'teal',
        'darkslategray',
        'turquoise',
        'paleturquoise',
        'mediumturquoise',
        'aquamarine',
        'gainsboro',
        'lightgray',
        'silver',
        'darkgray',
        'gray',
        'dimgray',
        'black',
        'powderblue',
        'lightblue',
        'lightskyblue',
        'skyblue',
        'darkturquoise',
        'deepskyblue',
        'dodgerblue',
        'royalblue',
        'cornflowerblue',
        'cadetblue',
        'lightsteelblue',
        'steelblue',
        'lightslategray',
        'slategray',
        'blue',
        'mediumblue',
        'darkblue',
        'navy',
        'midnightblue',
        'lightsalmon',
        'darksalmon',
        'salmon',
        'tomato',
        'lightcoral',
        'coral',
        'crimson',
        'red',
        'mediumorchid',
        'mediumpurple',
        'mediumslateblue',
        'slateblue',
        'blueviolet',
        'darkviolet',
        'darkorchid',
        'darkslateblue',
        'darkorchid',
        'thistle',
        'plum',
        'violet',
        'magenta',
        'fuchsia',
        'darkmagenta',
        'purple',
        'palegreen',
        'lightgreen',
        'lime',
        'limegreen',
        'forestgreen',
        'green',
        'darkgreen',
        'greenyellow',
        'chartreuse',
        'lawngreen',
        'yellowgreen',
        'olivedrab',
        'darkolivegreen',
        'mediumaquamarine',
        'mediumspringgreen',
        'springgreen',
        'darkseagreen',
    );

    /**
     * コンストラクタ.
     */
    public function __construct()
    {
        // アプリのイニシャライズ
        $this->app = new Eccube\Application();
        $this->app->initialize();
        $this->app->initializePlugin();
        $this->app->boot();

        // Entity Manager設定
        $this->em = $this->app['orm.em'];

        // コマンドライン引数 --delete
        $arrOption = getopt('', array('delete'));
        if (isset($arrOption['delete'])) {
            $this->delete = true;
        }
    }

    /**
     * 削除処理
     */
    public function init_delete()
    {
        $this->em->getFilters()->disable('soft_delete');
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ProductStock e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の商品在庫データを削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ProductClass e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の商品と規格の関連づけを削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ProductCategory e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の商品とカテゴリの関連付けを削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ProductImage e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の商品画像を削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\Product e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の商品を削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ClassCategory e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の規格分類を削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\CategoryCount e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件のカテゴリ数を削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\CategoryTotalCount e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件のカテゴリ合計を削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\Category e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件のカテゴリを削除しました \n");
        $numDeleted  = $this->em
            ->createQuery('delete from Eccube\Entity\ClassName e')
            ->execute();
        print("初期化処理で" . $numDeleted . "件の規格を削除しました \n");
        $this->em->getFilters()->enable('soft_delete');
    }



    /**
     * カテゴリを生成する.
     *
     * 以下のように, ツリー状のカテゴリを生成する
     *
     *  大カテゴリ -- 中カテゴリ -- 小カテゴリ
     *             |             |- 小カテゴリ
     *             |             |- 小カテゴリ
     *             |
     *             |- 中カテゴリ -- 小カテゴリ
     *                            |- 小カテゴリ
     *                            |- 小カテゴリ
     * @return void
     */
    public function createCategories()
    {
        $existingMaxRank = 0;

        print("カテゴリを生成しています...\n");

        if (!$this->delete){
            $q = $this->em
                ->createQuery('SELECT MAX(c.rank) from Eccube\Entity\Category c');
            $singleResult = $q->getSingleResult();
            $existingMaxRank = $singleResult[1];
            
        }

        $count = 0;

        // 全カテゴリ共通の値
        $common_val = array();
        $common_val['creator'] = $this->app['eccube.repository.member']->find(2);
        $common_val['del_flg'] = (string) '0';

        // 大カテゴリを生成
        for ($i = 0; $i < TOP_CATEGORIES_VOLUME; $i++) {
            $Category = new \Eccube\Entity\Category();
            $Category->setCreator($common_val['creator'])
                ->setDelFlg($common_val['del_flg'])
                ->setName(sprintf("Category%d00", $i))
                ->setLevel(1)
                ->setRank($this->lfGetTotalCategoryrank($existingMaxRank) - $count);
            $this->saveEntity($Category);
            $this->arrCategory1[] = $Category;
            $count++;
            print(".");

            $top_category = $Category;
            // 中カテゴリを生成
            for ($j = 0; $j < MIDDLE_CATEGORIES_VOLUME; $j++) {
                $Category = new \Eccube\Entity\Category();
                $Category->setCreator($common_val['creator'])
                    ->setDelFlg($common_val['del_flg'])
                    ->setName(sprintf("Category%d%d0", $i,
                        $j + MIDDLE_CATEGORIES_VOLUME))
                    ->setParent($top_category)
                    ->setLevel(2)
                    ->setRank($this->lfGetTotalCategoryrank($existingMaxRank) - $count);
                $this->saveEntity($Category);
                $this->arrCategory2[] = $Category;
                $count++;
                print(".");

                $middle_category = $Category;
                // 小カテゴリを生成
                for ($k = 0; $k < SMALL_CATEGORIES_VOLUME; $k++) {
                    $Category = new \Eccube\Entity\Category();
                    $Category->setCreator($common_val['creator'])
                        ->setDelFlg($common_val['del_flg'])
                        ->setName(sprintf("Category%d%d%d",
                            $i, $j,
                            $k + SMALL_CATEGORIES_VOLUME))
                        ->setParent($middle_category)
                        ->setLevel(3)
                        ->setRank($this->lfGetTotalCategoryrank($existingMaxRank) - $count);
                    $this->saveEntity($Category);
                    $this->arrCategory3[] = $Category;
                    $count++;
                    print(".");
                }
            }
        }
        print("\n");
    }

    /**
     * 規格を生成する.
     *
     * @return void
     */
    public function createClassData()
    {
        $existingClassNameMaxRank = 0;

        // 規格データ生成
        print("規格データを生成しています...\n");

        // 既存のランク最大値を取得
        $q = $this->em
            ->createQuery('SELECT MAX (c.rank) from Eccube\Entity\ClassName c');
        $singleResult = $q->getSingleResult();
        $existingClassNameMaxRank = $singleResult[1];

        $this->createClassName('Size', $existingClassNameMaxRank + 1);
        $this->createClassName('Color', $existingClassNameMaxRank + 2);
        print("\n");

        // 規格分類データ生成
        print("規格分類データを生成しています...\n");

        $q = $this->em
            ->createQuery('SELECT MAX (c.rank) from Eccube\Entity\ClassCategory c');
        $singleResult = $q->getSingleResult();
        $existingClassCategoryMaxRank = $singleResult[1];

        // 規格1
        for ($i = 0; $i < CLASSCATEGORY1_VOLUME; $i++) {
            $this->createClassCategory($this->arrSize[$i],
                $this->arrClassName[0],
                $existingClassCategoryMaxRank + $i + 1);
        }

        // 規格2
        for ($i = 0; $i < CLASSCATEGORY2_VOLUME; $i++) {
            $this->createClassCategory($this->arrColor[$i],
                $this->arrClassName[1],
                $existingClassCategoryMaxRank + CLASSCATEGORY1_VOLUME + $i + 1);
        }
        print("\n");
    }

    /**
     * 商品と規格の関連づけを行う.
     *
     * @return void
     */
    public function relateClass()
    {

        print("商品と規格の関連づけを行います...\n");

        foreach ($this->arrProduct as $product) {
            $this->createProductsClass($product);
        }
        print("\n");
    }

    /**
     * 商品を生成する.
     *
     * @return void
     */
    public function createProducts()
    {
        // 既存のランク最大値を取得
        $q = $this->em->createQuery('SELECT MAX (c.rank) from Eccube\Entity\ProductImage c');
        $singleResult = $q->getSingleResult();
        $existingProductImageMaxRank = $singleResult[1];

        // 全プロダクト共通の値
        $common_val = array();
        $common_val['creator'] = $this->app['eccube.repository.member']->find(2);
        $common_val['del_flg'] = (string) '0';
        $common_val['status'] = $this->app['eccube.repository.master.disp']
            ->find(Eccube\Entity\Master\Disp::DISPLAY_SHOW);
        $common_val['note'] = "コメント";
        $common_val['free_area'] = "コメント";

        print("商品を生成しています...\n");

        for ($i = 0; $i < PRODUCTS_VOLUME; $i++) {
            $Product = new \Eccube\Entity\Product();
            $Product->setCreator($common_val['creator'])
                ->setDelFlg($common_val['del_flg'])
                ->setName(sprintf("商品%d", $i))
                ->setStatus($common_val['status'])
                ->setNote($common_val['note'])
                ->setFreeArea($common_val['free_area']);
            $this->saveEntity($Product);

            $ProductImage = new \Eccube\Entity\ProductImage();
            $ProductImage->setCreator($common_val['creator'])
                ->setProduct($Product)
                ->setFileName("fork-1.jpg")
                ->setRank($existingProductImageMaxRank + $i + 1);
            $this->saveEntity($ProductImage);

            $this->arrProduct[] = $Product;
            print("*");
        }
        print("\n");
    }

    /**
     * 規格を生成する.
     *
     * @param $class_name Eccube\Entity\ClassName 規格名
     * @param $rank int ランク
     * @return void
     */
    private function createClassName($class_name, $rank)
    {
        $ClassName = new \Eccube\Entity\ClassName();
        $ClassName->setCreator($this->app['eccube.repository.member']->find(2))
            ->setDelFlg((string) '0')
            ->setName($class_name)
            ->setRank($rank);
        $this->saveEntity($ClassName);

        $this->arrClassName[] = $ClassName;
        print("+");
    }

    /**
     * 規格分類を生成する.
     *
     * @param $classcategory_name string 規格分類名
     * @param $class_name \Eccube\Entity\ClassName 規格名
     * @param $rank string 規格分類のランク
     * @return void
     */
    private function createClassCategory($classcategory_name, $class_name, $rank)
    {
        $ClassCategory = new \Eccube\Entity\ClassCategory();
        $ClassCategory->setCreator($this->app['eccube.repository.member']->find(2))
            ->setDelFlg((string) '0')
            ->setName($classcategory_name)
            ->setRank($rank)
            ->setClassName($class_name)
            ->setRank($rank);
        $this->saveEntity($ClassCategory);

        switch ($class_name->getName()) {
            case 'Size':
                $this->arrClassCategory1[] = $ClassCategory;
                break;

            case 'Color':
                $this->arrClassCategory2[] = $ClassCategory;
                break;
            default:
        }
        print("+");
    }

    /**
     * 商品規格を生成する.
     *
     * @param integer Eccube\Entity\Product 商品
     * @return void
     */
    private function createProductsClass($product)
    {
        printf("商品ID %d の商品規格を生成しています...\n", $product->getId());

        // 商品規格共通の値
        $common_val = array();
        $common_val['creator'] = $this->app['eccube.repository.member']->find(2);
        $common_val['del_flg'] = (string) '0';
        $common_val['product_type'] = $this->app['eccube.repository.master.product_type']->find(1);
        $common_val['stock_unlimited'] = 1;
        $common_val['product'] = $product;
        $common_val['price01'] = 1000;
        $common_val['price02'] = 2000;

        foreach ($this->arrClassCategory1 as $classcategory1) {
            foreach ($this->arrClassCategory2 as $classcategory2) {
                $ProductClass = new \Eccube\Entity\ProductClass();
                $ProductClass->setCreator($common_val['creator'])
                    ->setDelFlg($common_val['del_flg'])
                    ->setProductType($common_val['product_type'])
                    ->setStockUnlimited($common_val['stock_unlimited'])
                    ->setProduct($common_val['product'])
                    ->setPrice01($common_val['price01'])
                    ->setPrice02($common_val['price02'])
                    ->setClassCategory1($classcategory1)
                    ->setClassCategory2($classcategory2)
                    ->setCode('CODE_'
                        . $product->getId()
                        . '_'
                        . $classcategory1->getId()
                        . '_'
                        . $classcategory2->getId());
                $this->saveEntity($ProductClass);
                print("#");
            }
        }

        // 規格無し用
        $ProductClass = new \Eccube\Entity\ProductClass();
        $ProductClass->setCreator($common_val['creator'])
            ->setDelFlg($common_val['del_flg'])
            ->setProductType($common_val['product_type'])
            ->setStockUnlimited($common_val['stock_unlimited'])
            ->setProduct($common_val['product'])
            ->setPrice01($common_val['price01'])
            ->setPrice02($common_val['price02'])
            ->setCode('CODE_'
                . $product->getId());
        $this->saveEntity($ProductClass);
        print("\n");
    }

    /**
     * 商品とカテゴリの関連づけを行う.
     *
     * @return void
     */
    public function relateProductsCategories()
    {
        print("商品とカテゴリの関連づけを行います...\n");

        print("大カテゴリ の商品カテゴリを生成しています...\n");
        $this->createProductsCategories($this->arrCategory1);
        print("中カテゴリ の商品カテゴリを生成しています...\n");
        $this->createProductsCategories($this->arrCategory2);
        print("小カテゴリ の商品カテゴリを生成しています...\n");
        $this->createProductsCategories($this->arrCategory3);
    }

    /**
     * 商品カテゴリを生成する.
     *
     * @param array $arrCategory カテゴリ の配列
     * @return void
     */
    private function createProductsCategories($arrCategory)
    {
        $count = 0;
        foreach ($arrCategory as $category) {
            foreach($this->arrProduct as $product) {
                $ProductCategory = new Eccube\Entity\ProductCategory();
                $ProductCategory->setProductId($product->getId())
                    ->setProduct($product)
                    ->setCategoryId($category->getId())
                    ->setCategory($category)
                    ->setRank($count);
                $this->saveEntity($ProductCategory);
                print("$");
            }
        }
        print("\n");
    }

    /**
     * 総カテゴリ数を計算し、dtb_categoryに代入するrankに使う
     */
    private function lfGetTotalCategoryrank($existingMaxRank = 0)
    {
        $TotalCategoryrank = (TOP_CATEGORIES_VOLUME * MIDDLE_CATEGORIES_VOLUME * SMALL_CATEGORIES_VOLUME) + (MIDDLE_CATEGORIES_VOLUME * TOP_CATEGORIES_VOLUME) + TOP_CATEGORIES_VOLUME + $existingMaxRank;

        return $TotalCategoryrank;
    }

    /**
     * EntityManagerにデータを登録
     */
    private function saveEntity($entity)
    {
        $this->em->persist($entity);
        $this->persist_count++;
        if($this->persist_count > ENTITY_MANAGER_FLUSH_INTERVAL){
            $this->em->flush();
            $this->persist_count = 0;
        }
    }
}
