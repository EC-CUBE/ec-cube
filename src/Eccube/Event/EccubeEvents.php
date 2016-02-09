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
    const ADMIN_ORDER_EDIT_INDEX_INITIALIZE = 'admin.order.edit.index.initialize';
    const ADMIN_ORDER_EDIT_INDEX_COMPLETE = 'admin.order.edit.index.complete';

    // searchCustomer
    const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE = 'admin.order.edit.search.customer.initialize';
    const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_SEARCH = 'admin.order.edit.search.customer.search';
    const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE = 'admin.order.edit.search.customer.complete';

    // searchCustomerById
    const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_INITIALIZE = 'admin.order.edit.search.customer.by.id.initialize';
    const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE = 'admin.order.edit.search.customer.by.id.complete';

    // searchProduct
    const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE = 'admin.order.edit.search.product.initialize';
    const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH = 'admin.order.edit.search.product.search';
    const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE = 'admin.order.edit.search.product.complete';


    /**
     * Admin/Order/MailController
     */
    // index
    const ADMIN_ORDER_MAIL_INDEX_INITIALIZE = 'admin.order.mail.index.initialize';
    const ADMIN_ORDER_MAIL_INDEX_CHANGE = 'admin.order.mail.index.change';
    const ADMIN_ORDER_MAIL_INDEX_CONFIRM = 'admin.order.mail.index.confirm';
    const ADMIN_ORDER_MAIL_INDEX_COMPLETE = 'admin.order.mail.index.complete';

    // view
    const ADMIN_ORDER_MAIL_VIEW_COMPLETE = 'admin.order.mail.view.complete';

    // mailAll
    const ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE = 'admin.order.mail.mail.all.initialize';
    const ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE = 'admin.order.mail.mail.all.change';
    const ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM = 'admin.order.mail.mail.all.confirm';
    const ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE = 'admin.order.mail.mail.all.complete';


    /**
     * Admin/Order/OrderController
     */
    // index
    const ADMIN_ORDER_INDEX_INITIALIZE = 'admin.order.index.initialize';
    const ADMIN_ORDER_INDEX_SEARCH = 'admin.order.index.search';

    // delete
    const ADMIN_ORDER_DELETE_COMPLETE = 'admin.order.delete.complete';

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
    const ADMIN_AUTHORITY_INDEX_INITIALIZE = 'admin.authority.index.initialize';
    const ADMIN_AUTHORITY_INDEX_COMPLETE = 'admin.authority.index.complete';


    /**
     * Admin/Setting/System/LogController
     */
    // index
    const ADMIN_LOG_INDEX_INITIALIZE = 'admin.log.index.initialize';
    const ADMIN_LOG_INDEX_COMPLETE = 'admin.log.index.complete';


    /**
     * Admin/Setting/System/MasterdataController
     */
    // index
    const ADMIN_MASTERDATA_INDEX_INITIALIZE = 'admin.log.index.initialize';
    const ADMIN_MASTERDATA_INDEX_FORM2_INITIALIZE = 'admin.log.index.form2.initialize';
    const ADMIN_MASTERDATA_INDEX_COMPLETE = 'admin.log.index.complete';

    // edit
    const ADMIN_MASTERDATA_EDIT_INITIALIZE = 'admin.masterdata.edit.initialize';
    const ADMIN_MASTERDATA_EDIT_FORM_INITIALIZE = 'admin.masterdata.edit.form.initialize';
    const ADMIN_MASTERDATA_EDIT_COMPLETE = 'admin.masterdata.edit.complete';


    /**
     * Admin/Setting/System/MemberController
     */
    // index
    const ADMIN_MEMBER_INDEX_INITIALIZE = 'admin.member.index.initialize';

    // edit
    const ADMIN_MEMBER_EDIT_INITIALIZE = 'admin.member.edit.initialize';
    const ADMIN_MEMBER_EDIT_COMPLETE = 'admin.member.edit.complete';

    // delete
    const ADMIN_MEMBER_DELETE_INITIALIZE = 'admin.member.delete.initialize';
    const ADMIN_MEMBER_DELETE_COMPLETE = 'admin.member.delete.complete';


    /**
     * Block/SearchProductController
     */
    // index


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


    /**
     * MailService
     */
    const MAIL_CUSTOMER_CONFIRM = 'mail.customer.confirm';
    const MAIL_CUSTOMER_COMPLETE = 'mail.customer.complete';
    const MAIL_CUSTOMER_WITHDRAW = 'mail.customer.withdraw';
    const MAIL_CONTACT = 'mail.contact';
    const MAIL_ORDER = 'mail.order';
    const MAIL_ADMIN_CUSTOMER_CONFIRM = 'mail.admin.customer.confirm';
    const MAIL_ADMIN_ORDER = 'mail.admin.order';
    const MAIL_PASSWORD_RESET = 'mail.password.reset';
    const MAIL_PASSWORD_RESET_COMPLETE = 'mail.password.reset.complete';

}