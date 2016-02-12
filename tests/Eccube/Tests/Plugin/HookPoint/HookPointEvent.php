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

namespace Plugin\HookPoint;

use Eccube\Event\EccubeEvents;
use Eccube\Event\EventArgs;
use Symfony\Component\Validator\Constraints as Assert;

class HookPointEvent
{

    /** @var  \Eccube\Application $app */
    private $app;

    public function __construct($app)
    {
        $this->app = $app;
    }


    // Admin/AdminContoller

// login
    public function onAdminLogin1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ADMIM_LOGIN_INITIALIZE;
    }


// index
    function onAdminIndex1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ADMIM_INDEX_INITIALIZE;
    }

    function onAdminIndex2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ADMIM_INDEX_ORDER;
    }

    function onAdminIndex3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ADMIM_INDEX_SALES;
    }

    function onAdminIndex4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ADMIM_INDEX_COMPLETE;
    }

// searchNonStockProducts

// Admin/Content/BlockController

// index
    function onAdminContentBlock1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_BLOCK_INDEX_COMPLETE;
    }

// edit
    function onAdminContentBlock2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_INITIALIZE;
    }
    function onAdminContentBlock3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_BLOCK_EDIT_COMPLETE;
    }

// delete
    function onAdminContentBlock4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_BLOCK_DELETE_COMPLETE;
    }


// Admin/Content/FileController

// index

// create

// delete

// download

// upload


// Admin/Content/LayoutController

// index
    function onAdminContentLayout1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_INITIALIZE;
    }

    function onAdminContentLayout2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_LAYOUT_INDEX_COMPLETE;
    }


// Admin/Content/NewsController

// index
    function onAdminContentNews1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_NEWS_INDEX_INITIALIZE;
    }

    function onAdminContentNews2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_NEWS_INDEX_COMPLETE;
    }


// edit
    function onAdminContentNews3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_INITIALIZE;
    }

    function onAdminContentNews4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_NEWS_EDIT_COMPLETE;
    }


// delete
    function onAdminContentNews5(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_NEWS_DELETE_COMPLETE;
    }


// Admin/Content/PageController

// index
    function onAdminContentPage1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_PAGE_INDEX_COMPLETE;
    }


// edit
    function onAdminContentPage2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_INITIALIZE;
    }

    function onAdminContentPage3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_PAGE_EDIT_COMPLETE;
    }


// delete
    function onAdminContentPage4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CONTENT_PAGE_DELETE_COMPLETE;
    }


// Admin/Customer/CustomerController

// index
    public function onAdminCustomer1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_INDEX_INITIALIZE;
    }

    public function onAdminCustomer2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_INDEX_SEARCH;
    }

// resend
    public function onAdminCustomer3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_RESEND_COMPLETE;
    }

// delete
    public function onAdminCustomer4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_DELETE_COMPLETE;
    }

// export


// Admin/Customer/CustomerEditController

// index
    public function onAdminCustomerEdit1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_INITIALIZE;
    }

    public function onAdminCustomerEdit2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CUSTOMER_EDIT_INDEX_COMPLETE;
    }



// Admin/Order/EditController

// index
    public function onAdminOrderEdit1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_INDEX_INITIALIZE;
    }

    public function onAdminOrderEdit2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_INDEX_COMPLETE;
    }

// searchCustomer
    public function onAdminOrderEdit3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_INITIALIZE;
    }

    public function onAdminOrderEdit4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_SEARCH;
    }

    public function onAdminOrderEdit5(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_COMPLETE;
    }

// searchCustomerById
    public function onAdminOrderEdit6(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_INITIALIZE;
    }

    public function onAdminOrderEdit7(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_CUSTOMER_BY_ID_COMPLETE;
    }

// searchProduct
    public function onAdminOrderEdit8(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_INITIALIZE;
    }

    public function onAdminOrderEdit9(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_SEARCH;
    }

    public function onAdminOrderEdit10(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_EDIT_SEARCH_PRODUCT_COMPLETE;
    }



// Admin/Order/MailController

// index
    public function onAdminMail1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_INDEX_INITIALIZE;
    }

    public function onAdminMail2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CHANGE;
    }

    public function onAdminMail3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_INDEX_CONFIRM;
    }

    public function onAdminMail4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_INDEX_COMPLETE;
    }

// view
    public function onAdminMail5(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_VIEW_COMPLETE;
    }

// mailAll
    public function onAdminMail6(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_INITIALIZE;
    }

    public function onAdminMail7(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CHANGE;
    }

    public function onAdminMail8(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_CONFIRM;
    }

    public function onAdminMail9(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_MAIL_MAIL_ALL_COMPLETE;
    }



