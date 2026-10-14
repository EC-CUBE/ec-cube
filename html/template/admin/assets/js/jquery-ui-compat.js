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

/**
 * jQuery UI 互換 shim (非推奨)
 *
 * 管理画面から jQuery UI を撤去した (#6943) が、プラグインの管理画面テンプレートには
 * admin.bundle.js が生やしていた $.fn.sortable / $.fn.resizable を前提にしたものがある。
 * それらを壊さないよう、同じ呼び出しを SortableJS と CSS の resize で近似する。
 *
 * この shim は 4.5 で削除する。新規実装では window.Sortable (SortableJS) と
 * CSS の `resize` + ResizeObserver を直接使うこと。
 *
 * 対応範囲 (ストアプラグインで実際に使われていた範囲に絞っている):
 *
 * - sortable
 *   - オプション: items / handle / cancel / placeholder / connectWith / distance / delay / disabled
 *   - コールバック: create / start / stop / update / receive / remove (第 2 引数 ui は item / helper / placeholder / sender)
 *   - メソッド: destroy / refresh / refreshPositions / cancel / toArray / serialize / option / enable / disable / instance / widget
 *     (cancel は jQuery UI と同じく、ドロップ後に呼んでも直前のドラッグ開始時の位置へ戻す。AJAX 保存失敗時の巻き戻し用)
 *   - 無視するオプション: axis / cursor / opacity / tolerance / helper / appendTo / zIndex / revert 等
 *   - 制約: items はコンテナ直下の要素のみ並び替えられる (SortableJS の制約)
 * - resizable
 *   - オプション: handles / minWidth / minHeight / maxWidth / maxHeight / disabled
 *   - コールバック: start / resize / stop (jQuery UI と同じくリサイズハンドルの押下中だけ発火する。
 *     ウィンドウ幅の変化など、ハンドル操作以外の大きさの変化では発火しない)
 *   - メソッド: destroy / option / enable / disable / instance / widget
 * - disableSelection / enableSelection
 */
const $ = require('jquery');
const SortableModule = require('sortablejs');
const Sortable = SortableModule.default || SortableModule;

/**
 * @typedef {Object} SortableUi jQuery UI sortable のコールバック第 2 引数
 * @property {JQuery} item 並び替え対象の要素
 * @property {JQuery} helper カーソルに追従しているクローン (ドラッグ中のみ実体がある)
 * @property {JQuery} placeholder ドロップ位置を示す要素 (SortableJS では item 自身)
 * @property {JQuery|null} sender connectWith で別のリストから移動してきた場合の移動元
 * @property {{top: number, left: number}} position 常に 0 (互換のためだけに持つ)
 * @property {{top: number, left: number}} originalPosition 常に 0
 * @property {{top: number, left: number}} offset 常に 0
 */

/**
 * @callback SortableCallback
 * @param {JQuery.Event} event 種別は sortstart / sortupdate 等
 * @param {SortableUi} ui
 * @returns {void|false} false を返すと後続の DOM イベントを発火しない
 */

/**
 * @typedef {Object} SortableOptions
 * @property {string} [items='> *'] 並び替える要素のセレクタ (コンテナ直下の要素のみ有効)
 * @property {string|false} [handle=false] ドラッグ開始を許すハンドルのセレクタ
 * @property {string|false} [cancel='input,textarea,button,select,option'] ドラッグを開始しない要素のセレクタ
 * @property {string|false} [placeholder=false] ドロップ位置の要素へ付けるクラス (空白区切りで複数可)。未指定なら不可視にする
 * @property {string|string[]|false} [connectWith=false] 相互に移動できるリストのセレクタ
 * @property {number} [distance=1] ドラッグ開始とみなす移動量 (px)
 * @property {number} [delay=0] ドラッグ開始までの遅延 (ms)
 * @property {boolean} [disabled=false]
 * @property {SortableCallback} [create]
 * @property {SortableCallback} [start]
 * @property {SortableCallback} [stop]
 * @property {SortableCallback} [update]
 * @property {SortableCallback} [receive]
 * @property {SortableCallback} [remove]
 */

