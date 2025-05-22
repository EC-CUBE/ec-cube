const path = require('path');
const webpack = require('webpack');

module.exports = {
  mode: 'production',
  entry: {
    front: './html/template/default/assets/js/bundle.js',
    admin: './html/template/admin/assets/js/bundle.js',
    install: './html/template/install/assets/js/bundle.js'
  },
  devtool: 'source-map',
  output: {
    path: path.resolve(__dirname, 'html/bundle'),
    filename: '[name].bundle.js'
  },
  resolve: {
    alias: {
      jquery: path.join(__dirname, 'node_modules', 'jquery')
    }
  },
  module: {
    rules: [
      {
        test: /\.css/,
        use: [
          'style-loader',
          {
            loader: 'css-loader'
          }
        ],
      },
      {
        test: /\.png|jpg|svg|gif|eot|wof|woff|ttf$/,
        use: ['url-loader']
      },
      {
        test: /\.js$/,
        use: [
          {
            loader: 'babel-loader',
            options: {
              presets: ['@babel/preset-env']
            }
          }
        ],
        exclude: /node_modules/
      }
    ]
  },
  plugins: [
    new webpack.ProvidePlugin({
      $: "jquery",
      jQuery: "jquery",
      "window.jQuery": "jquery"
    })
  ]
};
