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
    const FRONT_CART_INDEX_INITIALIZE = 'front.cart.index.initialize';
    const FRONT_CART_INDEX_COMPLETE = 'front.cart.index.complete';
    // add
    const FRONT_CART_ADD_INITIALIZE = 'front.cart.add.initialize';
    const FRONT_CART_ADD_COMPLETE = 'front.cart.add.complete';
    // up
    const FRONT_CART_UP_INITIALIZE = 'front.cart.up.initialize';
    const FRONT_CART_UP_COMPLETE = 'front.cart.up.complete';
    // down
    const FRONT_CART_DOWN_INITIALIZE = 'front.cart.down.initialize';
    const FRONT_CART_DOWN_COMPLETE = 'front.cart.down.complete';
    // remove
    const FRONT_CART_REMOVE_INITIALIZE = 'front.cart.remove.initialize';
    const FRONT_CART_REMOVE_COMPLETE = 'front.cart.remove.complete';
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
    const FRONT_ENTRY_ACTIVATE_INITIALIZE = 'front.entry.activate.initialize';
    const FRONT_ENTRY_ACTIVATE_COMPLETE = 'front.entry.activate.complete';
    /**
     * ForgotController
     */
    // index
    const FRONT_FORGOT_INDEX_INITIALIZE = 'front.forgot.index.initialize';
    const FRONT_FORGOT_INDEX_COMPLETE = 'front.forgot.index.complete';
    // reset
    const FRONT_FORGOT_RESET_INITIALIZE = 'front.reset.index.initialize';
    const FRONT_FORGOT_RESET_COMPLETE = 'front.reset.index.complete';
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