/**
 * @typedef {Object} ResizableUi jQuery UI resizable のコールバック第 2 引数
 * @property {JQuery} element
 * @property {JQuery} helper element と同じ
 * @property {JQuery} originalElement element と同じ
 * @property {{top: number, left: number}} position
 * @property {{top: number, left: number}} originalPosition
 * @property {{width: number, height: number}} size 現在の大きさ
 * @property {{width: number, height: number}} originalSize リサイズ開始時の大きさ
 */

/**
 * @callback ResizableCallback
 * @param {JQuery.Event} event 種別は resizestart / resize / resizestop
 * @param {ResizableUi} ui
 * @returns {void|false}
 */

/**
 * @typedef {Object} ResizableOptions
 * @property {string} [handles='e,s,se'] 有効にする方向。n/s を含めば縦、e/w を含めば横に変更できる
 * @property {number|null} [minWidth=10]
 * @property {number|null} [minHeight=10]
 * @property {number|null} [maxWidth=null]
 * @property {number|null} [maxHeight=null]
 * @property {boolean} [disabled=false]
 * @property {ResizableCallback} [create]
 * @property {ResizableCallback} [start]
 * @property {ResizableCallback} [resize]
 * @property {ResizableCallback} [stop]
 */

/**
 * @typedef {CompatSortable|CompatResizable} CompatWidget
 */

const ISSUE_URL = 'https://github.com/EC-CUBE/ec-cube/issues/6943';

/** @type {Object<string, boolean>} 警告を出した API 名 */
const warned = {};

/**
 * 非推奨 API の警告を API ごとに 1 度だけ出す.
 *
 * @param {string} name $.fn 配下の API 名
 * @param {string} alternative 移行先の説明
 * @returns {void}
 */
function deprecated(name, alternative) {
    if (warned[name]) {
        return;
    }
    warned[name] = true;
    if (typeof console !== 'undefined' && typeof console.warn === 'function') {
        console.warn(
            '[EC-CUBE] $.fn.' + name + ' は jQuery UI 撤去に伴う互換 shim で、4.5 で削除されます。'
            + alternative + ' へ移行してください。' + ISSUE_URL
        );
    }
}

/**
 * jQuery UI の _trigger 相当。コールバックを呼び、要素に prefix 付きイベントを発火する。
 *
 * @param {CompatWidget} widget
 * @param {string} prefix イベント名の接頭辞 (sort / resize)
 * @param {string} name コールバック名 (start / update 等)
 * @param {Event|null|undefined} originalEvent 元になったブラウザイベント
 * @param {SortableUi|ResizableUi|Object} ui
 * @returns {boolean} コールバックが false を返すか preventDefault されたら false
 */
function trigger(widget, prefix, name, originalEvent, ui) {
    const event = $.Event(originalEvent);
    event.type = (prefix + name).toLowerCase();
    event.target = widget.element;
    const callback = widget.options[name];
    if (typeof callback === 'function' && callback.call(widget.element, event, ui) === false) {
        return false;
    }
    $(widget.element).trigger(event, ui);
    return !event.isDefaultPrevented();
}

/**
 * @param {CompatWidget} widget
 * @param {string} name メソッド名 (_ 始まりは呼べない)
 * @param {any[]} args
 * @returns {any}
 */
function getMethod(widget, name, args) {
    const method = widget[name];
    if (typeof method !== 'function' || name.charAt(0) === '_') {
        throw new Error("no such method '" + name + "' for " + widget.widgetName + ' widget instance');
    }
    return method.apply(widget, args);
}

/**
 * $.fn.xxx(options) / $.fn.xxx('method', ...args) の振り分け。jQuery UI の $.widget.bridge 相当。
 *
 * @param {string} widgetName
 * @param {new (element: HTMLElement, options: Object) => CompatWidget} Widget
 * @returns {(this: JQuery, options?: Object|string, ...args: any[]) => any} jQuery プラグイン関数
 */
