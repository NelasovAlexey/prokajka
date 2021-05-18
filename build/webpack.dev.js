const url = require('url');
const webpack = require('webpack');
const { merge } = require('webpack-merge');

// config
let webpackCommon = require('./webpack.common');
const config = require('./config');

const target = process.env.DEVURL || config.devUrl;

/**
 * We do this to enable injection over SSL.
 */
if (url.parse(target).protocol === 'https:') {
  process.env.NODE_TLS_REJECT_UNAUTHORIZED = 0;
}

if (url.parse(target).path !== '/') {
  config.proxyUrl += url.parse(target).path;
}

let webpackDev = {
  mode: 'development',
  output: {
    pathinfo: true,
    publicPath: config.proxyUrl + config.publicPath,
  },
  devtool: '#cheap-module-source-map',
  stats: false,
  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: [/(node_modules)(?![/|\\](bootstrap|foundation-sites))/],
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
    ],
  },
  plugins: [
    // new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.HotModuleReplacementPlugin(),
    new webpack.NoEmitOnErrorsPlugin(),
  ],
};

webpackCommon = merge(webpackCommon, webpackDev);

webpackCommon.entry = require('./util/addHotMiddleware')(webpackCommon.entry);

module.exports = function () {
  return webpackCommon;
};
