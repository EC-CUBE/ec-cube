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

namespace Eccube\Event;

final class EccubeEvents
{
    /**
     * Admin/AdminController
     */
    // login
    public const ADMIN_ADMIM_LOGIN_INITIALIZE = 'admin.admin.login.initialize';

    // index
    public const ADMIN_ADMIM_INDEX_INITIALIZE = 'admin.admin.index.initialize';
    public const ADMIN_ADMIM_INDEX_ORDER = 'admin.admin.index.order';
    public const ADMIN_ADMIM_INDEX_SALES = 'admin.admin.index.sales';
    public const ADMIN_ADMIM_INDEX_COMPLETE = 'admin.admin.index.complete';

    // searchNonStockProducts

    // changePassword
    public const ADMIN_ADMIM_CHANGE_PASSWORD_INITIALIZE = 'admin.admin.change_password.initialize';
    public const ADMIN_ADMIN_CHANGE_PASSWORD_COMPLETE = 'admin.admin.change_password.complete';

    /**
     * Admin/Content/BlockController
     */
    // index
    public const ADMIN_CONTENT_BLOCK_INDEX_COMPLETE = 'admin.content.block.index.complete';

    // edit
    public const ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE = 'admin.content.block.edit.initialize';
    public const ADMIN_CONTENT_BLOCK_EDIT_COMPLETE = 'admin.content.block.edit.complete';

    // delete
    public const ADMIN_CONTENT_BLOCK_DELETE_COMPLETE = 'admin.content.block.delete.complete';

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
    public const ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE = 'admin.content.layout.index.initialize';
    public const ADMIN_CONTENT_LAYOUT_INDEX_COMPLETE = 'admin.content.layout.index.complete';

    /**
     * Admin/Content/NewsController
     */
    // index
    public const ADMIN_CONTENT_NEWS_INDEX_INITIALIZE = 'admin.content.news.index.initialize';

    // edit
    public const ADMIN_CONTENT_NEWS_EDIT_INITIALIZE = 'admin.content.news.edit.initialize';
    public const ADMIN_CONTENT_NEWS_EDIT_COMPLETE = 'admin.content.news.edit.complete';

    // delete
    public const ADMIN_CONTENT_NEWS_DELETE_COMPLETE = 'admin.content.news.delete.complete';

    /**
     * Admin/Content/PageController
     */
    // index
    public const ADMIN_CONTENT_PAGE_INDEX_COMPLETE = 'admin.content.page.index.initialize';

    // edit
    public const ADMIN_CONTENT_PAGE_EDIT_INITIALIZE = 'admin.content.page.edit.initialize';
    public const ADMIN_CONTENT_PAGE_EDIT_COMPLETE = 'admin.content.page.edit.complete';

    // delete
    public const ADMIN_CONTENT_PAGE_DELETE_COMPLETE = 'admin.content.page.delete.complete';

    /**
     * Admin/Customer/CustomerController
     */
    // index
    public const ADMIN_CUSTOMER_INDEX_INITIALIZE = 'admin.customer.index.initialize';
    public const ADMIN_CUSTOMER_INDEX_SEARCH = 'admin.customer.index.search';

    // resend
    public const ADMIN_CUSTOMER_RESEND_COMPLETE = 'admin.customer.resend.complete';

    // delete
    public const ADMIN_CUSTOMER_DELETE_COMPLETE = 'admin.customer.delete.complete';

    public const ADMIN_CUSTOMER_DELIVERY_DELETE_COMPLETE = 'admin.customer.delivery.delete.complete';

    // export
    public const ADMIN_CUSTOMER_CSV_EXPORT = 'admin.customer.csv.export';

    /**
     * Admin/Customer/CustomerEditController
     */
    // index
    public const ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE = 'admin.customer.edit.index.initialize';
    public const ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE = 'admin.customer.edit.index.complete';

    // index
    public const ADMIN_CUSTOMER_DELIVERY_EDIT_INDEX_INITIALIZE = 'admin.customer.delivery.edit.index.initialize';
    public const ADMIN_CUSTOMER_DELIVERY_EDIT_INDEX_COMPLETE = 'admin.customer.delivery.edit.index.complete';

