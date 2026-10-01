window.$ = window.jQuery = require('jquery');

const { Spinner } = require('spin.js');
window.Spinner = Spinner;

require('ace-builds/src-min-noconflict/ace');
require('ace-builds/src-min-noconflict/ext-language_tools');
// mode / theme / worker は html/bundle/ace に静的配置している。
// サブディレクトリに設置した場合でも解決できるよう、このバンドル自身の URL を基準にする。
const bundleSrc = document.currentScript ? document.currentScript.src : window.location.href;
const acePath = new URL('ace/', bundleSrc).href;
// ace は読み込み時に自身の置き場 (= このバンドルの位置) を basePath だけでなく
// modePath / themePath / workerPath にも入れる。config.moduleUrl() は
// options[component + 'Path'] を basePath より優先するため、basePath だけ上書きしても
// mode / theme / worker は html/bundle 直下を見て 404 になる。4 つとも設定する。
// snippetsPath は設定しないこと: 設定すると moduleUrl() が component と区切り文字を落とし、
// ace/snippets/foo が ace/foo.js に解決されて逆に壊れる (未設定なら basePath へフォールバックする)。
['basePath', 'modePath', 'themePath', 'workerPath'].forEach((key) => window.ace.config.set(key, acePath));

require('jquery.qrcode');

// 並び替え (ドラッグ & ドロップ) は SortableJS を使う。jQuery UI は撤去した (#6943)。
// 管理画面のテンプレートとプラグインから window.Sortable で参照する。
// package.json の module フィールド経由で ESM 版が解決されるため default を取り出す。
const SortableModule = require('sortablejs');
window.Sortable = SortableModule.default || SortableModule;

// jQuery UI の $.fn.sortable / $.fn.resizable に依存するプラグイン向けの互換 shim (非推奨、4.5 で削除)。
// window.Sortable の後に読み込むこと。
require('./jquery-ui-compat');

const {
    Chart,
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip,
    SubTitle
} = require('chart.js');
Chart.register(
    ArcElement,
    LineElement,
    BarElement,
    PointElement,
    BarController,
    BubbleController,
    DoughnutController,
    LineController,
    PieController,
    PolarAreaController,
    RadarController,
    ScatterController,
    CategoryScale,
    LinearScale,
    LogarithmicScale,
    RadialLinearScale,
    TimeScale,
    TimeSeriesScale,
    Decimation,
    Filler,
    Legend,
    Title,
    Tooltip,
    SubTitle
);
window.Chart = Chart;

require('ladda/dist/ladda-themeless.min.css');
const Ladda = require('ladda');
window.Ladda = Ladda;

const bootstrap = require('bootstrap');
window.bootstrap = bootstrap;

require('filepond/dist/filepond.min.css');
require('filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css');
const FilePondPluginImagePreview = require('filepond-plugin-image-preview/dist/filepond-plugin-image-preview');
const FilePondPluginFileValidateType = require('filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type');
const FilePondPluginFileValidateSize = require('filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size');
const FilePond = require('filepond');
FilePond.registerPlugin(
    FilePondPluginImagePreview,
    FilePondPluginFileValidateType,
    FilePondPluginFileValidateSize
);
window.FilePond = FilePond;
const FilePondLocale_en = require('filepond/locale/en-en.js');
window.FilePondLocale_en = FilePondLocale_en.default;
const FilePondLocale_ja = require('filepond/locale/ja-ja.js');
window.FilePondLocale_ja = FilePondLocale_ja.default;
