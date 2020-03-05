const path = require('path');
const webpack = require('webpack');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
  mode: 'production',
  entry: path.resolve(__dirname, 'app/assets/src/js', 'main.js'),
  output: {
    path: path.resolve(__dirname, 'app/assets/dist'),
    filename: 'js/main.js'
  },
  module: {
    rules: [
      {
        test: /\.s[ac]ss$/i,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: path.resolve(__dirname, 'app/assets/dist/css')
            }
          },
          // add optimization
          {
            loader: 'css-loader',
            options: {
              sourceMap: true
            }
          },
          // add postcss-loader
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true
            }
          },
        ],
      },
    ],
  },
  devtool: 'source-map',
  plugins: [
    new MiniCssExtractPlugin({
      filename: 'css/[name].css'
    }),
  ]
};