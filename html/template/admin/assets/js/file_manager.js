/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
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

(function(window, undefined) {

    // 名前空間の重複を防ぐ
    if (window.eccube === undefined) {
        window.eccube = {};
    }

    var eccube = window.eccube;

    //指定されたidの削除を行うページを実行する。
    eccube.deleteMember = function(id, pageno, lastAdminFlag) {
        var url = "./delete.php?id=" + id + "&pageno=" + pageno;
        var message = lastAdminFlag ?
            '警告: 管理者がいなくなってしまいますと、システム設定などの操作が行えなくりますが宜しいでしょうか'
            : '登録内容を削除しても宜しいでしょうか';
        if (window.confirm(message)) {
            location.href = url;
        }
    };

    // ラジオボタンチェック状態を保存
    eccube.checkedRadios = "";

    // ラジオボタンのチェック状態を取得する。
    eccube.getRadioChecked = function() {
        var max;
        var cnt;
        var startname = "";
        var ret;
        var name;
        max = document.form1.elements.length;
        eccube.checkedRadios = [max];
        for (cnt = 0; cnt < max; cnt++) {
            if (document.form1.elements[cnt].type === 'radio') {
                name = document.form1.elements[cnt].name;
                /* radioボタンは同じ名前が２回続けて検出されるので、
                 最初の名前の検出であるかどうかの判定 */
                // 1回目の検出
                if (startname !== name) {
                    startname = name;
                    ret = document.form1.elements[cnt].checked;
                    if (ret === true) {
                        // 稼働がチェックされている。
                        eccube.checkedRadios[name] = 1;
                    }
                    // 2回目の検出
                } else {
                    ret = document.form1.elements[cnt].checked;
                    if (ret === true) {
                        // 非稼働がチェックされている。
                        eccube.checkedRadios[name] = 0;
                    }
                }
            }
        }
    };

    // 管理者メンバーページの切替
    eccube.moveMemberPage = function(pageno) {
        location.href = "?pageno=" + pageno;
    };

    // ページナビで使用する
    eccube.moveNaviPage = function(pageno, mode, form) {
        if (form === undefined) {
            form = eccube.defaults.formId;
        }
        var formElement = eccube.getFormElement(form);
        formElement.find("input[name='search_pageno']").val(pageno);
        if (mode !== undefined) {
            formElement.find("input[name='mode']").val(mode);
        }
        formElement.submit();
    };

    // ページナビで使用する(mode = search専用)
    eccube.moveSearchPage = function(pageno) {
        eccube.moveNaviPage(pageno, "search");
    };

    // ページナビで使用する(form2)
    eccube.moveSecondSearchPage = function(pageno) {
        eccube.moveNaviPage(pageno, "search", "form2");
    };

    // 項目に入った値をクリアする。
    eccube.clearValue = function(name) {
        document.form1[name].value = "";
    };

    // 規格分類登録へ移動
    eccube.moveClassCatPage = function(class_id) {
        location.href = "./classcategory.php?class_id=" + class_id;
    };

    eccube.checkAllBox = function(input, selector) {
        if ($(input).prop('checked')) {
            $(selector).prop('checked', true);
        } else {
            $(selector).prop('checked', false);
        }
    };

    //指定されたidの削除を行うページを実行する。
    eccube.moveDeleteUrl = function(url) {
        if (window.confirm('登録内容を削除しても宜しいでしょうか')) {
            location.href = url;
        }
        return false;
    };

    //配送料金を自動入力
    eccube.setDelivFee = function(max) {
        var name;
        for (var cnt = 1; cnt <= max; cnt++) {
            name = "fee" + cnt;
            document.form1[name].value = document.form1.fee_all.value;
        }
    };

    // 在庫数制限判定
    eccube.checkStockLimit = function(icolor) {
        if (document.form1.stock_unlimited) {
            var list = ['stock'];
            if (document.form1.stock_unlimited.checked) {
                eccube.changeDisabled(list, icolor);
                document.form1.stock.value = "";
            } else {
                eccube.changeDisabled(list, '');
            }
        }
    };

    // 確認メッセージ
    eccube.doConfirm = function() {
        return window.confirm('この内容で登録しても宜しいでしょうか');
    };

    // フォームに代入してからサブミットする。
    eccube.insertValueAndSubmit = function(fm, ele, val, msg) {
        var ret;
        if (msg) {
            ret = window.confirm(msg);
        } else {
            ret = true;
        }
        if (ret) {
            fm[ele].value = val;
            fm.submit();
            return false;
        }
        return false;
    };

    //制限数判定
    eccube.checkLimit = function(elem1, elem2, icolor) {
        if (document.form1[elem2]) {
            var list = [elem1];
            if (document.form1[elem2].checked) {
                eccube.changeDisabled(list, icolor);
                document.form1[elem1].value = "";
            } else {
                eccube.changeDisabled(list, '');
            }
        }
    };

    /**
     * ファイル管理
     */
    eccube.fileManager = {
        tree: "",                // 生成HTML格納
        arrTreeStatus: [],       // ツリー状態保持
        old_select_id: '',       // 前回選択していたファイル
        selectFileHidden: "",    // 選択したファイルのhidden名
        treeStatusHidden: "",    // ツリー状態保存用のhidden名
        modeHidden: ""           // modeセットhidden名
    };

    // ツリー表示
    eccube.fileManager.viewFileTree = function(view_id, arrTree, openFolder, selectHidden, treeHidden, mode) {
        eccube.fileManager.selectFileHidden = selectHidden;
        eccube.fileManager.treeStatusHidden = treeHidden;
        eccube.fileManager.modeHidden = mode;

        var tmp = [];
        $.each(arrTree, function(key, value) {
            arrTree[key]['path'] = value[2];
            arrTree[key]['depth'] = value[3];
            arrTree[key]['name'] = value['path'].split('/').slice(-1).pop();
            arrTree[key]['name'] = arrTree[key]['name'] ? arrTree[key]['name'] : 'user_data';
            arrTree[key]['children'] = [];
            if (tmp[value['depth']] === undefined) {
                tmp[value['depth']] = [value];
            } else {
                tmp[value['depth']].push(value);
            }
        });

        var i = tmp.length - 1;
        for (i; i > 0; i--) {
            var j = i - 1;
            $.each(tmp[i], function(iKey, iValue) {
                $.each(tmp[j], function(jKey, jValue) {
                    if (iValue[2].indexOf(jValue[2]) === 0) {
                        jValue['children'].push(iValue);
                        return false;
                    }
                });
            });
            delete tmp[i];
        }
        var rootNode = tmp[0][0];
        var li = eccube.fileManager.buildDirectoryNode(rootNode['name'], rootNode['path'], rootNode['children'], openFolder);
        eccube.fileManager.tree = li.html();
        $('#' + view_id).html(li);

        /** FIXME: will be remove comment when solution accepted
         var id, level, old_id, old_level, tmp_level, sort_no_img, display, arrFileSplit, file_name, folder_img;

         for(var i = 0; i < arrTree.length; i++) {
            id = arrTree[i][0];
            level = arrTree[i][3];

            if(i === 0) {
                old_id = "0";
                old_level = 0;
            } else {
                old_id = arrTree[i-1][0];
                old_level = arrTree[i-1][3];
            }

            // 階層上へ戻る
            if(level <= (old_level - 1)) {
                tmp_level = old_level - level;
                for(var up_roop = 0; up_roop <= tmp_level; up_roop++) {
                    eccube.fileManager.tree += '</label>';
                }
            }

            // 同一階層で次のフォルダへ
            if(id !== old_id && level === old_level) {
                eccube.fileManager.tree += '</label>';
            }

            // // 階層の分だけスペースを入れる
            // for(var space_cnt = 0; space_cnt < arrTree[i][3]; space_cnt++) {
            //     eccube.fileManager.tree += "&nbsp;&nbsp;&nbsp;";
            // }

            // 階層画像の表示・非表示処理
            // if(arrTree[i][4]) {
            //     if(arrTree[i][1] === '_parent') {
            //         sort_no_img = eccube.fileManager.IMG_MINUS;
            //     } else {
            //         sort_no_img = eccube.fileManager.IMG_NORMAL;
            //     }
            //     // 開き状態を保持
            //     eccube.fileManager.arrTreeStatus.push(arrTree[i][2]);
            //     display = 'block';
            // } else {
            //     if(arrTree[i][1] === '_parent') {
            //         sort_no_img = eccube.fileManager.IMG_PLUS;
            //     } else {
            //         sort_no_img = eccube.fileManager.IMG_NORMAL;
            //     }
            //     display = 'none';
            // }

          arrFileSplit = arrTree[i][2].split("/");

            file_name = arrFileSplit[arrFileSplit.length-1];
          file_name = file_name ? file_name : 'user_data';

          // フォルダの画像を選択
            if(arrTree[i][2] === openFolder) {
                //folder_img = eccube.fileManager.IMG_FOLDER_OPEN;
              folder_img = 'collapsed';
            } else {
              folder_img = 'collapse';
                //folder_img = eccube.fileManager.IMG_FOLDER_CLOSE;
            }

            // 階層画像に子供がいたらオンクリック処理をつける
            // if(sort_no_img !== eccube.fileManager.IMG_NORMAL) {
            //     eccube.fileManager.tree += '<a href="javascript:eccube.fileManager.toggleTreeMenu(\'tree'+ i +'\',\'sort_no_img'+ i +'\',\''+ arrTree[i][2] +'\')"><span id="sort_no_img'+ i +'">' + sort_no_img + '</span>';
            // } else {
            //     eccube.fileManager.tree += '<span id="sort_no_img'+ i +'">' + sort_no_img + '</span>';

            // }
            eccube.fileManager.tree += '<li><label class="collapsed" data-toggle="collapse" href="#directory_userdata" aria-expanded="false" aria-controls="directory_userdata"><a href="javascript:eccube.fileManager.openFolder(\''+ arrTree[i][2] +'\')">' + file_name +'</a></label></li>';
            // eccube.fileManager.tree += '<label id="tree'+ i +'" class="' + folder_img + '">';

        }
         document.getElementById(view_id).innerHTML = eccube.fileManager.tree;

         */
    };

    // build directory node
    eccube.fileManager.buildDirectoryNode = function(name, path, children, currentPath) {
        var ul = $('<ul></ul>'),
            li = $('<li></li>'),
            label = $('<label></label>'),
            a = $('<a href="#"></a>');
        currentPath = currentPath || '';

        a.html(name);
        a.on('click', function(e) {
            eccube.fileManager.openFolder(path);
            return e.preventDefault();
        });
        a.appendTo(label);

        label.attr('data-toggle', 'collapse');
        label.attr('href', '#' + path.replace('/', '_'));
        label.attr('aria-expanded', false);
        label.attr('aria-control', '');
        label.appendTo(li);

        if (currentPath.indexOf(path) !== 0) {
            label.addClass('collapsed')
        }

        if (children.length) {
            if (currentPath.indexOf(path) !== 0) {
                ul.addClass('collapse list-unstyled');
            } else {
                ul.addClass('collapsed list-unstyled');
            }

            ul.attr('id', path.replace('/', '_'));
            $.each(children, function(k, v) {
                var li = eccube.fileManager.buildDirectoryNode(v['name'], v['path'], v['children'], currentPath);
                li.appendTo(ul);
            });
            ul.appendTo(li);
        }

        return li;
    };

    // Tree状態をhiddenにセット
    eccube.fileManager.setTreeStatus = function(name) {
        var tree_status = "";
        for (var i = 0; i < eccube.fileManager.arrTreeStatus.length; i++) {
            if (i !== 0) {
                tree_status += '|';
            }
            tree_status += eccube.fileManager.arrTreeStatus[i];
        }
        document.form1[name].value = tree_status;
    };

    // 階層ツリーメニュー表示・非表示処理
    eccube.fileManager.toggleTreeMenu = function(tName, imgName, path) {
        var tMenu = $("#" + tName);

        if (tMenu.css("display") === 'none') {
            $("#" + imgName).html(eccube.fileManager.IMG_MINUS);
            tMenu.show();
            // 階層の開いた状態を保持
            eccube.fileManager.arrTreeStatus.push(path);
        } else {
            $("#" + imgName).html(eccube.fileManager.IMG_PLUS);
            tMenu.hide();
            // 閉じ状態を保持
            eccube.fileManager.fnDelTreeStatus(path);
        }
    };

    // Tree状態を削除する(閉じる状態へ)
    eccube.fileManager.deleteTreeStatus = function(path) {
        for (var i = 0; i < eccube.fileManager.arrTreeStatus.length; i++) {
            if (eccube.fileManager.arrTreeStatus[i] === path) {
                eccube.fileManager.arrTreeStatus[i] = "";
            }
        }
    };

    // ファイルリストダブルクリック処理
    eccube.fileManager.doubleClick = function(arrTree, path, is_dir, now_dir, is_parent) {
        if (is_dir) {
            if (!is_parent) {
                for (var cnt = 0; cnt < arrTree.length; cnt++) {
                    if (now_dir === arrTree[cnt][2]) {
                        var open_flag = false;
                        for (var status_cnt = 0; status_cnt < eccube.fileManager.arrTreeStatus.length; status_cnt++) {
                            if (eccube.fileManager.arrTreeStatus[status_cnt] === arrTree[cnt][2]) {
                                open_flag = true;
                            }
                        }
                        if (!open_flag) {
                            eccube.fileManager.toggleTreeMenu('tree' + cnt, 'sort_no_img' + cnt, arrTree[cnt][2]);
                        }
                    }
                }
            }
            eccube.fileManager.openFolder(path);
        } else {
            // Download
            eccube.setModeAndSubmit('download', '', '');
        }
    };

    // フォルダオープン処理
    eccube.fileManager.openFolder = function(path) {
        // クリックしたフォルダ情報を保持
        document.form1[eccube.fileManager.selectFileHidden].value = path;
        // treeの状態をセット
        eccube.fileManager.setTreeStatus(eccube.fileManager.treeStatusHidden);
        // submit
        eccube.setModeAndSubmit(eccube.fileManager.modeHidden, '', '');
    };

    // ファイル選択
    eccube.fileManager.selectFile = function(id) {
        eccube.fileManager.old_select_id = id;
    };

    eccube.navi = {};

    // クリックを無視するフラグ
    eccube.navi.ignore_click = false;

    /**
     * 対象を指定してメニューを開く
     */
    eccube.navi.openMenu = function($target) {
        $target
        // 対象を開く
            .addClass('sfhover')
            // 対象以外を閉じる
            .siblings('li')
            .removeClass('sfhover')
            .find('li').removeClass('sfhover');
    };

    /**
     * 全てのメニューを閉じる
     */
    eccube.navi.closeAllMenu = function() {
        $("#navi")
            .removeClass('active')
            .find('li')
            .removeClass('sfhover');
    };

    /**
     * 一時的にクリックイベントを無視する
     */
    eccube.navi.setIgnoreClick = function(milliseconds) { // FIXME: 関数名
        if (milliseconds === null) milliseconds = 100;
        eccube.navi.ignore_click = true;
        setTimeout(function() {
            eccube.navi.ignore_click = false;
        }, milliseconds);
    };

    // グローバルに使用できるようにする
    window.eccube = eccube;
    eccube.defaults = {
        formId: 'form1',
        windowFeatures: {
            scrollbars: 'yes',
            resizable: 'yes',
            toolbar: 'no',
            location: 'no',
            directories: 'no',
            status: 'no',
            focus: true,
            formTarget: ''
        }
    };

    eccube.setModeAndSubmit = function(mode, keyname, keyid) {
        switch (mode) {
            case 'delete_category':
                if (!window.confirm('選択したカテゴリとカテゴリ内の全てのカテゴリを削除します')) {
                    return;
                }
                break;
            case 'delete':
                if (!window.confirm('一度削除したデータは、元に戻せません。\n削除しても宜しいですか？')) {
                    return;
                }
                break;
            case 'confirm':
                if (!window.confirm('登録しても宜しいですか')) {
                    return;
                }
                break;
            case 'delete_all':
                if (!window.confirm('検索結果を全て削除しても宜しいですか')) {
                    return;
                }
                break;
            default:
                break;
        }
        document.form1.mode.value = mode;
        if (keyname !== undefined && keyname !== "" && keyid !== undefined && keyid !== "") {
            document.form1[keyname].value = keyid;
        }
        document.form1.submit();
    };

    eccube.setValueAndSubmit = function(form, key, val, msg) {
        var ret;
        if (msg !== undefined) {
            ret = window.confirm(msg);
        } else {
            ret = true;
        }
        if (ret) {
            var values = {};
            values[key] = val;
            eccube.submitForm(values, form);
        }
        return false;
    };

    eccube.setValue = function(key, val, form) {
        var formElement = eccube.getFormElement(form);
        formElement.find("*[name=" + key + "]").val(val);
    };

    eccube.getFormElement = function(form) {
        var formElement;
        if (form !== undefined && typeof form === "string" && form !== "") {
            formElement = $("form#" + form);
        } else if (form !== undefined && typeof form === "object") {
            formElement = form;
        } else {
            formElement = $("form#" + eccube.defaults.formId);
        }
        return formElement;
    };

    eccube.openWindow = function(URL, name, width, height, option) {
        var features = "width=" + width + ",height=" + height;
        if (option === undefined) {
            option = eccube.defaults.windowFeatures;
        } else {
            option = $.extend(eccube.defaults.windowFeatures, option);
        }
        features = features + ",scrollbars=" + option.scrollbars +
            ",resizable=" + option.resizable +
            ",toolbar=" + option.toolbar +
            ",location=" + option.location +
            ",directories=" + option.directories +
            ",status=" + option.status;
        if (option.hasOwnProperty('menubar')) {
            features = features + ",menubar=" + option.menubar;
        }
        var WIN = window.open(URL, name, features);
        if (option.formTarget !== "") {
            document.forms[option.formTarget].target = name;
        }
        if (option.focus) {
            WIN.focus();
        }
    };

    eccube.fileManager.fnDelTreeStatus = function(path) {
        eccube.fileManager.deleteTreeStatus(path);
    };

    // TODO 仮実装
    eccube.fileManager.convertToHierarchy = function(paths /* array of array of strings */) {
        // Build the node structure
        var rootNode = {name: "root", children: []};
        var $rootNode = $('<div />');


        for (var i = 0; i < paths.length; i++) {
            eccube.fileManager.buildNodeRecursive(
                rootNode,
                $rootNode,
                paths[i].replace(/^\//, 'user_data/')
                    .replace(/\/$/, '')
                    .split('/'),
                0
            );
        }
        ;
        console.log(rootNode);
        return $rootNode;
    };

    // TODO 仮実装
    eccube.fileManager.buildNodeRecursive = function(node, $node, path, idx) {
        if (idx < path.length) {
            let $item = $('<li>' + path[idx] + '</li>');
            let item = path[idx];
            let $dir = $node.find('ul');
            let dir = node.children.find(function(child) {
                return child.name == item;
            });
            if (!dir) {
                node.children.push(dir = {name: item, children: []});
            }

            if (!$dir.length) {
                $dir = $('<ul />').append($item);
                console.log($dir);
                $node.append($dir);
            }

            eccube.fileManager.buildNodeRecursive(dir, $dir, path, idx + 1);
        }
    };

    /**
     * Initialize.
     */
    $(function() {
        var naviClicked = false;
        // ヘッダナビゲーション
        $("#navi").find("div").click(function() {
            // タブレットでの二重イベント発生を回避
            if (eccube.navi.ignore_click) return false;

            naviClicked = true;
            $("#navi").addClass('active');

            var parent = $(this).parent('li');

            // 開閉を切り替え.
            if (!parent.hasClass('sfhover')) {
                eccube.navi.openMenu(parent);
            } else {
                if (parent.hasClass('on_level1')) {
                    eccube.navi.closeAllMenu();
                } else {
                    parent.removeClass('sfhover');
                }
            }
        });
        // ナビゲーションがアクティブであれば、マウスオーバーを有効に.
        $("#navi").find('li').hover(function() {
            if ($("#navi").hasClass('active')) {
                eccube.navi.openMenu($(this));
                eccube.navi.setIgnoreClick();
            }
        });
        // ナビゲーション以外をクリックしたらナビを閉じる.
        $(document).click(function() {
            if (!naviClicked) {
                eccube.navi.closeAllMenu();
            } else {
                naviClicked = false;
            }
        });
    });
})(window);

(function($) {
    /**
     * パンくず
     */
    var o;

    $.fn.breadcrumbs = function(options) {
        var defaults = {
            bread_crumbs: '',
            start_node: '<span>ホーム</span>',
            anchor_node: '<a onclick="eccube.setModeAndSubmit(\'tree\', \'parent_category_id\', ' +
            '{category_id}); return false" href="javascript:;" />',
            delimiter_node: '<span>&nbsp;/;&nbsp;</span>'
        };

        return this.each(function() {
            if (options) {
                o = $.fn.extend(defaults, options);
            }
            var $this = $(this);
            var total = o.bread_crumbs.length;
            var $node = $(o.start_node);

            for (var i = total - 1; i >= 0; i--) {
                if (i === total - 1) {
                    $node.append(o.delimiter_node);
                }

                var anchor = o.anchor_node.replace(/{category_id}/ig, o.bread_crumbs[i].category_id);
                $(anchor)
                    .text(o.bread_crumbs[i].category_name)
                    .appendTo($node);

                if (i > 0) {
                    $node.append(o.delimiter_node);
                }
            }
            $this.html($node);
            return this;
        });
    };
})(jQuery);