// Admin/Order/OrderController

// index
    public function onAdminOrder1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_INDEX_INITIALIZE;
    }

    public function onAdminOrder2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_INDEX_SEARCH;
    }

// delete
    public function onAdminOrder3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_ORDER_DELETE_COMPLETE;
    }

// exportOrder

// exportShipping


// Admin/Product/CategoryController

// index
    public function onAdminCategory1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_INITIALIZE;
    }

    public function onAdminCategory2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CATEGORY_INDEX_COMPLETE;
    }

// delete
    public function onAdminCategory3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CATEGORY_DELETE_COMPLETE;
    }

// export


// Admin/Product/ClassCategoryController

// index
    public function onAdminClassCategory1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_INITIALIZE;
    }

    public function onAdminClassCategory2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_INDEX_COMPLETE;
    }

// delete
    public function onAdminClassCategory3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_CATEGORY_DELETE_COMPLETE;
    }



// Admin/Product/ClassNameController

// index
    public function onAdminClassName1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_INITIALIZE;
    }

    public function onAdminClassName2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_INDEX_COMPLETE;
    }

// delete
    public function onAdminClassName3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_CLASS_NAME_DELETE_COMPLETE;
    }



// Admin/Product/CsvImportController

// csvProduct

// csvCatgory

// csvTemplate


// Admin/Product/ProductClassController

// index
    public function onAdminProductClass1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_INITIALIZE;
    }

    public function onAdminProductClass2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_INDEX_CLASSES;
    }

// edit
    public function onAdminProductClass3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_PRODUCT_CLASS_EDIT_INITIALIZE;
    }

    public function onAdminProductClass4(EventArgs $event)
    {
    }

    public function onAdminProductClass5(EventArgs $event)
    {
    }

    public function onAdminProductClass6(EventArgs $event)
    {
    }



// Admin/Product/ProductController

// index
    public function onAdminProduct1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_INDEX_INITIALIZE;
    }

    public function onAdminProduct2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_INDEX_SEARCH;
    }

// addImage
    public function onAdminProduct3(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_ADD_IMAGE_COMPLETE;
    }

// edit
    public function onAdminProduct4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_EDIT_INITIALIZE;
    }

    public function onAdminProduct5(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_EDIT_SEARCH;
    }

    public function onAdminProduct6(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_EDIT_COMPLETE;
    }

// delete
    public function onAdminProduct7(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_DELETE_COMPLETE;
    }

// copy
    public function onAdminProduct8(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_COPY_COMPLETE;
    }

// display
    public function onAdminProduct9(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_PRODUCT_DISPLAY_COMPLETE;
    }

// export


// Admin/Setting/Shop/CsvController

// index
    public function onAdminCsv1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_INDEX_INITIALIZE;
    }

    public function onAdminCsv2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_INDEX_COMPLETE;
    }


// Admin/Setting/Shop/CustomerAgreementController

// index
    public function onAdminAgree1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CSV_INDEX_INITIALIZE;
    }

    public function onAdminAgree2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_CSV_INDEX_COMPLETE;
    }


// Admin/Setting/Shop/DeliveryController

// index
    public function onAdminDelivery1(EventArgs $event)
    {
    }

    public function onAdminDelivery2(EventArgs $event)
    {
    }

// edit
    public function onAdminDelivery3(EventArgs $event)
    {
    }

    public function onAdminDelivery4(EventArgs $event)
    {
    }

// delete
    public function onAdminDelivery5(EventArgs $event)
    {
    }

    public function onAdminDelivery6(EventArgs $event)
    {
    }


// Admin/Setting/Shop/MailController

// index
    public function onAdminShopMail1(EventArgs $event)
    {
    }

    public function onAdminShopMail2(EventArgs $event)
    {
    }


// Admin/Setting/Shop/PaymentController

// index
    public function onAdminPayment1(EventArgs $event)
    {
    }

    public function onAdminPayment2(EventArgs $event)
    {
    }

// edit
    public function onAdminPayment3(EventArgs $event)
    {
    }

    public function onAdminPayment4(EventArgs $event)
    {
    }

// imageAdd
    public function onAdminPayment5(EventArgs $event)
    {
    }

    public function onAdminPayment6(EventArgs $event)
    {
    }

// delete
    public function onAdminPayment7(EventArgs $event)
    {
    }

    public function onAdminPayment8(EventArgs $event)
    {
    }


// Admin/Setting/Shop/ShopController

// index
    public function onAdminShop1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_SHOP_INDEX_INITIALIZE;
    }

    public function onAdminShop2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_SHOP_INDEX_COMPLETE;
    }


