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
     * Admin/Setting/System/SecurityController
     */
    // index
    const ADMIN_SECURITY_INDEX_INITIALIZE = 'admin.security.index.initialize';
    const ADMIN_SECURITY_INDEX_COMPLETE = 'admin.security.index.complete';
    /**
     * Admin/Setting/System/SystemController
     */
    // index
    const ADMIN_SYSTEM_INDEX_INITIALIZE = 'admin.system.index.initialize';
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