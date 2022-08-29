let config = {}

config.paths = {
  source: {
    template: './html/template',
  },
  output: {
    template: './html/template',
  },
  assets: {
    scss: '/**/scss/**/*.scss',
    js: '/**/js/bundle.js'
  },
}
config.server = 'http://127.0.0.1:8080/'

module.exports = config
