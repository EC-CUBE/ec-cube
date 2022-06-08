const $ = require('jquery');
global.$ = global.jQuery = $;

const { Spinner } = require('spin.js');
global.Spinner = Spinner;

require('ace-builds/src-min-noconflict/ace');
require('ace-builds/src-min-noconflict/ext-language_tools');
require('ace-builds/webpack-resolver');

require('jquery.qrcode');

require('jquery-ui/themes/base/all.css');
require('jquery-ui/ui/widgets/resizable');

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
