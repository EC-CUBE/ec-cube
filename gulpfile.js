/*
 * モジュール
 */
// common
const gulp = require('gulp');
const plumber = require('gulp-plumber');
const notify = require('gulp-notify');
const rename = require('gulp-rename');
const process = require('process');
// server
const browserSync = require('browser-sync')
// sass
const sass = require('gulp-sass');
const postcss = require('gulp-postcss');
const atImport = require('postcss-import');
const autoprefixer = require('autoprefixer');
const cssmqpacker = require('css-mqpacker');
const sortCSSmq = require('sort-css-media-queries');
const cleanCSS = require('gulp-clean-css');

/*
 * 変数
 */
// gulpconfig 存在しない場合のフォールバック
try {
  var config = require('./gulpconfig');
} catch (e) {
  var config = {
    paths: {
      source: {
        template: './html/template',
      },
      output: {
        template: './html/template',
      },
      assets: {
        scss: '/**/scss/**/*.scss',
      },
    }
  }
}

/*
 * config 存在判定
 */
const configDecisionTask = (done) => {
  if (!config.server) {
    console.log('\033[31m' + 'Error:'+ '\033[39m' + ' gulpconfig.jsが設定されていません。');
    console.log('\033[31m' + 'Error:'+ '\033[39m' + ' タスクを終了します。');
    throw new Error();
  }
  done();
};

/*
 * Browsersync サーバー
 */
const BrowsersyncTask = (done) => {
  browserSync({
    proxy: config.server
  })
  done()
};

/*
 * sass
 */
const sassSource = config.paths.source.template + config.paths.assets.scss;
const sassOutput = config.paths.output.template;

// scssのコンパイル
const sassTask = () => {
  return gulp
    .src([sassSource], {
      sourcemaps: true,
      base: config.paths.output.template
    })
    .pipe(plumber({
      errorHandler: notify.onError('Error: <%= error.message %>')
    }))
    .pipe(sass())
    .pipe(postcss([
      atImport(),
      autoprefixer(),
      cssmqpacker({
        sort: sortCSSmq
      }),
    ]))
    .pipe(rename((path) => {
      path.dirname = path.dirname.replace('/scss', '/css')
    }))
    .pipe(gulp.dest(sassOutput, {sourcemaps: '.'}))
    .pipe(browserSync.stream());
};

// scssのコンパイル minify化
const sassCssMinifyTask = () => {
  return gulp
    .src([sassSource], {
      sourcemaps: true,
      base: config.paths.output.template
    })
    .pipe(plumber({
      errorHandler: notify.onError('Error: <%= error.message %>')
    }))
    .pipe(sass())
    .pipe(postcss([
      autoprefixer(),
      cssmqpacker({
        sort: sortCSSmq
      }),
    ]))
    .pipe(cleanCSS())
    .pipe(rename((path) => {
      path.dirname = path.dirname.replace('/scss', '/css')
      if (path.extname === '.css') path.extname = '.min.css'
    }))
    .pipe(gulp.dest(sassOutput, {sourcemaps: '.'}))
    .pipe(browserSync.stream());
};

/*
 * エクスポート
 */
exports.configDecisionTask = configDecisionTask;
exports.BrowsersyncTask = BrowsersyncTask;
exports.sassTask = sassTask;
exports.sassCssMinifyTask = sassCssMinifyTask;

/*
 * タスク
 */
// デフォルト
gulp.task('default',
  gulp.parallel(sassTask, sassCssMinifyTask)
);

// 監視
gulp.task('watch', () => {
  gulp.parallel(sassTask, sassCssMinifyTask),
  gulp.watch(
    config.paths.source.template + config.paths.assets.scss,
    gulp.parallel(sassTask, sassCssMinifyTask)
  )
});

// minifyタスクの監視
gulp.task('watch:min', gulp.series(
  sassCssMinifyTask,
  () => {
    gulp.watch(
      config.paths.source.template + config.paths.assets.scss,
      sassCssMinifyTask
    )
  }
));

// 監視 ブラウザ自動更新
gulp.task('start', gulp.series(
  configDecisionTask,
  gulp.parallel(sassTask, sassCssMinifyTask),
  BrowsersyncTask,
  () => {
    gulp.watch(
      config.paths.source.template + config.paths.assets.scss,
      sassTask
    ),
    gulp.watch(
      config.paths.source.template + config.paths.assets.scss,
      sassCssMinifyTask
    );
  }
));

// 監視 ブラウザ自動更新 minify化
gulp.task('start:min', gulp.series(
  configDecisionTask,
  sassCssMinifyTask,
  BrowsersyncTask,
  () => {
    gulp.watch(
      config.paths.source.template + config.paths.assets.scss,
      sassCssMinifyTask
    );
  }
));