// Admin/Setting/Shop/TaxRuleController

// index
    public function onAdminTax1(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_TAX_RULE_INDEX_INITIALIZE;
    }

    public function onAdminTax2(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_TAX_RULE_INDEX_COMPLETE;
    }

// delete
    public function onAdminTax4(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_TAX_RULE_DELETE_COMPLETE;
    }

// editParameter
    public function onAdminTax5(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_TAX_RULE_EDIT_PARAMETER_INITIALIZE;
    }

    public function onAdminTax6(EventArgs $event)
    {
        echo EccubeEvents::ADMIN_TAX_RULE_EDIT_PARAMETER_COMPLETE;
    }


// Admin/Setting/Shop/TradelawController

// index
    public function onAdminTrade1(EventArgs $event)
    {
    }

    public function onAdminTrade2(EventArgs $event)
    {
    }


// Admin/Setting/System/AuthorityController

// index
    public function onAdminAuthority1(EventArgs $event)
    {
    }

    public function onAdminAuthority2(EventArgs $event)
    {
    }



// Admin/Setting/System/LogController

// index
    public function onAdminLog1(EventArgs $event)
    {
    }

    public function onAdminLog2(EventArgs $event)
    {
    }



// Admin/Setting/System/MasterdataController

// index
    public function onAdminMasterdata1(EventArgs $event)
    {
    }

    public function onAdminMasterdata2(EventArgs $event)
    {
    }

    public function onAdminMasterdata3(EventArgs $event)
    {
    }

// edit
    public function onAdminMasterdata4(EventArgs $event)
    {
    }

    public function onAdminMasterdata5(EventArgs $event)
    {
    }

    public function onAdminMasterdata6(EventArgs $event)
    {
    }



// Admin/Setting/System/MemberController

// index
    public function onAdminMember1(EventArgs $event)
    {
    }

// edit
    public function onAdminMember2(EventArgs $event)
    {
    }

    public function onAdminMember3(EventArgs $event)
    {
    }

// delete
    public function onAdminMember4(EventArgs $event)
    {
    }

    public function onAdminMember5(EventArgs $event)
    {
    }



// Block/SearchProductController

// index
    public function onBlock1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_BLOCK_SEARCH_PRODUCT_INDEX_INITIALIZE;
    }


// Mypage/ChangeController

// index
    public function onMypageChange1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_INITIALIZE;
    }

    public function onMypageChange2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_CHANGE_INDEX_COMPLETE;
    }


// Mypage/DeliveryController

// edit
    public function onMypageDelivery1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_DELIVERY_EDIT_INITIALIZE;
    }

    public function onMypageDelivery2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_DELIVERY_EDIT_COMPLETE;
    }

// delete
    public function onMypageDelivery3(EventArgs $event)
    {
    }

    public function onMypageDelivery4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_DELIVERY_DELETE_COMPLETE;
    }


// Mypage/MypageController

// login
    public function onMypage1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_LOGIN_INITIALIZE;
    }

// index
    public function onMypage2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_INDEX_SEARCH;
    }

// history
    public function onMypage3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_HISTORY_INITIALIZE;
    }

// order
    public function onMypage4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_INITIALIZE;
    }

    public function onMypage5(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_ORDER_COMPLETE;
    }

// favorite
    public function onMypage6(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_FAVORITE_SEARCH;
    }

// delete
    public function onMypage7(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_INITIALIZE;
    }

    public function onMypage8(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_MYPAGE_DELETE_COMPLETE;
    }


// Mypage/WithdrawController

// index
    public function onWithdraw1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_INITIALIZE;
    }

    public function onWithdraw2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_MYPAGE_WITHDRAW_INDEX_COMPLETE;
    }


// CartController

// index
    public function onCart1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_INDEX_INITIALIZE;
    }

    public function onCart2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_INDEX_COMPLETE;
    }

// add
    public function onCart3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_ADD_INITIALIZE;
    }

    public function onCart4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_ADD_COMPLETE;
    }

    public function onCart5(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_ADD_EXCEPTION;
    }

// up
    public function onCart6(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_UP_INITIALIZE;
    }

    public function onCart7(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_UP_COMPLETE;
    }

    public function onCart8(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_UP_EXCEPTION;
    }

// down
    public function onCart9(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_DOWN_INITIALIZE;
    }

    public function onCart10(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_DOWN_COMPLETE;
    }

    public function onCart11(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_DOWN_EXCEPTION;
    }

// remove
    public function onCart12(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_REMOVE_INITIALIZE;
    }

    public function onCart13(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CART_REMOVE_COMPLETE;
    }


