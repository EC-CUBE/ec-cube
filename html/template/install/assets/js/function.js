/*!
 * function.js for EC-CUBE install
 */

document.addEventListener('DOMContentLoaded', function() {

    /*
     * Brake point Check
     */
    function applyBreakpoint() {
        if (window.innerWidth < 768) {
            document.body.classList.add('sp_view');
            document.body.classList.remove('md_view', 'pc_view');
            var wrapper = document.getElementById('wrapper');
            if (wrapper) wrapper.classList.remove('sidebar-open');
        } else if (window.innerWidth < 992) {
            document.body.classList.remove('sp_view', 'pc_view');
            document.body.classList.add('md_view');
            var wrapper = document.getElementById('wrapper');
            if (wrapper) wrapper.classList.add('sidebar-open');
        } else {
            document.body.classList.remove('sp_view', 'md_view');
            document.body.classList.add('pc_view');
            var wrapper = document.getElementById('wrapper');
            if (wrapper) wrapper.classList.add('sidebar-open');
        }
    }
    window.addEventListener('load', applyBreakpoint);
    window.addEventListener('resize', applyBreakpoint);

    /////////// accordion
    document.querySelectorAll('.accordion .toggle').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            var panel = this.nextElementSibling;
            while (panel && !panel.classList.contains('accpanel')) {
                panel = panel.nextElementSibling;
            }
            if (!panel) return;
            if (panel.style.display === 'none' || panel.style.display === '') {
                this.classList.add('active');
                panel.style.display = 'block';
            } else {
                this.classList.remove('active');
                panel.style.display = 'none';
            }
        });
    });

    /////////// dropdownの中をクリックしても閉じないようにする
    document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    /////////// database choice
    var hideParameters = function() {
        document.querySelectorAll('.required').forEach(function(el) { el.style.display = 'none'; });
        [
            '#install_step4_database_host',
            '#install_step4_database_port',
            '#install_step4_database_name',
            '#install_step4_database_user',
            '#install_step4_database_password'
        ].forEach(function(sel) {
            var el = document.querySelector(sel);
            if (el) el.setAttribute('disabled', 'disabled');
        });
    };

    var showParameters = function() {
        document.querySelectorAll('.required').forEach(function(el) { el.style.display = ''; });
        [
            '#install_step4_database_host',
            '#install_step4_database_port',
            '#install_step4_database_name',
            '#install_step4_database_user',
            '#install_step4_database_password'
        ].forEach(function(sel) {
            var el = document.querySelector(sel);
            if (el) el.removeAttribute('disabled');
        });
    };

    var dbSelect = document.getElementById('install_step4_database');
    if (dbSelect) {
        if (dbSelect.value === 'pdo_sqlite') {
            hideParameters();
        } else {
            showParameters();
        }
        dbSelect.addEventListener('change', function() {
            if (this.value === 'pdo_sqlite') {
                hideParameters();
            } else {
                showParameters();
            }
        });
    }

    /////////// 特定の条件下でのみ入力を許可する
    // ロードバランサー、プロキシ設定
    document.querySelectorAll("[name*='[trusted_proxies_connection_only]']").forEach(function(el) {
        el.addEventListener('change', function() {
            document.querySelectorAll("[name*='[trusted_proxies]']").forEach(function(input) {
                if (el.checked) {
                    input.setAttribute('readonly', 'readonly');
                } else {
                    input.removeAttribute('readonly');
                }
            });
        });
    });
});
