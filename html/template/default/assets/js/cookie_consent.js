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
依存: Symfony CSRF Token
利用: 全フロントページ（default_frame.twig、機能 ON のときのみ読み込み）

公開 API（コア標準 GA・店舗・プラグインが連動に利用する拡張点）:
  - window.ECCUBE.cookieConsent.getStatus()        現在の同意状態を返す（'accepted'|'rejected'|null）
  - window.ECCUBE.cookieConsent.saveConsentStatus(...) 同意状態をサーバへ保存（fetch）
  - document の 'eccube:cookie-consent:changed' イベント（detail: { status }）を同意確定時に発火

注意:
  - 同意 Cookie はサーバ側レスポンス（Set-Cookie）でのみ設定する。JS から Cookie は書き込まない。
  - GA 等の第三者タグの読み込みは、上記イベント購読＋初回 getStatus() チェックで利用側が行う
    （本モジュールから loadGoogleAnalytics() を直接呼び出さない）。
====================================================================
*/
(function() {
    'use strict';

    // EC-CUBE名前空間の初期化
    window.ECCUBE = window.ECCUBE || {};
    window.ECCUBE.config = window.ECCUBE.config || {};
    window.ECCUBE.config.cookieConsent = window.ECCUBE.config.cookieConsent || {};
    window.ECCUBE.cookieConsent = window.ECCUBE.cookieConsent || {};

    var COOKIE_NAME = 'eccube_cookie_consent';
    var CHANGED_EVENT = 'eccube:cookie-consent:changed';
    var FADE_OUT_DURATION = 300;

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
     * バナー非表示（フェードアウト後に display: none）
     */
    function hideBanner() {
        var banner = document.getElementById('cookie-consent-banner');
        if (!banner) {
            return;
        }

        banner.style.transition = 'opacity ' + FADE_OUT_DURATION + 'ms';
        banner.style.opacity = '0';
        setTimeout(function() {
            banner.style.display = 'none';
            banner.style.removeProperty('opacity');
            banner.style.removeProperty('transition');
        }, FADE_OUT_DURATION);
    }

    /**
     * バナー表示（同意状態が未設定のときのみ）
     */
    function showBannerIfNeeded() {
        var banner = document.getElementById('cookie-consent-banner');
        if (banner && getStatus() === null) {
            banner.style.display = 'block';
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
     * クッキー同意状態を保存（fetch）。グローバル関数として公開。
     *
     * Cookie の書き込みはサーバ側（Set-Cookie）に一本化し、JS では書き込まない。
     *
     * @param {string} status - 'accepted' または 'rejected'
     * @param {string} source - 'popup' または 'settings_page'
     * @param {string|null} previousStatus - 以前の同意状態
     * @param {function} onSuccess - 成功時のコールバック（引数: サーバのレスポンス JSON）
     * @param {function} onError - エラー時のコールバック
     *                             （引数: { success: false, status?: number, message: string }。
     *                              message はサーバが文言を返さない場合は空文字）
     */
    window.ECCUBE.cookieConsent.saveConsentStatus = function(status, source, previousStatus, onSuccess, onError) {
        var config = window.ECCUBE && window.ECCUBE.config && window.ECCUBE.config.cookieConsent;
        var updateUrl = (config && config.updateUrl) || '/cookie-consent/update';

        var body = new URLSearchParams();
        body.append('consent_status', status);
        body.append('source', source);
        body.append('previous_status', previousStatus || '');
        body.append('_token', getCsrfToken());

        fetch(updateUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: body.toString(),
            credentials: 'same-origin'
        })
        .then(function(res) {
            // バリデーションエラー(400)はサーバが JSON で message を返すため本文を読む。
            // 403 / 500 は HTML が返り parse に失敗するので、原因を握り潰さずログに残す。
            return res.json().then(
                function(json) {
                    return { res: res, json: json };
                },
                function(parseError) {
                    console.error('Cookie consent save: invalid JSON response', res.status, parseError);

                    return { res: res, json: null };
                }
            );
        })
        .then(
            function(result) {
                if (result.res.ok && result.json && result.json.success) {
                    // 同意状態の確定をアプリ全体へ通知（公開フック）。
                    // GA 等のローダーはこのイベントを購読してリロードなしで読み込みを開始する。
                    dispatchChanged(status);

                    if (onSuccess) {
                        onSuccess(result.json);
                    }

                    return;
                }

                // message はサーバ由来の文言があるときだけ渡す。
                // 空のときは呼び出し側のローカライズ済み文言にフォールバックさせる。
                var message = (result.json && result.json.message) || '';
                console.error('Cookie consent save failed:', result.res.status, message);
                if (onError) {
                    onError({ success: false, status: result.res.status, message: message });
                }
            },
            // 第 2 引数で受けることで、onSuccess 内の例外を保存失敗として扱わない
            function(error) {
                console.error('Cookie consent save error:', error);
                if (onError) {
                    onError({ success: false, message: error && error.message ? error.message : String(error) });
                }
            }
        );
    };

    /**
     * DOM Ready - バナーの表示制御とイベントハンドラー
     */
    function init() {
        // 同意状態が未設定のときだけバナーを表示（キャッシュ安全：表示判定はクライアント側）
        showBannerIfNeeded();

        // 閉じるボタン（×）クリック時（Cookie は設定しない＝次回再表示）
        var closeButton = document.getElementById('cookie-consent-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                hideBanner();
            });
        }

        // 同意ボタンクリック時
        var acceptButton = document.getElementById('cookie-consent-accept');
        if (acceptButton) {
            acceptButton.addEventListener('click', function() {
                window.ECCUBE.cookieConsent.saveConsentStatus('accepted', 'popup', null, function() {
                    hideBanner();
                });
            });
        }

        // 拒否ボタンクリック時
        var rejectButton = document.getElementById('cookie-consent-reject');
        if (rejectButton) {
            rejectButton.addEventListener('click', function() {
                window.ECCUBE.cookieConsent.saveConsentStatus('rejected', 'popup', null, function() {
                    hideBanner();
                });
            });
        }
    }

    // 本スクリプトは </body> 直前で読み込まれるが、遅延読み込み等で
    // DOMContentLoaded 後に評価される場合もあるため両方に対応する。
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