function bridge(widgetName, Widget) {
    const dataKey = 'eccube-jquery-ui-compat-' + widgetName;

    return function (options) {
        const args = Array.prototype.slice.call(arguments, 1);
        const isMethodCall = typeof options === 'string';
        let returnValue = this;

        this.each(function () {
            let widget = $.data(this, dataKey);
            if (isMethodCall) {
                if (options === 'instance') {
                    returnValue = widget;
                    return false;
                }
                if (!widget) {
                    throw new Error("cannot call methods on " + widgetName + " prior to initialization; attempted to call method '" + options + "'");
                }
                const result = getMethod(widget, options, args);
                if (result !== widget && result !== undefined) {
                    returnValue = result && result.jquery ? returnValue.pushStack(result.get()) : result;
                    return false;
                }
                return undefined;
            }
            if (widget) {
                widget.option(options || {});
            } else {
                widget = new Widget(this, options || {});
                $.data(this, dataKey, widget);
            }
            return undefined;
        });

        return returnValue;
    };
}

/* ------------------------------------------------------------------ */
/* sortable                                                            */
/* ------------------------------------------------------------------ */

/** @type {SortableOptions} */
const SORTABLE_DEFAULTS = {
    items: '> *',
    handle: false,
    cancel: 'input,textarea,button,select,option',
    placeholder: false,
    connectWith: false,
    distance: 1,
    delay: 0,
    disabled: false,
};

class CompatSortable {
    /**
     * @param {HTMLElement} element 並び替えるリストのコンテナ
     * @param {SortableOptions} options
     */
    constructor(element, options) {
        /** @type {string} */
        this.widgetName = 'sortable';
        /** @type {HTMLElement} */
        this.element = element;
        /** @type {SortableOptions} */
        this.options = $.extend({}, SORTABLE_DEFAULTS, options);
        /** @type {Sortable} SortableJS のインスタンス */
        this.instance = null;
        /** @type {string|undefined} 既定の不可視プレースホルダを戻すための元の visibility */
        this._placeholderVisibility = undefined;
        /** @type {boolean} ドラッグ中か (jQuery UI の dragging 相当。ドロップ処理の先頭で false に戻る) */
        this._dragging = false;
        /**
         * @type {{item: HTMLElement, prev: Element|null, parent: HTMLElement}|undefined}
         * 直前のドラッグ開始時の位置 (jQuery UI の domPosition 相当)。cancel() の戻し先
         */
        this._domPosition = undefined;
        this._create();
    }

    /** @returns {void} */
    _create() {
        const self = this;
        const options = this.options;

        $(this.element).addClass('ui-sortable');
        this._setDisabledClass();

        this.instance = Sortable.create(this.element, {
            draggable: options.items,
            handle: options.handle || null,
            filter: options.cancel || null,
            preventOnFilter: false,
            group: this._groupName(),
            disabled: !!options.disabled,
            delay: options.delay,
            // jQuery UI と同じくマウスイベント駆動にし、要素のクローンをカーソルに追従させる
            forceFallback: true,
            fallbackOnBody: true,
            fallbackTolerance: options.distance,
            fallbackClass: 'ui-sortable-helper',
            ghostClass: 'ui-sortable-placeholder',
            animation: 0,
            onStart(evt) {
                // カーソルに追従するクローンは子孫も含めて id が重複するため外す
                if (Sortable.ghost) {
                    Sortable.ghost.removeAttribute('id');
                    Sortable.ghost.querySelectorAll('[id]').forEach((el) => el.removeAttribute('id'));
                }
                // この時点では item はまだ元の位置にある。cancel() で戻すために記録する
                self._domPosition = {
                    item: evt.item,
                    prev: evt.item.previousElementSibling,
                    parent: evt.from,
                };
                self._dragging = true;
                self._applyPlaceholder(evt.item);
                trigger(self, 'sort', 'start', evt.originalEvent, self._uiHash(evt));
            },
            onUnchoose(evt) {
                // ドロップ直後 (update / stop より前) に元の表示へ戻す
                self._dragging = false;
                self._clearPlaceholder(evt.item);
            },
            onUpdate(evt) {
                trigger(self, 'sort', 'update', evt.originalEvent, self._uiHash(evt));
            },
            onRemove(evt) {
                // 別のリストへ移動した: 移動元では update (sender なし) と remove を発火する
                trigger(self, 'sort', 'update', evt.originalEvent, self._uiHash(evt));
                trigger(self, 'sort', 'remove', evt.originalEvent, self._uiHash(evt));
            },
            onAdd(evt) {
                // 別のリストから受け取った: 移動先では receive と update (sender あり) を発火する
                const ui = self._uiHash(evt, evt.from);
                trigger(self, 'sort', 'receive', evt.originalEvent, ui);
                trigger(self, 'sort', 'update', evt.originalEvent, ui);
            },
            onEnd(evt) {
                trigger(self, 'sort', 'stop', evt.originalEvent, self._uiHash(evt));
            },
        });

        trigger(this, 'sort', 'create', null, {});
    }