    /**
     * Admin/Order/EditController
     */
    // index
    public const ADMIN_ORDER_EDIT_INDEX_INITIALIZE = 'admin.order.edit.index.initialize';
    public const ADMIN_ORDER_EDIT_INDEX_PROGRESS = 'admin.order.edit.index.progress';
    public const ADMIN_ORDER_EDIT_INDEX_COMPLETE = 'admin.order.edit.index.complete';

    // searchCustomer
    public const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE = 'admin.order.edit.search.customer.initialize';
    public const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_SEARCH = 'admin.order.edit.search.customer.search';
    public const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE = 'admin.order.edit.search.customer.complete';

    // searchCustomerById
    public const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_INITIALIZE = 'admin.order.edit.search.customer.by.id.initialize';
    public const ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE = 'admin.order.edit.search.customer.by.id.complete';

    // searchProduct
    public const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE = 'admin.order.edit.search.product.initialize';
    public const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH = 'admin.order.edit.search.product.search';
    public const ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE = 'admin.order.edit.search.product.complete';

    /**
     * Admin/Order/MailController
     */
    // index
    public const ADMIN_ORDER_MAIL_INDEX_INITIALIZE = 'admin.order.mail.index.initialize';
    public const ADMIN_ORDER_MAIL_INDEX_CHANGE = 'admin.order.mail.index.change';
    public const ADMIN_ORDER_MAIL_INDEX_CONFIRM = 'admin.order.mail.index.confirm';
    public const ADMIN_ORDER_MAIL_INDEX_COMPLETE = 'admin.order.mail.index.complete';

    // mailAll
    public const ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE = 'admin.order.mail.mail.all.initialize';
    public const ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE = 'admin.order.mail.mail.all.change';
    public const ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM = 'admin.order.mail.mail.all.confirm';
    public const ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE = 'admin.order.mail.mail.all.complete';

    /**
     * Admin/Order/OrderController
     */
    // index
    public const ADMIN_ORDER_INDEX_INITIALIZE = 'admin.order.index.initialize';
    public const ADMIN_ORDER_INDEX_SEARCH = 'admin.order.index.search';

    // delete
    public const ADMIN_ORDER_DELETE_COMPLETE = 'admin.order.delete.complete';

    // exportOrder
    public const ADMIN_ORDER_CSV_EXPORT_ORDER = 'admin.order.csv.export.order';

    // exportShipping
    public const ADMIN_ORDER_CSV_EXPORT_SHIPPING = 'admin.order.csv.export.shipping';

    /**
     * Admin/Shipping/ShippingController
     */
    // index
    public const ADMIN_SHIPPING_INDEX_INITIALIZE = 'admin.shipping.index.initialize';
    public const ADMIN_SHIPPING_INDEX_SEARCH = 'admin.shipping.index.search';

    /**
     * Admin/Product/CategoryController
     */
    // index
    public const ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE = 'admin.product.category.index.initialize';
    public const ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE = 'admin.product.category.index.complete';

    // delete
    public const ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE = 'admin.product.category.delete.complete';

    // export
    public const ADMIN_PRODUCT_CATEGORY_CSV_EXPORT = 'admin.product.category.csv.export';

    /**
     * Admin/Product/TagController
     */
    // index
    public const ADMIN_PRODUCT_TAG_INDEX_INITIALIZE = 'admin.product.tag.index.initialize';
    public const ADMIN_PRODUCT_TAG_INDEX_COMPLETE = 'admin.product.tag.index.complete';

    // delete
    public const ADMIN_PRODUCT_TAG_DELETE_COMPLETE = 'admin.product.tag.delete.complete';

    /**
     * Admin/Product/ClassCategoryController
     */
    // index
    public const ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_INITIALIZE = 'admin.product.class.category.index.initialize';
    public const ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_COMPLETE = 'admin.product.class.category.index.complete';

    // delete
    public const ADMIN_PRODUCT_CLASS_CATEGORY_DELETE_COMPLETE = 'admin.product.class.category.delete.complete';

    /**
     * Admin/Product/ClassNameController
     */
    // index
    public const ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE = 'admin.product.class.name.index.initialize';
    public const ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE = 'admin.product.class.name.index.complete';

    // delete
    public const ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE = 'admin.product.class.name.delete.complete';

    /**
     * Admin/Product/CsvImportController
     */
    // csvProduct

    // csvCategory

    // csvTemplate

    /**
     * Admin/Product/ProductClassController
     */
    // index
    public const ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_INITIALIZE = 'admin.product.product.class.index.initialize';
    public const ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_CLASSES = 'admin.product.product.class.index.classes';

