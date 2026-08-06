// EC-CUBE フロントエンドビルド (JavaScript バンドル + SCSS コンパイル)
//
//   npm run build   … 一括ビルド
//   npm start       … ビルド後にファイルを監視して差分ビルド
//
// JavaScript は esbuild でバンドルする。CSS は従来の style-loader と同じく
// JavaScript から <style> として注入するため、テンプレート側の変更は不要。
import * as esbuild from 'esbuild'
import * as sass from 'sass-embedded'
import postcss from 'postcss'
import autoprefixer from 'autoprefixer'
import sortMediaQueries from 'postcss-sort-media-queries'
import fs from 'node:fs'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const rootDir = path.dirname(fileURLToPath(import.meta.url))
const templateDir = path.join(rootDir, 'html/template')
const bundleDir = path.join(rootDir, 'html/bundle')
const aceSrcDir = path.join(rootDir, 'node_modules/ace-builds/src-min-noconflict')
const aceDistDir = path.join(bundleDir, 'ace')

const isWatch = process.argv.includes('--watch')

// 画像・フォントは data URI としてバンドルに埋め込む (url-loader 相当)
const assetLoader = {
  '.png': 'dataurl',
  '.jpg': 'dataurl',
  '.jpeg': 'dataurl',
  '.gif': 'dataurl',
  '.svg': 'dataurl',
  '.eot': 'dataurl',
  '.woff': 'dataurl',
  '.woff2': 'dataurl',
  '.ttf': 'dataurl',
}

// ace エディタは mode / theme / worker を実行時に basePath から読み込む。
// webpack-resolver (file-loader) の代替として、実際に使うものだけを静的配置する。
// 対象は管理画面テンプレートで指定しているモードから決定している。
//   mode-twig       … ページ管理・ブロック編集・レイアウト管理
//   mode-css        … CSS 管理
//   mode-javascript … JavaScript 管理
// worker は mode-twig が javascript / css / html の 3 つを参照するため同梱する。
const aceFiles = [
  'mode-css.js',
  'mode-javascript.js',
  'mode-twig.js',
  'theme-tomorrow.js',
  'worker-css.js',
  'worker-html.js',
  'worker-javascript.js',
  'ext-searchbox.js',
  'snippets/css.js',
  'snippets/html.js',
  'snippets/javascript.js',
  'snippets/text.js',
  'snippets/twig.js',
]

/**
 * CSS を JavaScript から <style> として注入するプラグイン (style-loader 相当)。
 * CSS 内の @import と url() は esbuild の CSS バンドラで先に解決しておく。
 */
const styleInjectPlugin = {
  name: 'style-inject',
  setup(build) {
    build.onLoad({ filter: /\.css$/ }, async (args) => {
      const bundled = await esbuild.build({
        entryPoints: [args.path],
        bundle: true,
        minify: true,
        write: false,
        logLevel: 'silent',
        loader: assetLoader,
      })
      const css = bundled.outputFiles.map((file) => file.text).join('')

      return {
        contents: `var style = document.createElement('style');`
          + `style.textContent = ${JSON.stringify(css)};`
          + `document.head.appendChild(style);`,
        loader: 'js',
      }
    })
  },
}

const jsBuildOptions = {
  entryPoints: {
    front: 'html/template/default/assets/js/bundle.js',
    admin: 'html/template/admin/assets/js/bundle.js',
    install: 'html/template/install/assets/js/bundle.js',
  },
  outdir: bundleDir,
  entryNames: '[name].bundle',
  bundle: true,
  minify: true,
  sourcemap: true,
  target: ['es2018'],
  // CommonJS 形式のライブラリが参照する global を window に読み替える
  define: { global: 'window' },
  loader: assetLoader,
  plugins: [styleInjectPlugin],
  logLevel: 'info',
}

/** ace の mode / theme / worker を html/bundle/ace へ配置する */
function copyAceFiles() {
  fs.rmSync(aceDistDir, { recursive: true, force: true })

  for (const file of aceFiles) {
    const src = path.join(aceSrcDir, file)
    const dist = path.join(aceDistDir, file)
    fs.mkdirSync(path.dirname(dist), { recursive: true })
    fs.copyFileSync(src, dist)
  }
}

