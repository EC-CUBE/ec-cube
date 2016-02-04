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
    /**
     * Mypage/ChangeController
     */
    // index
    const MYPAGE_CHANGE_INDEX_INITIALIZE = 'mypage.change.index.initialize';
    const MYPAGE_CHANGE_INDEX_COMPLETE = 'mypage.change.index.complete';
    /**
     * Mypage/DeliveryController
     */
    // edit
    const MYPAGE_DELIVERY_EDIT_INITIALIZE = 'mypage.delivery.edit.initialize';
    const MYPAGE_DELIVERY_EDIT_COMPLETE = 'mypage.delivery.edit.complete';
    // delete
    const MYPAGE_DELIVERY_DELETE_INITIALIZE = 'mypage.delivery.delete.initialize';
    const MYPAGE_DELIVERY_DELETE_COMPLETE = 'mypage.delivery.delete.complete';
    /**
     * Mypage/MypageController
     */
    // login
    const MYPAGE_MYPAGE_LOGIN_INITIALIZE = 'mypage.mypage.login.initialize';
    const MYPAGE_MYPAGE_LOGIN_COMPLETE = 'mypage.mypage.login.complete';
    // index
    const MYPAGE_MYPAGE_INDEX_INITIALIZE = 'mypage.mypage.index.initialize';
    const MYPAGE_MYPAGE_INDEX_COMPLETE = 'mypage.mypage.index.complete';
    // history
    const MYPAGE_MYPAGE_HISTORY_INITIALIZE = 'mypage.mypage.history.initialize';
    const MYPAGE_MYPAGE_HISTORY_COMPLETE = 'mypage.mypage.history.complete';
    // order
    const MYPAGE_MYPAGE_ORDER_INITIALIZE = 'mypage.mypage.order.initialize';
    const MYPAGE_MYPAGE_ORDER_COMPLETE = 'mypage.mypage.order.complete';
    // favorite
    const MYPAGE_MYPAGE_FAVORITE_INITIALIZE = 'mypage.mypage.favorite.initialize';
    const MYPAGE_MYPAGE_FAVORITE_COMPLETE = 'mypage.mypage.favorite.complete';
    // delete
    const MYPAGE_MYPAGE_DELETE_INITIALIZE = 'mypage.mypage.delete.initialize';
    const MYPAGE_MYPAGE_DELETE_COMPLETE = 'mypage.mypage.delete.complete';
    /**
     * Mypage/WithdrawController
     */
    // index
    const MYPAGE_WITHDRAW_INDEX_INITIALIZE = 'mypage.withdraw.index.initialize';
    const MYPAGE_WITHDRAW_INDEX_COMPLETE = 'mypage.withdraw.index.complete';
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
    // detail
    /**
     * ShoppingController
     */
    // index
    // confirm
    // complete
    // delivery
    // payment
    // shippingChange
    // shipping
    // shippingEditChange
    // shippingEdit
    // customer
    // login
    // nonmember
    // shippingMultipleChange
    // shippingMultiple
    // shippingMultipleEdit
    // shippingError
    /**
     * UserDataController
     */
    // index
}