    // edit
    public const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_INITIALIZE = 'admin.product.product.class.edit.initialize';
    public const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_COMPLETE = 'admin.product.product.class.edit.complete';
    public const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_UPDATE = 'admin.product.product.class.edit.update';
    public const ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_DELETE = 'admin.product.product.class.edit.delete';

    /**
     * Admin/Product/ProductController
     */
    // index
    public const ADMIN_PRODUCT_INDEX_INITIALIZE = 'admin.product.index.initialize';
    public const ADMIN_PRODUCT_INDEX_SEARCH = 'admin.product.index.search';

    // addImage
    public const ADMIN_PRODUCT_ADD_IMAGE_COMPLETE = 'admin.product.add.image.complete';

    // edit
    public const ADMIN_PRODUCT_EDIT_INITIALIZE = 'admin.product.edit.initialize';
    public const ADMIN_PRODUCT_EDIT_SEARCH = 'admin.product.edit.search';
    public const ADMIN_PRODUCT_EDIT_COMPLETE = 'admin.product.edit.complete';

    // delete
    public const ADMIN_PRODUCT_DELETE_COMPLETE = 'admin.product.delete.complete';

    // copy
    public const ADMIN_PRODUCT_COPY_COMPLETE = 'admin.product.copy.complete';

    // display
    public const ADMIN_PRODUCT_DISPLAY_COMPLETE = 'admin.product.display.complete';

    // export
    public const ADMIN_PRODUCT_CSV_EXPORT = 'admin.product.csv.export';

    /**
     * Admin/Setting/Shop/CsvController
     */
    // index
    public const ADMIN_SETTING_SHOP_CSV_INDEX_INITIALIZE = 'admin.setting.shop.csv.index.initialize';
    public const ADMIN_SETTING_SHOP_CSV_INDEX_COMPLETE = 'admin.setting.shop.csv.index.complete';

    /**
     * Admin/Setting/Shop/DeliveryController
     */
    // index
    public const ADMIN_SETTING_SHOP_DELIVERY_INDEX_COMPLETE = 'admin.setting.shop.delivery.index.complete';
    // edit
    public const ADMIN_SETTING_SHOP_DELIVERY_EDIT_INITIALIZE = 'admin.setting.shop.delivery.edit.initialize';
    public const ADMIN_SETTING_SHOP_DELIVERY_EDIT_COMPLETE = 'admin.setting.shop.delivery.edit.complete';
    // delete
    public const ADMIN_SETTING_SHOP_DELIVERY_DELETE_COMPLETE = 'admin.setting.shop.delivery.delete.complete';
    // visibility
    public const ADMIN_SETTING_SHOP_DELIVERY_VISIBILITY_COMPLETE = 'admin.setting.shop.delivery.visibility.complete';

    /**
     * Admin/Setting/Shop/MailController
     */
    // index
    public const ADMIN_SETTING_SHOP_MAIL_INDEX_INITIALIZE = 'admin.setting.shop.mail.index.initialize';
    public const ADMIN_SETTING_SHOP_MAIL_INDEX_COMPLETE = 'admin.setting.shop.mail.index.complete';
    // preview
    public const ADMIN_SETTING_SHOP_MAIL_PREVIEW_COMPLETE = 'admin.setting.shop.mail.preview.complete';

    /**
     * Admin/Setting/Shop/PaymentController
     */
    // index
    public const ADMIN_SETTING_SHOP_PAYMENT_INDEX_COMPLETE = 'admin.setting.shop.payment.index.complete';
    // edit
    public const ADMIN_SETTING_SHOP_PAYMENT_EDIT_INITIALIZE = 'admin.setting.shop.payment.edit.initialize';
    public const ADMIN_SETTING_SHOP_PAYMENT_EDIT_COMPLETE = 'admin.setting.shop.payment.edit.complete';
    // imageAdd
    public const ADMIN_SETTING_SHOP_PAYMENT_IMAGE_ADD_COMPLETE = 'admin.setting.shop.payment.image.add.complete';
    // delete
    public const ADMIN_SETTING_SHOP_PAYMENT_DELETE_COMPLETE = 'admin.setting.shop.payment.delete.complete';

