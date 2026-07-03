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

namespace Eccube\Service\AgentCommerce\Exception;

/**
 * エージェントチェックアウトのエラーコード分類.
 *
 * ACP/UCP 共通の「2 系統エラー」のうち、ここで表すのは主に **プロトコル系**
 * (HTTP 4xx/5xx + Error) に対応する不正要求・処理不能を分類するためのもの。
 * 在庫切れ・販売停止等の **ビジネス系** (HTTP 200 + messages[]) は本コードではなく
 * {@link \Eccube\Service\AgentCommerce\CheckoutSession\AgentCheckoutMessage} で表現する。
 *
 * HTTP ステータス / messages[] への具体的な変換アダプタはプロトコル層 (#6776/#6574) が担う。
 */
enum AgentCheckoutErrorCode: string
{
    /** 明細が空. */
    case EMPTY_LINE_ITEMS = 'empty_line_items';

    /** 指定された商品 (ProductClass) が見つからない. */
    case PRODUCT_NOT_FOUND = 'product_not_found';

    /** 数量が不正 (0 以下等). */
    case INVALID_QUANTITY = 'invalid_quantity';

    /** 購入者/配送先住所が不足している. */
    case MISSING_ADDRESS = 'missing_address';

    /** 指定された都道府県 (Pref) が見つからない. */
    case INVALID_PREF = 'invalid_pref';

    /** セッションが確定不能な状態 (期限切れ・取消済等). */
    case INVALID_SESSION_STATE = 'invalid_session_state';
}