    /**
     * @returns {string|undefined} SortableJS の group 名。connectWith 未指定なら独立したリストにする
     */
    _groupName() {
        const connectWith = this.options.connectWith;
        if (!connectWith) {
            return undefined;
        }
        // connectWith に同じセレクタを指定したリスト同士を相互に移動可能にする
        return 'ui-sortable-connect:' + (Array.isArray(connectWith) ? connectWith.join(',') : String(connectWith));
    }

    /** @returns {string[]} placeholder オプションのクラス名一覧 */
    _placeholderClasses() {
        const placeholder = this.options.placeholder;
        return typeof placeholder === 'string' ? placeholder.split(/\s+/).filter(Boolean) : [];
    }

    /**
     * @param {HTMLElement} item ドラッグ中にドロップ位置へ残る要素
     * @returns {void}
     */
    _applyPlaceholder(item) {
        const classes = this._placeholderClasses();
        if (classes.length) {
            item.classList.add(...classes);
        } else {
            // jQuery UI の既定は「元要素の大きさの空白」なので、ドロップ位置の要素を不可視にする
            this._placeholderVisibility = item.style.visibility;
            item.style.visibility = 'hidden';
        }
    }

    /**
     * @param {HTMLElement} item
     * @returns {void}
     */
    _clearPlaceholder(item) {
        const classes = this._placeholderClasses();
        if (classes.length) {
            item.classList.remove(...classes);
        } else if (this._placeholderVisibility !== undefined) {
            item.style.visibility = this._placeholderVisibility;
            this._placeholderVisibility = undefined;
        }
    }

    /**
     * @param {Sortable.SortableEvent} evt
     * @param {HTMLElement} [sender] 別のリストから受け取った場合の移動元
     * @returns {SortableUi}
     */
    _uiHash(evt, sender) {
        const $item = $(evt.item);
        return {
            helper: $(Sortable.ghost || evt.item),
            placeholder: $item,
            item: $item,
            sender: sender ? $(sender) : null,
            position: { top: 0, left: 0 },
            originalPosition: { top: 0, left: 0 },
            offset: { top: 0, left: 0 },
        };
    }

    /**
     * @param {{connected?: boolean}} [options] connected なら connectWith 先の要素も含める
     * @returns {JQuery}
     */
    _getItems(options) {
        let $items = $(this.element).find(this.options.items);
        if (options && options.connected && this.options.connectWith) {
            $(this.options.connectWith).not(this.element).each((_, el) => {
                const widget = $.data(el, 'eccube-jquery-ui-compat-sortable');
                if (widget) {
                    $items = $items.add($(el).find(widget.options.items));
                }
            });
        }
        return $items;
    }

    /** @returns {void} */
    _setDisabledClass() {
        $(this.element).toggleClass('ui-sortable-disabled ui-state-disabled', !!this.options.disabled);
    }

    /**
     * @param {string|Object} [key] 省略で全オプション、文字列で 1 件取得 (value 併用で設定)、オブジェクトで一括設定
     * @param {any} [value]
     * @returns {any|this}
     */
    option(key, value) {
        if (key === undefined) {
            return $.extend({}, this.options);
        }
        if (typeof key === 'string') {
            if (arguments.length === 1) {
                return this.options[key];
            }
            this._setOption(key, value);
            return this;
        }
        Object.keys(key).forEach((name) => this._setOption(name, key[name]));
        return this;
    }

