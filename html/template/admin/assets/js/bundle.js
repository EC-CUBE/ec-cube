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

require('jquery-ui/themes/base/all.css');
// jQuery UI の各モジュールは UMD の AMD 分岐で内部依存を解決しているが、
// esbuild は AMD を解釈しないため依存が読み込まれない。必要なものを依存順に明示する。
require('jquery-ui/ui/version');
require('jquery-ui/ui/position');
require('jquery-ui/ui/widget');
require('jquery-ui/ui/widgets/mouse');
require('jquery-ui/ui/disable-selection');
require('jquery-ui/ui/plugin');
require('jquery-ui/ui/widgets/resizable');
require('jquery-ui/ui/data');
require('jquery-ui/ui/scroll-parent');
require('jquery-ui/ui/widgets/sortable');
require('jquery-ui/ui/keycode');
require('jquery-ui/ui/unique-id');
require('jquery-ui/ui/widgets/tooltip');

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
