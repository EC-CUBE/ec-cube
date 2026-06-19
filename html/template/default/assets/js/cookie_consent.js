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

/*
====================================================================
Module: CookieConsent
役割: クッキー同意バナーの動作制御 ＋ 同意状態の公開フック
依存: jQuery, Symfony CSRF Token
利用: 全フロントページ（default_frame.twig、機能 ON のときのみ読み込み）

公開 API（コア標準 GA・店舗・プラグインが連動に利用する拡張点）:
  - window.ECCUBE.cookieConsent.getStatus()        現在の同意状態を返す（'accepted'|'rejected'|null）
  - window.ECCUBE.cookieConsent.saveConsentStatus(...) 同意状態をサーバへ保存（Ajax）
  - document の 'eccube:cookie-consent:changed' イベント（detail: { status }）を同意確定時に発火

注意:
  - 同意 Cookie はサーバ側レスポンス（Set-Cookie）でのみ設定する。JS から Cookie は書き込まない。
  - GA 等の第三者タグの読み込みは、上記イベント購読＋初回 getStatus() チェックで利用側が行う
    （本モジュールから loadGoogleAnalytics() を直接呼び出さない）。
====================================================================
*/
(function($) {
    'use strict';

    // EC-CUBE名前空間の初期化
    window.ECCUBE = window.ECCUBE || {};
    window.ECCUBE.config = window.ECCUBE.config || {};
    window.ECCUBE.config.cookieConsent = window.ECCUBE.config.cookieConsent || {};
    window.ECCUBE.cookieConsent = window.ECCUBE.cookieConsent || {};

    var COOKIE_NAME = 'eccube_cookie_consent';
    var CHANGED_EVENT = 'eccube:cookie-consent:changed';

    /**
     * CSRFトークン取得
     */
    function getCsrfToken() {
        var meta = document.querySelector('meta[name="eccube-csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    /**
     * 現在の同意状態を取得（同意 Cookie を読み取るだけの純関数）
     *
     * @returns {string|null} 'accepted' | 'rejected' | null（未設定）
     */
    function getStatus() {
        var matches = document.cookie.match(new RegExp('(?:^|; )' + COOKIE_NAME + '=([^;]*)'));
        var status = matches ? decodeURIComponent(matches[1]) : null;

        // 許可値（accepted/rejected）以外は未設定（null）扱いに揃える。
        // 改ざん等で不正値が入ってもバナー再表示・GA 非ロードが破綻しないようにする。
        return (status === 'accepted' || status === 'rejected') ? status : null;
    }

    // 現在状態の getter を公開（GA・店舗・プラグインの初回チェック用）
    window.ECCUBE.cookieConsent.getStatus = getStatus;

    /**
     * バナー非表示
     */
    function hideBanner() {
        $('#cookie-consent-banner').fadeOut(300);
    }

    /**
     * バナー表示（同意状態が未設定のときのみ）
     */
    function showBannerIfNeeded() {
        if (getStatus() === null) {
            $('#cookie-consent-banner').show();
        }
    }

    /**
     * 同意確定をアプリ全体へ通知する（公開フック）。
     * 登録済みのリスナ（コア標準 GA・店舗・プラグイン）が購読してタグ読み込み等を行う。
     *
     * @param {string} status - 'accepted' または 'rejected'
     */
    function dispatchChanged(status) {
        document.dispatchEvent(new CustomEvent(CHANGED_EVENT, { detail: { status: status } }));
    }

    /**
     * クッキー同意状態を保存（Ajax）。グローバル関数として公開。
     *
     * Cookie の書き込みはサーバ側（Set-Cookie）に一本化し、JS では書き込まない。
     *
     * @param {string} status - 'accepted' または 'rejected'
     * @param {string} source - 'popup' または 'settings_page'
     * @param {string|null} previousStatus - 以前の同意状態
     * @param {function} onSuccess - 成功時のコールバック
     * @param {function} onError - エラー時のコールバック
     */
    window.ECCUBE.cookieConsent.saveConsentStatus = function(status, source, previousStatus, onSuccess, onError) {
        var config = window.ECCUBE && window.ECCUBE.config && window.ECCUBE.config.cookieConsent;
        var updateUrl = (config && config.updateUrl) || '/cookie_consent/update';

        $.ajax({
            url: updateUrl,
            type: 'POST',
            data: {
                consent_status: status,
                source: source,
                previous_status: previousStatus || null,
                _token: getCsrfToken()
            },
            dataType: 'json'
        })
        .done(function(response) {
            if (response.success) {
                // 同意状態の確定をアプリ全体へ通知（公開フック）。
                // GA 等のローダーはこのイベントを購読してリロードなしで読み込みを開始する。
                dispatchChanged(status);

                if (onSuccess) {
                    onSuccess(response);
                }
            } else {
                console.error('Cookie consent save failed:', response.message);
                if (onError) {
                    onError(response);
                }
            }
        })
        .fail(function(xhr, ajaxStatus, error) {
            console.error('Cookie consent save error:', error);
            if (onError) {
                onError({ success: false, message: error });
            }
        });
    };

    /**
     * DOM Ready - バナーの表示制御とイベントハンドラー
     */
    $(function() {
        // 同意状態が未設定のときだけバナーを表示（キャッシュ安全：表示判定はクライアント側）
        showBannerIfNeeded();

        // 閉じるボタン（×）クリック時（Cookie は設定しない＝次回再表示）
        $('#cookie-consent-close').on('click', function() {
            hideBanner();
        });

        // 同意ボタンクリック時
        $('#cookie-consent-accept').on('click', function() {
            window.ECCUBE.cookieConsent.saveConsentStatus('accepted', 'popup', null, function() {
                hideBanner();
            });
        });

        // 拒否ボタンクリック時
        $('#cookie-consent-reject').on('click', function() {
            window.ECCUBE.cookieConsent.saveConsentStatus('rejected', 'popup', null, function() {
                hideBanner();
            });
        });
    });
})(jQuery);
