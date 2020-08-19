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
  },
}
config.server = 'http://localhost:8080'

module.exports = config