    /**
     * @param {string} key
     * @param {any} value
     * @returns {void}
     */
    _setOption(key, value) {
        this.options[key] = value;
        switch (key) {
            case 'items':
                this.instance.option('draggable', value);
                break;
            case 'handle':
                this.instance.option('handle', value || null);
                break;
            case 'cancel':
                this.instance.option('filter', value || null);
                break;
            case 'disabled':
                this.instance.option('disabled', !!value);
                this._setDisabledClass();
                break;
            case 'connectWith':
                this.instance.option('group', this._groupName());
                break;
            case 'distance':
                this.instance.option('fallbackTolerance', value);
                break;
            case 'delay':
                this.instance.option('delay', value);
                break;
            default:
                break;
        }
    }

    /** @returns {this} */
    enable() {
        return this.option('disabled', false);
    }

    /** @returns {this} */
    disable() {
        return this.option('disabled', true);
    }

    /** @returns {JQuery} */
    widget() {
        return $(this.element);
    }

    /** @returns {this} */
    refresh() {
        // SortableJS は DOM を都度参照するため再走査は不要
        return this;
    }

    /** @returns {this} */
    refreshPositions() {
        return this;
    }

    /**
     * 直前のドラッグ開始時の位置へ要素を戻す.
     * jQuery UI と同じく、ドロップ後 (update コールバックの中や AJAX 保存の失敗時) に呼べる.
     *
     * @returns {this}
     */
    cancel() {
        if (this._dragging) {
            // ドラッグ中なら先にドロップを完了させる (jQuery UI が合成 mouseup で _mouseUp を呼ぶのと同じ)。
            // fallback モードの SortableJS は PointerEvent が使える環境では document の pointerup、
            // それ以外では mouseup でドロップ処理を行う (ドロップ後はリスナが外れるため両方送っても二重にならない)
            const doc = this.element.ownerDocument;
            if (typeof PointerEvent !== 'undefined') {
                doc.dispatchEvent(new PointerEvent('pointerup', { bubbles: true, cancelable: true }));
            }
            doc.dispatchEvent(new MouseEvent('mouseup', { bubbles: true, cancelable: true }));
        }
        const position = this._domPosition;
        if (position) {
            if (position.prev) {
                position.prev.after(position.item);
            } else {
                position.parent.prepend(position.item);
            }
        }
        return this;
    }

    /**
     * @param {{attribute?: string, item?: string, connected?: boolean}} [o]
     * @returns {string[]} 各要素の id (attribute で属性名を変更できる)
     */
    toArray(o) {
        const options = o || {};
        const ret = [];
        this._getItems(options).each(function () {
            ret.push($(options.item || this).attr(options.attribute || 'id') || '');
        });
        return ret;
    }

    /**
     * @param {{attribute?: string, item?: string, key?: string, expression?: RegExp, connected?: boolean}} [o]
     * @returns {string} "id[]=1&id[]=2" 形式のクエリ文字列
     */
    serialize(o) {
        const options = o || {};
        const str = [];
        this._getItems(options).each(function () {
            const res = ($(options.item || this).attr(options.attribute || 'id') || '')
                .match(options.expression || (/(.+)[\-=_](.+)/));
            if (res) {
                str.push((options.key || res[1] + '[]') + '=' + (options.key && options.expression ? res[1] : res[2]));
            }
        });
        if (!str.length && options.key) {
            str.push(options.key + '=');
        }
        return str.join('&');
    }

    /** @returns {this} */
    destroy() {
        this.instance.destroy();
        this._domPosition = undefined;
        $(this.element)
            .removeClass('ui-sortable ui-sortable-disabled ui-state-disabled')
            .removeData('eccube-jquery-ui-compat-sortable');
        return this;
    }
}

/* ------------------------------------------------------------------ */
/* resizable                                                           */
/* ------------------------------------------------------------------ */

