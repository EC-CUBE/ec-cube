// 既定で OFF の店舗設定を ON にしたターゲットであることを、ZAP のグローバル変数に記録する (standalone スクリプト)。
// admin_enable_options.zst の後に before_script として実行し、option_fields.js が参照する (zap/targets.json の *_options)。

var ScriptVars = Java.type('org.zaproxy.zap.extension.script.ScriptVars');
ScriptVars.setGlobalVar('eccube.options', '1');
