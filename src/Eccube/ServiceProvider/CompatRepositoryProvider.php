<?php

namespace Eccube\ServiceProvider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CompatRepositoryProvider implements ServiceProviderInterface
{

    public function register(Container $app)
    {
        // Repository
        $app['eccube.repository.master.authority'] = function () use ($app) {
            return $app['Eccube\Repository\Master\AuthorityRepository'];
        };
        $app['eccube.repository.master.tag'] = function () use ($app) {
            return $app['Eccube\Repository\Master\TagRepository'];
        };
        $app['eccube.repository.master.pref'] = function () use ($app) {
            return $app['Eccube\Repository\Master\PrefRepository'];
        };
        $app['eccube.repository.master.sex'] = function () use ($app) {
            return $app['Eccube\Repository\Master\SexRepository'];
        };
        $app['eccube.repository.master.disp'] = function () use ($app) {
            return $app['Eccube\Repository\Master\DispRepository'];
        };
        $app['eccube.repository.master.product_type'] = function () use ($app) {
            return $app['Eccube\Repository\Master\ProductTypeRepository'];
        };
        $app['eccube.repository.master.page_max'] = function () use ($app) {
            return $app['Eccube\Repository\Master\PageMaxRepository'];
        };
        $app['eccube.repository.master.order_status'] = function () use ($app) {
            return $app['Eccube\Repository\Master\OrderSatusRepository'];
        };
        $app['eccube.repository.master.product_list_max'] = function () use ($app) {
            return $app['Eccube\Repository\Master\ProductListMaxRepository'];
        };
        $app['eccube.repository.master.product_list_order_by'] = function () use ($app) {
            return $app['Eccube\Repository\Master\ProductListOrderByRepository'];
        };
        $app['eccube.repository.master.device_type'] = function () use ($app) {
            return $app['Eccube\Repository\Master\DeviceTypeRepository'];
        };
        $app['eccube.repository.master.csv_type'] = function () use ($app) {
            return $app['Eccube\Repository\Master\CsvTypeRepository'];
        };
        $app['eccube.repository.master.order_item_type'] = function () use ($app) {
            return $app['Eccube\Repository\Master\OrderItemTypeRepository'];
        };
        $app['eccube.repository.base_info'] = function () use ($app) {
            return $app['Eccube\Repository\BaseInfoRepository'];
        };
        $app['eccube.repository.delivery'] = function () use ($app) {
            return $app['Eccube\Repository\DeliveryRepository'];
        };
        $app['eccube.repository.delivery_date'] = function () use ($app) {
            return $app['Eccube\Repository\DeliveryDateRepository'];
        };
        $app['eccube.repository.delivery_fee'] = function () use ($app) {
            return $app['Eccube\Repository\DeliveryFeeRepository'];
        };
        $app['eccube.repository.delivery_time'] = function () use ($app) {
            return $app['Eccube\Repository\DeliveryTimeRepository'];
        };
        $app['eccube.repository.payment'] = function () use ($app) {
            return $app['Eccube\Repository\PaymentRepository'];
        };
        $app['eccube.repository.payment_option'] = function () use ($app) {
            return $app['Eccube\Repository\PaymentOptionRepository'];
        };
        $app['eccube.repository.customer'] = function () use ($app) {
            return $app['Eccube\Repository\CustomerRepository'];
        };
        $app['eccube.repository.news'] = function () use ($app) {
            return $app['Eccube\Repository\NewsRepository'];
        };
        $app['eccube.repository.mail_history'] = function () use ($app) {
            return $app['Eccube\Repository\MailHistoryRepository'];
        };
        $app['eccube.repository.member'] = function () use ($app) {
            return $app['Eccube\Repository\MemberRepository'];
        };
        $app['eccube.repository.order'] = function () use ($app) {
            return $app['Eccube\Repository\OrderRepository'];
        };
        $app['eccube.repository.product'] = function () use ($app) {
            return $app['Eccube\Repository\ProductRepository'];
        };
        $app['eccube.repository.product_image'] = function () use ($app) {
            return $app['Eccube\Repository\ProductImageRepository'];
        };
        $app['eccube.repository.product_class'] = function () use ($app) {
            return $app['Eccube\Repository\ProductClassRepository'];
        };
        $app['eccube.repository.product_stock'] = function () use ($app) {
            return $app['Eccube\Repository\ProductStockRepository'];
        };
        $app['eccube.repository.product_tag'] = function () use ($app) {
            return $app['Eccube\Repository\ProductTagRepository'];
        };
        $app['eccube.repository.class_name'] = function () use ($app) {
            return $app['Eccube\Repository\ClassNameRepository'];
        };
        $app['eccube.repository.class_category'] = function () use ($app) {
            return $app['Eccube\Repository\ClassCategoryRepository'];
        };
        $app['eccube.repository.customer_favorite_product'] = function () use ($app) {
            return $app['Eccube\Repository\CustomerFavoriteProductRepository'];
        };
        $app['eccube.repository.tax_rule'] = function () use ($app) {
            return $app['Eccube\Repository\TaxRuleRepository'];
        };
        $app['eccube.repository.page_layout'] = function () use ($app) {
            return $app['Eccube\Repository\PageLayoutRepository'];
        };
        $app['eccube.repository.block'] = function () use ($app) {
            return $app['Eccube\Repository\BlockRepository'];
        };
        $app['eccube.repository.order'] = function () use ($app) {
            return $app['Eccube\Repository\OrderRepository'];
        };
        $app['eccube.repository.customer_address'] = function () use ($app) {
            return $app['Eccube\Repository\CustomerAddressRepository'];
        };
        $app['eccube.repository.shipping'] = function () use ($app) {
            return $app['Eccube\Repository\ShippingRepository'];
        };
        $app['eccube.repository.shipment_item'] = function () use ($app) {
            return $app['Eccube\Repository\ShipmentItemRepository'];
        };
        $app['eccube.repository.master.customer_status'] = function () use ($app) {
            return $app['Eccube\Repository\Master\CustomerStatusRepository'];
        };

        $app['eccube.repository.mail_template'] = function () use ($app) {
            return $app['Eccube\Repository\MailTemplateRepository'];
        };
        $app['eccube.repository.csv'] = function () use ($app) {
            return $app['Eccube\Repository\CsvRepository'];
        };
        $app['eccube.repository.template'] = function () use ($app) {
            return $app['Eccube\Repository\TemplateRepository'];
        };
        $app['eccube.repository.authority_role'] = function () use ($app) {
            return $app['Eccube\Repository\AuthorityRoleRepository'];
        };
        $app['eccube.repository.category'] = function () use ($app) {
            return $app['Eccube\Repository\CategoryRepository'];
        };

        // alias
        $app['eccube.repository.order_status'] = function () use ($app) {
            return $app['Eccube\Repository\Master\OrderStatusRepository'];
        };
        $app['eccube.repository.customer_status'] = function () use ($app) {
            return $app['Eccube\Repository\Master\CustomerStatusRepository'];
        };
    }
}