// ContactController

// index
    public function onContact1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CONTACT_INDEX_INITIALIZE;
    }

    public function onContact2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_CONTACT_INDEX_COMPLETE;
    }


// EntryController

// index
    public function onEntry1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_ENTRY_INDEX_INITIALIZE;
    }

    public function onEntry2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_ENTRY_INDEX_COMPLETE;
    }

// activate
    public function onEntry3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_ENTRY_ACTIVATE_INITIALIZE;
    }

    public function onEntry4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_ENTRY_ACTIVATE_COMPLETE;
    }


// ForgotController

// index
    public function onForgot1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_FORGOT_INDEX_INITIALIZE;
    }

    public function onForgot2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_FORGOT_INDEX_COMPLETE;
    }

// reset
    public function onForgot3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_FORGOT_RESET_INITIALIZE;
    }

    public function onForgot4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_FORGOT_RESET_COMPLETE;
    }


// ProductController

// index
    public function onProduct1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_INDEX_INITIALIZE;
    }

    public function onProduct2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_INDEX_SEARCH;
    }

    public function onProduct3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_INDEX_COMPLETE;
    }

    public function onProduct4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_INDEX_DISP;
    }

    public function onProduct5(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_INDEX_ORDER;
    }

// detail
    public function onProduct6(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_DETAIL_INITIALIZE;
    }

    public function onProduct7(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_DETAIL_FAVORITE;
    }

    public function onProduct8(EventArgs $event)
    {
        echo EccubeEvents::FRONT_PRODUCT_DETAIL_COMPLETE;
    }



// ShoppingController

// index
    public function onShopping1(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_INDEX_INITIALIZE;
    }

// confirm
    public function onShopping2(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_CONFIRM_INITIALIZE;
    }

    public function onShopping3(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_CONFIRM_PROCESSING;
    }

    public function onShopping4(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_CONFIRM_COMPLETE;
    }

// complete
    public function onShopping5(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_COMPLETE_INITIALIZE;
    }

// delivery
    public function onShopping6(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_DELIVERY_INITIALIZE;
    }

    public function onShopping7(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_DELIVERY_COMPLETE;
    }

// payment
    public function onShopping8(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_PAYMENT_INITIALIZE;
    }

    public function onShopping9(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_PAYMENT_COMPLETE;
    }

// shippingChange

// shipping
    public function onShopping10(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_COMPLETE;
    }

// shippingEditChange

// shippingEdit
    public function onShopping11(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_INITIALIZE;
    }

    public function onShopping12(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_EDIT_COMPLETE;
    }

// customer
    public function onShopping13(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_CUSTOMER_INITIALIZE;
    }

// login
    public function onShopping14(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_LOGIN_INITIALIZE;
    }

// nonmember
    public function onShopping15(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_NONMEMBER_INITIALIZE;
    }

    public function onShopping16(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_NONMEMBER_COMPLETE;
    }

// shippingMultipleChange

// shippingMultiple
    public function onShopping17(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_INITIALIZE;
    }

    public function onShopping18(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_COMPLETE;
    }

// shippingMultipleEdit
    public function onShopping19(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_INITIALIZE;
    }

    public function onShopping20(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_MULTIPLE_EDIT_COMPLETE;
    }

// shippingError
    public function onShopping21(EventArgs $event)
    {
        echo EccubeEvents::FRONT_SHOPPING_SHIPPING_ERROR_COMPLETE;
    }



// UserDataController

// index


    function onMail1(EventArgs $event)
    {
        echo EccubeEvents::MAIL_CUSTOMER_CONFIRM;
    }

    function onMail2(EventArgs $event)
    {
        echo EccubeEvents::MAIL_CUSTOMER_COMPLETE;
    }

    function onMail3(EventArgs $event)
    {
        echo EccubeEvents::MAIL_CUSTOMER_WITHDRAW;
    }

    function onMail4(EventArgs $event)
    {
        echo EccubeEvents::MAIL_CONTACT;
    }

    function onMail5(EventArgs $event)
    {
        echo EccubeEvents::MAIL_ORDER;
    }

    function onMail6(EventArgs $event)
    {
        echo EccubeEvents::MAIL_ADMIN_CUSTOMER_CONFIRM;
    }

    function onMail7(EventArgs $event)
    {
        echo EccubeEvents::MAIL_ADMIN_ORDER;
    }

    function onMail8(EventArgs $event)
    {
        echo EccubeEvents::MAIL_PASSWORD_RESET;
    }

    function onMail9(EventArgs $event)
    {
        echo EccubeEvents::MAIL_PASSWORD_RESET_COMPLETE;
    }
}