/** @type {ResizableOptions} */
const RESIZABLE_DEFAULTS = {
    handles: 'e,s,se',
    minWidth: 10,
    minHeight: 10,
    maxWidth: null,
    maxHeight: null,
    disabled: false,
};

class CompatResizable {
    /**
     * @param {HTMLElement} element 大きさを変えられるようにする要素
     * @param {ResizableOptions} options
     */
    constructor(element, options) {
        /** @type {string} */
        this.widgetName = 'resizable';
        /** @type {HTMLElement} */
        this.element = element;
        /** @type {ResizableOptions} */
        this.options = $.extend({}, RESIZABLE_DEFAULTS, options);
        /** @type {ResizeObserver|undefined} */
        this._observer = undefined;
        /** @type {boolean} リサイズハンドルを押下中か */
        this._mouseDown = false;
        /** @type {boolean} start を発火済みで stop 待ちか */
        this._resizing = false;
        /** @type {{width: number, height: number}|undefined} 押下時の大きさ */
        this._originalSize = undefined;
        this._create();
    }

    /** @returns {void} */
    _create() {
        const el = this.element;
        this._originalStyle = {
            resize: el.style.resize,
            overflow: el.style.overflow,
            minWidth: el.style.minWidth,
            minHeight: el.style.minHeight,
            maxWidth: el.style.maxWidth,
            maxHeight: el.style.maxHeight,
        };

        $(el).addClass('ui-resizable');
        this._applyStyle();

        this._resizing = false;
        this._lastSize = this._size();
        this._onMouseUp = (event) => {
            this._mouseDown = false;
            if (this._resizing) {
                this._resizing = false;
                trigger(this, 'resize', 'stop', event, this._uiHash());
            }
        };
        this._onMouseDown = () => {
            this._originalSize = this._size();
            this._mouseDown = true;
            // jQuery UI と同じく押下中だけ document を監視する (destroy() を待たずにリスナが消える)
            el.ownerDocument.addEventListener('mouseup', this._onMouseUp, { once: true });
        };
        el.addEventListener('mousedown', this._onMouseDown);

        if (typeof ResizeObserver !== 'undefined') {
            this._observer = new ResizeObserver(() => this._onResize());
            this._observer.observe(el);
        }

        trigger(this, 'resize', 'create', null, {});
    }

    /** @returns {void} */
    _applyStyle() {
        const el = this.element;
        const options = this.options;
        // CSS の resize は overflow: visible では効かない
        if (window.getComputedStyle(el).overflow === 'visible') {
            el.style.overflow = 'hidden';
        }
        el.style.resize = options.disabled ? 'none' : this._resizeValue();
        if (options.minWidth != null) el.style.minWidth = options.minWidth + 'px';
        if (options.minHeight != null) el.style.minHeight = options.minHeight + 'px';
        if (options.maxWidth != null) el.style.maxWidth = options.maxWidth + 'px';
        if (options.maxHeight != null) el.style.maxHeight = options.maxHeight + 'px';
    }

    /** @returns {'both'|'horizontal'|'vertical'|'none'} handles オプションに対応する CSS resize の値 */
    _resizeValue() {
        const handles = String(this.options.handles || 'e,s,se').split(',').map((h) => h.trim());
        if (handles.indexOf('all') !== -1) {
            return 'both';
        }
        const horizontal = handles.some((h) => /^(e|w|ne|nw|se|sw)$/.test(h));
        const vertical = handles.some((h) => /^(n|s|ne|nw|se|sw)$/.test(h));
        if (horizontal && vertical) return 'both';
        if (horizontal) return 'horizontal';
        if (vertical) return 'vertical';
        return 'none';
    }

    /** @returns {{width: number, height: number}} */
    _size() {
        return { width: this.element.offsetWidth, height: this.element.offsetHeight };
    }

    /** @returns {ResizableUi} */
    _uiHash() {
        const $el = $(this.element);
        const position = $el.position();
        return {
            element: $el,
            helper: $el,
            originalElement: $el,
            originalPosition: position,
            originalSize: this._originalSize || this._lastSize,
            position,
            size: this._size(),
        };
    }

