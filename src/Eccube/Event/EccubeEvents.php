<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
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
 */
namespace Eccube\Event;
final class EccubeEvents
{
    /**
     * Admin/AdminContoller
     */
    // login
    const ADMIN_ADMIM_LOGIN_INITIALIZE = 'admin.admin.login.initialize';
    const ADMIN_ADMIM_LOGIN_COMPLETE = 'admin.admin.login.complete';
    // index
    // searchNonStockProducts
    /**
     * Admin/Content/BlockController
     */
    // index
    const ADMIN_BLOCK_INDEX_INITIALIZE = 'admin.block.index.initialize';
    // edit
    // delete
    /**
     * Admin/Content/FileController
     */
    // index
    // create
    // delete
    // download
    // upload
    /**
     * Admin/Content/LayoutController
     */
    // index
    /**
     * Admin/Content/NewsController
     */
    // index
    // edit
    // delete
    /**
     * Admin/Content/PageController
     */
    // index
    // edit
    // delete
    /**
     * Admin/Customer/CustomerController
     */
    // index
    // resend
    // delete
    // export
    /**
     * Admin/Customer/CustomerEditController
     */
    // index
    /**
     * Admin/Order/EditController
     */
    // index
    // searchCustomer
    // searchCustomerById
    // searchProduct
    /**
     * Admin/Order/MailController
     */
    // index
    // view
    // mailAll
    /**
     * Admin/Order/OrderController
     */
    // index
    // delete
    // exportOrder
    // exportShipping
    /**
     * Admin/Product/CategoryController
     */
    // index
    // delete
    // export
    /**
     * Admin/Product/ClassCategoryController
     */
    // index
    // delete
    /**
     * Admin/Product/ClassNameController
     */
    // index
    // delete
    /**
     * Admin/Product/CsvImportController
     */
    // csvProduct
    // csvCatgory
    // csvTemplate
    /**
     * Admin/Product/ProductClassController
     */
    // index
    // edit
    /**
     * Admin/Product/ProductController
     */
    // index
    const ADMIN_PRODUCT_INDEX_INITIALIZE = 'admin.product.index.initialize';
    const ADMIN_PRODUCT_INDEX_SEARCH = 'admin.product.index.search';
    // addImage
    // edit
    const ADMIN_PRODUCT_EDIT_INITIALIZE = 'admin.product.edit.initialize';
    const ADMIN_PRODUCT_EDIT_COMPLETE = 'admin.product.edit.complete';
    // delete
    // copy
    // display
    // export
    /**
     * Admin/Setting/Shop/CsvController
     */
    // index
    /**
     * Admin/Setting/Shop/CustomerAgreementController
     */
    // index
    /**
     * Admin/Setting/Shop/DeliveryController
     */
    // index
    // edit
    // delete
    /**
     * Admin/Setting/Shop/MailController
     */
    // index
    /**
     * Admin/Setting/Shop/PaymentController
     */
    // index
    // edit
    // imageAdd
    // delete
    /**
     * Admin/Setting/Shop/ShopController
     */
    // index
    /**
     * Admin/Setting/Shop/TaxRuleController
     */
    // index
    // delete
    // editParameter
    /**
     * Admin/Setting/Shop/TradelawController
     */
    // index
    /**
     * Admin/Setting/System/AuthorityController
     */
    // index
    /**
     * Admin/Setting/System/LogController
     */
    // index
    /**
     * Admin/Setting/System/MasterdataController
     */
    // index
    // edit
    /**
     * Admin/Setting/System/MemberController
     */
    // index
    // edit
    // delete
    /**
     * Admin/Setting/System/SecurityController
     */
    // index
    /**
     * Admin/Setting/System/SystemController
     */
    // index
    /**
     * Block/SearchProductController
     */
    // index
    const FRONT_SEARCH_PRODUCT_INDEX_INITIALIZE = 'front.search.product.index.initialize';
    const FRONT_SEARCH_PRODUCT_INDEX_COMPLETE = 'front.search.product.index.complete';
    /**
     * Mypage/ChangeController
     */
    // index
    /**
     * Mypage/DeliveryController
     */
    // edit
    // delete
    /**
     * Mypage/MypageController
     */
    // login
    // index
    // history
    // order
    // favorite
    // delete
    /**
     * Mypage/WithdrawController
     */
    // index
    /**
     * CartController
     */
    // index
    // add
    // up
    // down
    // remove
    /**
     * ContactController
     */
    // index
    /**
     * EntryController
     */
    // index
    // activate
    /**
     * ForgotController
     */
    // index
    // reset
    /**
     * ProductController
     */
    // index
    const FRONT_PRODUCT_INDEX_INITIALIZE = 'front.product.index.initialize';
    const FRONT_PRODUCT_INDEX_SEARCH = 'front.product.index.search';
    const FRONT_PRODUCT_INDEX_COMPLETE = 'front.product.index.complete';
    const FRONT_PRODUCT_INDEX_DISP = 'front.product.index.disp';
    const FRONT_PRODUCT_INDEX_ORDER = 'front.product.index.order';
    // detail
    const FRONT_PRODUCT_DETAIL_INITIALIZE = 'front.product.detail.initialize';
    const FRONT_PRODUCT_DETAIL_FAVORITE = 'front.product.detail.favorite';
    const FRONT_PRODUCT_DETAIL_COMPLETE = 'front.product.detail.complete';
    /**
     * ShoppingController
     */
    // index
    const FRONT_SHOPPING_INDEX_INITIALIZE = 'front.shopping.index.initialize';
    // confirm
    const FRONT_SHOPPING_CONFIRM_INITIALIZE = 'front.shopping.confirm.initialize';
    const FRONT_SHOPPING_CONFIRM_PROCESSING = 'front.shopping.confirm.processing';
    const FRONT_SHOPPING_CONFIRM_COMPLETE = 'front.shopping.confirm.complete';
    // complete
    const FRONT_SHOPPING_COMPLETE_INITIALIZE = 'front.shopping.complete.initialize';
    // delivery
    const FRONT_SHOPPING_DELIVERY_INITIALIZE = 'front.shopping.delivery.initialize';
    const FRONT_SHOPPING_DELIVERY_COMPLETE = 'front.shopping.delivery.complete';
    // payment
    const FRONT_SHOPPING_PAYMENT_INITIALIZE = 'front.shopping.payment.initialize';
    const FRONT_SHOPPING_PAYMENT_COMPLETE = 'front.shopping.payment.complete';
    // shippingChange
    // shipping
    const FRONT_SHOPPING_SHIPPING_COMPLETE = 'front.shopping.shipping.complete';
    // shippingEditChange
    // shippingEdit
    const FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE = 'front.shopping.shipping.edit.initialize';
    const FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE = 'front.shopping.shipping.edit.complete';
    // customer
    const FRONT_SHOPPING_CUSTOMER_INITIALIZE = 'front.shopping.customer.initialize';
    // login
    const FRONT_SHOPPING_LOGIN_INITIALIZE = 'front.shopping.login.initialize';
    // nonmember
    const FRONT_SHOPPING_NONMEMBER_INITIALIZE = 'front.shopping.nonmember.initialize';
    const FRONT_SHOPPING_NONMEMBER_COMPLETE = 'front.shopping.nonmember.complete';
    // shippingMultipleChange
    // shippingMultiple
    const FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE = 'front.shopping.shipping.multiple.initialize';
    const FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE = 'front.shopping.shipping.multiple.complete';
    // shippingMultipleEdit
    const FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE = 'front.shopping.shipping.multiple.edit.initialize';
    const FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE = 'front.shopping.shipping.multiple.edit.complete';
    // shippingError
    const FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE = 'front.shopping.shipping.error.complete';
    /**
     * UserDataController
     */
    // index
}