    public const ADMIN_SETTING_SHOP_TRADE_LAW_INDEX_COMPLETE = 'admin.setting.shop.trade.law.index.complete';
    public const ADMIN_SETTING_SHOP_TRADE_LAW_POST_COMPLETE = 'admin.setting.shop.trade.law.post.complete';

    /**
     * Admin/Setting/Shop/ShopController
     */
    // index
    public const ADMIN_SETTING_SHOP_SHOP_INDEX_INITIALIZE = 'admin.setting.shop.shop.index.initialize';
    public const ADMIN_SETTING_SHOP_SHOP_INDEX_COMPLETE = 'admin.setting.shop.shop.index.complete';

    /**
     * Admin/Setting/Shop/TaxRuleController
     */
    // index
    public const ADMIN_SETTING_SHOP_TAX_RULE_INDEX_INITIALIZE = 'admin.setting.shop.tax.rule.index.initialize';
    public const ADMIN_SETTING_SHOP_TAX_RULE_INDEX_COMPLETE = 'admin.setting.shop.tax.rule.index.complete';
    // delete
    public const ADMIN_SETTING_SHOP_TAX_RULE_DELETE_COMPLETE = 'admin.setting.shop.tax.rule.delete.complete';
    // editParameter
    public const ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_INITIALIZE = 'admin.setting.shop.tax.rule.edit.parameter.initialize';
    public const ADMIN_SETTING_SHOP_TAX_RULE_EDIT_PARAMETER_COMPLETE = 'admin.setting.shop.tax.rule.edit.parameter.complete';

    /**
     * Admin/Setting/System/AuthorityController
     */
    // index
    public const ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_INITIALIZE = 'admin.setting.system.authority.index.initialize';
    public const ADMIN_SETTING_SYSTEM_AUTHORITY_INDEX_COMPLETE = 'admin.setting.system.authority.index.complete';

    /**
     * Admin/Setting/System/LogController
     */
    // index
    public const ADMIN_SETTING_SYSTEM_LOG_INDEX_INITIALIZE = 'admin.setting.system.log.index.initialize';
    public const ADMIN_SETTING_SYSTEM_LOG_INDEX_COMPLETE = 'admin.setting.system.log.index.complete';

    /**
     * Admin/Setting/System/MasterdataController
     */
    // index
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_INITIALIZE = 'admin.setting.system.masterdata.index.initialize';
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_FORM2_INITIALIZE = 'admin.setting.system.masterdata.index.form2.initialize';
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_INDEX_COMPLETE = 'admin.setting.system.masterdata.index.complete';

    // edit
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_INITIALIZE = 'admin.setting.system.masterdata.edit.initialize';
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_FORM_INITIALIZE = 'admin.setting.system.masterdata.edit.form.initialize';
    public const ADMIN_SETTING_SYSTEM_MASTERDATA_EDIT_COMPLETE = 'admin.setting.system.masterdata.edit.complete';

    /**
     * Admin/Setting/System/MemberController
     */
    // index
    public const ADMIN_SETTING_SYSTEM_MEMBER_INDEX_INITIALIZE = 'admin.setting.system.member.index.initialize';

    // edit
    public const ADMIN_SETTING_SYSTEM_MEMBER_EDIT_INITIALIZE = 'admin.setting.system.member.edit.initialize';
    public const ADMIN_SETTING_SYSTEM_MEMBER_EDIT_COMPLETE = 'admin.setting.system.member.edit.complete';

    // delete
    public const ADMIN_SETTING_SYSTEM_MEMBER_DELETE_INITIALIZE = 'admin.setting.system.member.delete.initialize';
    public const ADMIN_SETTING_SYSTEM_MEMBER_DELETE_COMPLETE = 'admin.setting.system.member.delete.complete';

    /**
     * Block/SearchProductController
     */
    // index
    public const FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE = 'front.block.search.product.index.initialize';

    /**
     * Mypage/ChangeController
     */
    // index
    public const FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE = 'front.mypage.change.index.initialize';
    public const FRONT_MYPAGE_CHANGE_INDEX_COMPLETE = 'front.mypage.change.index.complete';

    /**
     * Mypage/DeliveryController
     */
    // edit
    public const FRONT_MYPAGE_DELIVERY_EDIT_INITIALIZE = 'front.mypage.delivery.edit.initialize';
    public const FRONT_MYPAGE_DELIVERY_EDIT_COMPLETE = 'front.mypage.delivery.edit.complete';

