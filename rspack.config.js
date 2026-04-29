const path = require('path');
const rspack = require('@rspack/core');

module.exports = {
  mode: 'production',
  cache: {
    type: 'filesystem',
    buildDependencies: {
      config: [__filename],
    },
  },
  entry: {
    front: './html/template/default/assets/js/bundle.js',
    admin: './html/template/admin/assets/js/bundle.js',
    install: './html/template/install/assets/js/bundle.js'
  },
  devtool: 'source-map',
  output: {
    path: path.resolve(__dirname, 'html/bundle'),
    filename: '[name].bundle.js',
    clean: false
  },
  resolve: {
    extensions: ['.js'],
    alias: {
      jquery: path.join(__dirname, 'node_modules', 'jquery')
    }
  },
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      },
      {
        test: /\.(png|jpe?g|svg|gif|eot|woff2?|ttf)$/,
        type: 'asset/inline'
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: 'builtin:swc-loader',
          options: {
            jsc: {
              parser: { syntax: 'ecmascript' },
              target: 'es2017'
            }
          }
        }
      }
    ]
  },
  plugins: [
    new rspack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery'
    })
  ]
};