/** html/template 配下の SCSS エントリを列挙する (_ 始まりは部分ファイルなので除く) */
function findScssEntries(dir) {
  const entries = []

  for (const dirent of fs.readdirSync(dir, { withFileTypes: true })) {
    const fullPath = path.join(dir, dirent.name)

    if (dirent.isDirectory()) {
      entries.push(...findScssEntries(fullPath))
    } else if (dirent.name.endsWith('.scss') && !dirent.name.startsWith('_')) {
      entries.push(fullPath)
    }
  }

  return entries
}

/** SCSS の出力先 (assets/scss/foo.scss -> assets/css/foo.css) */
function toCssPath(scssPath, minify) {
  const dir = path.dirname(scssPath).replace(/scss$/, 'css')
  const name = path.basename(scssPath, '.scss')

  return path.join(dir, minify ? `${name}.min.css` : `${name}.css`)
}

const postcssProcessor = postcss([
  autoprefixer(),
  sortMediaQueries({ sort: 'mobile-first' }),
])

/** SCSS を 1 ファイル分コンパイルして CSS と source map を書き出す */
async function buildScssFile(scssPath, minify) {
  const cssPath = toCssPath(scssPath, minify)
  const mapName = `${path.basename(cssPath)}.map`
  const compiled = await sass.compileAsync(scssPath, {
    style: minify ? 'compressed' : 'expanded',
    sourceMap: true,
    sourceMapIncludeSources: true,
  })
  const processed = await postcssProcessor.process(compiled.css, {
    from: scssPath,
    to: cssPath,
    map: { prev: compiled.sourceMap, inline: false, annotation: mapName },
  })

  fs.mkdirSync(path.dirname(cssPath), { recursive: true })
  fs.writeFileSync(cssPath, processed.css)

  if (processed.map) {
    const mapDir = path.dirname(cssPath)
    const map = processed.map.toJSON()
    // sass-embedded は sources を file:// の絶対 URL で返す。生成物をコミットする運用では
    // ビルドマシンのパスが焼き込まれ、誰が再ビルドしても map だけが差分化するため、
    // map ファイルからの相対パス (区切りは POSIX 固定) に直してから書き出す。
    map.sources = map.sources.map((src) => {
      const abs = src.startsWith('file://') ? fileURLToPath(src) : src

      return path.isAbsolute(abs) ? path.relative(mapDir, abs).split(path.sep).join('/') : abs
    })
    fs.writeFileSync(path.join(mapDir, mapName), JSON.stringify(map))
  }
}

async function buildScss() {
  const entries = findScssEntries(templateDir)

  await Promise.all(
    entries.flatMap((entry) => [buildScssFile(entry, false), buildScssFile(entry, true)])
  )

  return entries
}

/** SCSS を含むディレクトリを再帰的に集める (監視対象) */
function findScssDirs(dir) {
  const dirs = []
  let hasScss = false

  for (const dirent of fs.readdirSync(dir, { withFileTypes: true })) {
    if (dirent.isDirectory()) {
      dirs.push(...findScssDirs(path.join(dir, dirent.name)))
    } else if (dirent.name.endsWith('.scss')) {
      hasScss = true
    }
  }

  if (hasScss) {
    dirs.push(dir)
  }

  return dirs
}

async function main() {
  copyAceFiles()

  const started = Date.now()
  const [context] = await Promise.all([
    isWatch ? esbuild.context(jsBuildOptions) : null,
    buildScss(),
  ])

  if (!isWatch) {
    await esbuild.build(jsBuildOptions)
    console.log(`build finished in ${Date.now() - started}ms`)

    return
  }

  await context.rebuild()
  await context.watch()
  console.log('watching javascript...')

  // SCSS は esbuild の管理外なので個別に監視する
  let timer = null
  for (const dir of findScssDirs(templateDir)) {
    fs.watch(dir, (_event, filename) => {
      if (!filename || !filename.endsWith('.scss')) {
        return
      }

      // 保存直後は同じファイルに対して複数回通知されるためまとめて処理する
      clearTimeout(timer)
      timer = setTimeout(() => {
        buildScss()
          .then(() => console.log(`scss rebuilt (${filename})`))
          .catch((e) => console.error(e))
      }, 100)
    })
  }

  console.log('watching scss...')
}

main().catch((e) => {
  console.error(e)
  process.exit(1)
})
