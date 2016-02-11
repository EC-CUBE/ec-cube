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

    // index
    const ADMIN_ADMIM_INDEX_INITIALIZE = 'admin.admin.index.initialize';
    const ADMIN_ADMIM_INDEX_ORDER = 'admin.admin.index.order';
    const ADMIN_ADMIM_INDEX_SALES = 'admin.admin.index.sales';
    const ADMIN_ADMIM_INDEX_COMPLETE = 'admin.admin.index.complete';

    // searchNonStockProducts


    /**
     * Admin/Content/BlockController
     */
    // index
    const ADMIN_CONTENT_BLOCK_INDEX_COMPLETE = 'admin.content.block.index.complete';

    // edit
    const ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE = 'admin.content.block.edit.initialize';
    const ADMIN_CONTENT_BLOCK_EDIT_COMPLETE = 'admin.content.block.edit.complete';

    // delete
    const ADMIN_CONTENT_BLOCK_DELETE_COMPLETE = 'admin.content.block.delete.complete';

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
    const ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE = 'admin.content.layout.index.initialize';
    const ADMIN_CONTENT_LAYOUT_INDEX_COMPLETE = 'admin.content.layout.index.complete';


    /**
     * Admin/Content/NewsController
     */
    // index
    const ADMIN_CONTENT_NEWS_INDEX_INITIALIZE = 'admin.content.news.index.initialize';
    const ADMIN_CONTENT_NEWS_INDEX_COMPLETE = 'admin.content.news.index.complete';

    // edit
    const ADMIN_CONTENT_NEWS_EDIT_INITIALIZE = 'admin.content.news.edit.initialize';
    const ADMIN_CONTENT_NEWS_EDIT_COMPLETE = 'admin.content.news.edit.complete';

    // delete
    const ADMIN_CONTENT_NEWS_DELETE_COMPLETE = 'admin.content.news.delete.complete';


    /**
     * Admin/Content/PageController
     */
    // index
    const ADMIN_CONTENT_PAGE_INDEX_COMPLETE = 'admin.content.page.index.initialize';

    // edit
    const ADMIN_CONTENT_PAGE_EDIT_INITIALIZE = 'admin.content.page.edit.initialize';
    const ADMIN_CONTENT_PAGE_EDIT_COMPLETE = 'admin.content.page.edit.complete';

    // delete
    const ADMIN_CONTENT_PAGE_DELETE_COMPLETE = 'admin.content.page.delete.complete';

    /**
     * Admin/Customer/CustomerController
     */
    // index
    const ADMIN_CUSTOMER_INDEX_INITIALIZE = 'admin.customer.index.initialize';
    const ADMIN_CUSTOMER_INDEX_SEARCH = 'admin.customer.index.search';

    // resend
    const ADMIN_CUSTOMER_RESEND_COMPLETE = 'admin.customer.resend.complete';

    // delete
    const ADMIN_CUSTOMER_DELETE_COMPLETE = 'admin.customer.delete.complete';

    // export


    /**
     * Admin/Customer/CustomerEditController
     */
    // index
    const ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE = 'admin.customer.edit.index.initialize';
    const ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE = 'admin.customer.edit.index.complete';


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
    const ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE = 'admin.product.category.index.initialize';
    const ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE = 'admin.product.category.index.complete';

    // delete
    const ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE = 'admin.product.category.delete.complete';

    // export


    /**
     * Admin/Product/ClassCategoryController
     */
    // index
    const ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_INITIALIZE = 'admin.product.class.category.index.initialize';
    const ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_COMPLETE = 'admin.product.class.category.index.complete';

    // delete
    const ADMIN_PRODUCT_CLASS_CATEGORY_DELETE_COMPLETE = 'admin.product.class.category.delete.complete';


    /**
     * Admin/Product/ClassNameController
     */
    // index
    const ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE = 'admin.product.class.name.index.initialize';
    const ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE = 'admin.product.class.name.index.complete';

    // delete
    const ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE = 'admin.product.class.name.delete.complete';


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
    const ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_INITIALIZE = 'admin.product.product.class.index.initialize';
    const ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_CLASSES = 'admin.product.product.class.index.classes';

    // edit
    const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_INITIALIZE = 'admin.product.product.class.edit.initialize';
    const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_COMPLETE = 'admin.product.product.class.edit.complete';
    const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_UPDATE = 'admin.product.product.class.edit.update';
    const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_DELETE = 'admin.product.product.class.edit.delete';


    /**
     * Admin/Product/ProductController
     */
    // index
    const ADMIN_PRODUCT_INDEX_INITIALIZE = 'admin.product.index.initialize';
    const ADMIN_PRODUCT_INDEX_SEARCH = 'admin.product.index.search';

    // addImage
    const ADMIN_PRODUCT_ADD_IMAGE_COMPLETE = 'admin.product.add.image.complete';

    // edit
    const ADMIN_PRODUCT_EDIT_INITIALIZE = 'admin.product.edit.initialize';
    const ADMIN_PRODUCT_EDIT_SEARCH = 'admin.product.edit.search';
    const ADMIN_PRODUCT_EDIT_COMPLETE = 'admin.product.edit.complete';

    // delete
    const ADMIN_PRODUCT_DELETE_COMPLETE = 'admin.product.delete.complete';

    // copy
    const ADMIN_PRODUCT_COPY_COMPLETE = 'admin.product.copy.complete';

    // display
    const ADMIN_PRODUCT_DISPLAY_COMPLETE = 'admin.product.display.complete';

    // export


    /**
     * Admin/Setting/Shop/CsvController
     */
    // index
    const ADMIN_CSV_INDEX_INITIALIZE = 'admin.csv.index.initialize';
    const ADMIN_CSV_INDEX_COMPLETE = 'admin.csv.index.complete';

    /**
     * Admin/Setting/Shop/CustomerAgreementController
     */
    // index
    const ADMIN_CUSTOMER_AGREEMENT_INDEX_INITIALIZE = 'admin.customer.agreement.index.initialize';
    const ADMIN_CUSTOMER_AGREEMENT_INDEX_COMPLETE = 'admin.customer.agreement.index.complete';

    /**
     * Admin/Setting/Shop/DeliveryController
     */
    // index
    const ADMIN_DELIVERY_INDEX_COMPLETE = 'admin.delivery.index.complete';
    // edit
    const ADMIN_DELIVERY_EDIT_INITIALIZE = 'admin.delivery.edit.initialize';
    const ADMIN_DELIVERY_EDIT_COMPLETE = 'admin.delivery.edit.complete';
    // delete
    const ADMIN_DELIVERY_DELETE_COMPLETE = 'admin.delivery.delete.complete';

    /**
     * Admin/Setting/Shop/MailController
     */
    // index
    const ADMIN_MAIL_INDEX_INITIALIZE = 'admin.mail.index.initialize';
    const ADMIN_MAIL_INDEX_COMPLETE = 'admin.mail.index.complete';

    /**
     * Admin/Setting/Shop/PaymentController
     */
    // index
    const ADMIN_PAYMENT_INDEX_COMPLETE = 'admin.payment.index.complete';
    // edit
    const ADMIN_PAYMENT_EDIT_INITIALIZE = 'admin.payment.edit.initialize';
    const ADMIN_PAYMENT_EDIT_COMPLETE = 'admin.payment.edit.complete';
    // imageAdd
    const ADMIN_PAYMENT_IMAGE_ADD_COMPLETE = 'admin.payment.image.add.complete';
    // delete
    const ADMIN_PAYMENT_DELETE_COMPLETE = 'admin.payment.delete.complete';

    /**
     * Admin/Setting/Shop/ShopController
     */
    // index
    const ADMIN_SHOP_INDEX_INITIALIZE = 'admin.shop.index.initialize';
    const ADMIN_SHOP_INDEX_COMPLETE = 'admin.shop.index.complete';

    /**
     * Admin/Setting/Shop/TaxRuleController
     */
    // index
    const ADMIN_TAX_RULE_INDEX_INITIALIZE = 'admin.tax.rule.index.initialize';
    const ADMIN_TAX_RULE_INDEX_COMPLETE = 'admin.tax.rule.index.complete';
    // delete
    const ADMIN_TAX_RULE_DELETE_COMPLETE = 'admin.tax.rule.delete.complete';
    // editParameter
    const ADMIN_TAX_RULE_EDIT_PARAMETER_INITIALIZE = 'admin.tax.rule.edit.parameter.initialize';
    const ADMIN_TAX_RULE_EDIT_PARAMETER_COMPLETE = 'admin.tax.rule.edit.parameter.complete';

    /**
     * Admin/Setting/Shop/TradelawController
     */
    // index
    const ADMIN_TRADE_LAW_INDEX_INITIALIZE = 'admin.trade.law.index.initialize';
    const ADMIN_TRADE_LAW_INDEX_COMPLETE = 'admin.trade.law.index.complete';

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
    const ADMIN_MASTERDATA_INDEX_INITIALIZE = 'admin.masterdata.index.initialize';
    const ADMIN_MASTERDATA_INDEX_FORM2_INITIALIZE = 'admin.masterdata.index.form2.initialize';
    const ADMIN_MASTERDATA_INDEX_COMPLETE = 'admin.masterdata.index.complete';

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
    const FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE = 'front.block.search.product.index.initialize';

    /**
     * Mypage/ChangeController
     */
    // index
    const FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE = 'front.mypage.change.index.initialize';
    const FRONT_MYPAGE_CHANGE_INDEX_COMPLETE = 'front.mypage.change.index.complete';

    /**
     * Mypage/DeliveryController
     */
    // edit
    const FRONT_MYPAGE_DELIVERY_EDIT_INITIALIZE = 'front.mypage.delivery.edit.initialize';
    const FRONT_MYPAGE_DELIVERY_EDIT_COMPLETE = 'front.mypage.delivery.edit.complete';

    // delete
    const FRONT_MYPAGE_DELIVERY_DELETE_COMPLETE = 'front.mypage.delete.complete';

    /**
     * Mypage/MypageController
     */
    // login
    const FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE = 'front.mypage.mypage.login.initialize';

    // index
    const FRONT_MYPAGE_MYPAGE_INDEX_SEARCH = 'front.mypage.mypage.index.search';

    // history
    const FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE = 'front.mypage.mypage.history.initialize';

    // order
    const FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE = 'front.mypage.mypage.order.initialize';
    const FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE = 'front.mypage.mypage.order.complete';

    // favorite
    const FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH = 'front.mypage.mypage.favorite.search';

    // delete
    const FRONT_MYPAGE_MYPAGE_DELETE_INITIALIZE = 'front.mypage.mypage.delete.initialize';
    const FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE = 'front.mypage.mypage.delete.complete';

    /**
     * Mypage/WithdrawController
     */
    // index
    const FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE = 'front.mypage.withdraw.index.initialize';
    const FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE = 'front.mypage.withdraw.index.complete';

    /**
     * CartController
     */
    // index
    const FRONT_CART_INDEX_INITIALIZE = 'front.cart.index.initialize';
    const FRONT_CART_INDEX_COMPLETE = 'front.cart.index.complete';
    // add
    const FRONT_CART_ADD_INITIALIZE = 'front.cart.add.initialize';
    const FRONT_CART_ADD_COMPLETE = 'front.cart.add.complete';
    const FRONT_CART_ADD_EXCEPTION = 'front.cart.add.exception';

    // up
    const FRONT_CART_UP_INITIALIZE = 'front.cart.up.initialize';
    const FRONT_CART_UP_COMPLETE = 'front.cart.up.complete';
    const FRONT_CART_UP_EXCEPTION = 'front.cart.up.exception';

    // down
    const FRONT_CART_DOWN_INITIALIZE = 'front.cart.down.initialize';
    const FRONT_CART_DOWN_COMPLETE = 'front.cart.down.complete';
    const FRONT_CART_DOWN_EXCEPTION = 'front.cart.down.exception';

    // remove
    const FRONT_CART_REMOVE_INITIALIZE = 'front.cart.remove.initialize';
    const FRONT_CART_REMOVE_COMPLETE = 'front.cart.remove.complete';

    // buystep
    const FRONT_CART_BUYSTEP_INITIALIZE = 'front.cart.buystep.initialize';
    const FRONT_CART_BUYSTEP_COMPLETE = 'front.cart.buystep.complete';


    /**
     * ContactController
     */
    // index
    const FRONT_CONTACT_INDEX_INITIALIZE = 'front.contact.index.initialize';
    const FRONT_CONTACT_INDEX_COMPLETE = 'front.contact.index.complete';

    /**
     * EntryController
     */
    // index
    const FRONT_ENTRY_INDEX_INITIALIZE = 'front.entry.index.initialize';
    const FRONT_ENTRY_INDEX_COMPLETE = 'front.entry.index.complete';
    // activate
    const FRONT_ENTRY_ACTIVATE_COMPLETE = 'front.entry.activate.complete';

    /**
     * ForgotController
     */
    // index
    const FRONT_FORGOT_INDEX_INITIALIZE = 'front.forgot.index.initialize';
    const FRONT_FORGOT_INDEX_COMPLETE = 'front.forgot.index.complete';
    // reset
    const FRONT_FORGOT_RESET_COMPLETE = 'front.reset.index.complete';

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
    const FRONT_USER_DATA_INDEX_INITIALIZE = 'front.user.data.index.initialize';


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