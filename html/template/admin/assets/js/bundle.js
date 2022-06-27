const $ = require('jquery');
global.$ = global.jQuery = $;

const { Spinner } = require('spin.js');
global.Spinner = Spinner;

require('ace-builds/src-min-noconflict/ace');
require('ace-builds/src-min-noconflict/ext-language_tools');
require('ace-builds/webpack-resolver');

require('jquery.qrcode');

require('jquery-ui/themes/base/all.css');
require('jquery-ui/ui/core');
require('jquery-ui/ui/position');
require('jquery-ui/ui/widget');
require('jquery-ui/ui/widgets/mouse');
require('jquery-ui/ui/widgets/resizable');
require('jquery-ui/ui/widgets/sortable');
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
global.Chart = Chart;

require('ladda/dist/ladda-themeless.min.css');
const Ladda = require('ladda');
global.Ladda = Ladda;

const bootstrap = require('bootstrap');
global.bootstrap = bootstrap;

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
global.FilePond = FilePond;