    /**
     * ResizeObserver のコールバック. リサイズハンドルの押下中に大きさが変わったときだけ
     * resize (初回は start も) を発火する. jQuery UI はハンドル操作でしか発火しないため、
     * ウィンドウ幅の変化などハンドル操作以外の変化では発火しない.
     *
     * @returns {void}
     */
    _onResize() {
        const size = this._size();
        if (size.width === this._lastSize.width && size.height === this._lastSize.height) {
            return;
        }
        this._lastSize = size;
        if (this.options.disabled || !this._mouseDown) {
            return;
        }
        if (!this._resizing) {
            this._resizing = true;
            trigger(this, 'resize', 'start', null, this._uiHash());
        }
        trigger(this, 'resize', 'resize', null, this._uiHash());
    }

    /**
     * @param {string|Object} [key] 省略で全オプション、文字列で 1 件取得 (value 併用で設定)、オブジェクトで一括設定
     * @param {any} [value]
     * @returns {any|this}
     */
    option(key, value) {
        if (key === undefined) {
            return $.extend({}, this.options);
        }
        if (typeof key === 'string') {
            if (arguments.length === 1) {
                return this.options[key];
            }
            this.options[key] = value;
        } else {
            $.extend(this.options, key);
        }
        this._applyStyle();
        $(this.element).toggleClass('ui-resizable-disabled ui-state-disabled', !!this.options.disabled);
        return this;
    }

    /** @returns {this} */
    enable() {
        return this.option('disabled', false);
    }

    /** @returns {this} */
    disable() {
        return this.option('disabled', true);
    }

    /** @returns {JQuery} */
    widget() {
        return $(this.element);
    }

    /** @returns {this} */
    destroy() {
        const el = this.element;
        if (this._observer) {
            this._observer.disconnect();
        }
        el.removeEventListener('mousedown', this._onMouseDown);
        el.ownerDocument.removeEventListener('mouseup', this._onMouseUp);
        Object.keys(this._originalStyle).forEach((prop) => {
            el.style[prop] = this._originalStyle[prop];
        });
        $(el)
            .removeClass('ui-resizable ui-resizable-disabled ui-state-disabled')
            .removeData('eccube-jquery-ui-compat-resizable');
        return this;
    }
}

/* ------------------------------------------------------------------ */
/* 登録                                                                */
/* ------------------------------------------------------------------ */

const sortableBridge = bridge('sortable', CompatSortable);
const resizableBridge = bridge('resizable', CompatResizable);

/**
 * @deprecated 4.5 で削除。SortableJS (window.Sortable) を使うこと
 * @this {JQuery}
 * @param {SortableOptions|string} [options] オプション、またはメソッド名
 * @returns {JQuery|any}
 */
$.fn.sortable = function () {
    deprecated('sortable', 'SortableJS (window.Sortable)');
    return sortableBridge.apply(this, arguments);
};

/**
 * @deprecated 4.5 で削除。CSS の resize と ResizeObserver を使うこと
 * @this {JQuery}
 * @param {ResizableOptions|string} [options] オプション、またはメソッド名
 * @returns {JQuery|any}
 */
$.fn.resizable = function () {
    deprecated('resizable', 'CSS の resize と ResizeObserver');
    return resizableBridge.apply(this, arguments);
};

/**
 * jQuery UI の ui/disable-selection.js 相当
 *
 * @deprecated 4.5 で削除。CSS の user-select: none を使うこと
 * @this {JQuery}
 * @returns {JQuery}
 */
$.fn.disableSelection = (function () {
    const eventType = 'onselectstart' in document.createElement('div') ? 'selectstart' : 'mousedown';
    return function () {
        deprecated('disableSelection', 'CSS の user-select: none');
        return this.on(eventType + '.ui-disableSelection', (event) => {
            event.preventDefault();
        });
    };
})();

/**
 * @deprecated 4.5 で削除
 * @this {JQuery}
 * @returns {JQuery}
 */
$.fn.enableSelection = function () {
    deprecated('enableSelection', 'CSS の user-select: none');
    return this.off('.ui-disableSelection');
};