    // delete
    public const FRONT_MYPAGE_DELIVERY_DELETE_COMPLETE = 'front.mypage.delete.complete';

    /**
     * Mypage/MypageController
     */
    // login
    public const FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE = 'front.mypage.mypage.login.initialize';

    // index
    public const FRONT_MYPAGE_MYPAGE_INDEX_SEARCH = 'front.mypage.mypage.index.search';

    // history
    public const FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE = 'front.mypage.mypage.history.initialize';

    // order
    public const FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE = 'front.mypage.mypage.order.initialize';
    public const FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE = 'front.mypage.mypage.order.complete';

    // favorite
    public const FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH = 'front.mypage.mypage.favorite.search';

    // delete
    public const FRONT_MYPAGE_MYPAGE_DELETE_INITIALIZE = 'front.mypage.mypage.delete.initialize';
    public const FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE = 'front.mypage.mypage.delete.complete';

    /**
     * Mypage/WithdrawController
     */
    // index
    public const FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE = 'front.mypage.withdraw.index.initialize';
    public const FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE = 'front.mypage.withdraw.index.complete';

    /**
     * CartController
     */
    // index
    public const FRONT_CART_INDEX_INITIALIZE = 'front.cart.index.initialize';
    public const FRONT_CART_INDEX_COMPLETE = 'front.cart.index.complete';
    // add
    public const FRONT_CART_ADD_INITIALIZE = 'front.cart.add.initialize';
    public const FRONT_CART_ADD_COMPLETE = 'front.cart.add.complete';
    public const FRONT_CART_ADD_EXCEPTION = 'front.cart.add.exception';

    // up
    public const FRONT_CART_UP_INITIALIZE = 'front.cart.up.initialize';
    public const FRONT_CART_UP_COMPLETE = 'front.cart.up.complete';
    public const FRONT_CART_UP_EXCEPTION = 'front.cart.up.exception';

    // down
    public const FRONT_CART_DOWN_INITIALIZE = 'front.cart.down.initialize';
    public const FRONT_CART_DOWN_COMPLETE = 'front.cart.down.complete';
    public const FRONT_CART_DOWN_EXCEPTION = 'front.cart.down.exception';

    // remove
    public const FRONT_CART_REMOVE_INITIALIZE = 'front.cart.remove.initialize';
    public const FRONT_CART_REMOVE_COMPLETE = 'front.cart.remove.complete';

    // buystep
    public const FRONT_CART_BUYSTEP_INITIALIZE = 'front.cart.buystep.initialize';
    public const FRONT_CART_BUYSTEP_COMPLETE = 'front.cart.buystep.complete';

    /**
     * ContactController
     */
    // index
    public const FRONT_CONTACT_INDEX_INITIALIZE = 'front.contact.index.initialize';
    public const FRONT_CONTACT_INDEX_COMPLETE = 'front.contact.index.complete';

    /**
     * EntryController
     */
    // index
    public const FRONT_ENTRY_INDEX_INITIALIZE = 'front.entry.index.initialize';
    public const FRONT_ENTRY_INDEX_COMPLETE = 'front.entry.index.complete';
    // activate
    public const FRONT_ENTRY_ACTIVATE_COMPLETE = 'front.entry.activate.complete';

    /**
     * ForgotController
     */
    // index
    public const FRONT_FORGOT_INDEX_INITIALIZE = 'front.forgot.index.initialize';
    public const FRONT_FORGOT_INDEX_COMPLETE = 'front.forgot.index.complete';
    // reset
    public const FRONT_FORGOT_RESET_COMPLETE = 'front.reset.index.complete';

    /**
     * ProductController
     */
    // index
    public const FRONT_PRODUCT_INDEX_INITIALIZE = 'front.product.index.initialize';
    public const FRONT_PRODUCT_INDEX_SEARCH = 'front.product.index.search';
    public const FRONT_PRODUCT_INDEX_COMPLETE = 'front.product.index.complete';
    public const FRONT_PRODUCT_INDEX_DISP = 'front.product.index.disp';
    public const FRONT_PRODUCT_INDEX_ORDER = 'front.product.index.order';

    // detail
    public const FRONT_PRODUCT_DETAIL_INITIALIZE = 'front.product.detail.initialize';
    public const FRONT_PRODUCT_DETAIL_FAVORITE = 'front.product.detail.favorite';
    public const FRONT_PRODUCT_DETAIL_COMPLETE = 'front.product.detail.complete';

    public const FRONT_PRODUCT_CART_ADD_INITIALIZE = 'front.product.cart.add.initialize';
    public const FRONT_PRODUCT_CART_ADD_COMPLETE = 'front.product.cart.add.complete';

    public const FRONT_PRODUCT_FAVORITE_ADD_INITIALIZE = 'front.product.favorite.add.initialize';
    public const FRONT_PRODUCT_FAVORITE_ADD_COMPLETE = 'front.product.favorite.add.complete';

    /**
     * ShoppingController
     */
    // index
    public const FRONT_SHOPPING_INDEX_INITIALIZE = 'front.shopping.index.initialize';

    // confirm
    public const FRONT_SHOPPING_CONFIRM_INITIALIZE = 'front.shopping.confirm.initialize';
    public const FRONT_SHOPPING_CONFIRM_PROCESSING = 'front.shopping.confirm.processing';
    public const FRONT_SHOPPING_CONFIRM_COMPLETE = 'front.shopping.confirm.complete';

    // complete
    public const FRONT_SHOPPING_COMPLETE_INITIALIZE = 'front.shopping.complete.initialize';

    // delivery
    public const FRONT_SHOPPING_DELIVERY_INITIALIZE = 'front.shopping.delivery.initialize';
    public const FRONT_SHOPPING_DELIVERY_COMPLETE = 'front.shopping.delivery.complete';

    // payment
    public const FRONT_SHOPPING_PAYMENT_INITIALIZE = 'front.shopping.payment.initialize';
    public const FRONT_SHOPPING_PAYMENT_COMPLETE = 'front.shopping.payment.complete';

    // shippingChange
    public const FRONT_SHOPPING_SHIPPING_CHANGE_INITIALIZE = 'front.shopping.shipping.change.initialize';

    // shipping
    public const FRONT_SHOPPING_SHIPPING_COMPLETE = 'front.shopping.shipping.complete';

    // shippingEditChange
    public const FRONT_SHOPPING_SHIPPING_EDIT_CHANGE_INITIALIZE = 'front.shopping.shipping.edit.change.initialize';

    // shippingEdit
    public const FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE = 'front.shopping.shipping.edit.initialize';
    public const FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE = 'front.shopping.shipping.edit.complete';

    // customer
    public const FRONT_SHOPPING_CUSTOMER_INITIALIZE = 'front.shopping.customer.initialize';

    // login
    public const FRONT_SHOPPING_LOGIN_INITIALIZE = 'front.shopping.login.initialize';

    // nonmember
    public const FRONT_SHOPPING_NONMEMBER_INITIALIZE = 'front.shopping.nonmember.initialize';
    public const FRONT_SHOPPING_NONMEMBER_COMPLETE = 'front.shopping.nonmember.complete';

    // shippingMultipleChange
    public const FRONT_SHOPPING_SHIPPING_MULTIPLE_CHANGE_INITIALIZE = 'front.shopping.shipping.multiple.change.initialize';

    // shippingMultiple
    public const FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE = 'front.shopping.shipping.multiple.initialize';
    public const FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE = 'front.shopping.shipping.multiple.complete';

    // shippingMultipleEdit
    public const FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE = 'front.shopping.shipping.multiple.edit.initialize';
    public const FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE = 'front.shopping.shipping.multiple.edit.complete';

    // shippingError
    public const FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE = 'front.shopping.shipping.error.complete';

    /**
     * UserDataController
     */
    // index
    public const FRONT_USER_DATA_INDEX_INITIALIZE = 'front.user.data.index.initialize';

    /**
     * MailService
     */
    public const MAIL_CUSTOMER_CONFIRM = 'mail.customer.confirm';
    public const MAIL_CUSTOMER_COMPLETE = 'mail.customer.complete';
    public const MAIL_CUSTOMER_WITHDRAW = 'mail.customer.withdraw';
    public const MAIL_CONTACT = 'mail.contact';
    public const MAIL_ORDER = 'mail.order';
    public const MAIL_ADMIN_CUSTOMER_CONFIRM = 'mail.admin.customer.confirm';
    public const MAIL_ADMIN_ORDER = 'mail.admin.order';
    public const MAIL_PASSWORD_RESET = 'mail.password.reset';
    public const MAIL_PASSWORD_RESET_COMPLETE = 'mail.password.reset.complete';
}
