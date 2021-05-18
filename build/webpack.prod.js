'use strict'; // eslint-disable-line

const path = require('path');
const webpack = require('webpack');
const { merge } = require('webpack-merge');
const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const MinifyPlugin = require('babel-minify-webpack-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');
const CircularDependencyPlugin = require('circular-dependency-plugin');

// config
let webpackCommon = require('./webpack.common');
const config = require('./config');

let webpackProd = {
  module: {
    rules: [
      {
        test: /\.js$/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env'],
          },
        },
      },
    ],
  },
  optimization: {
    minimize: config.env.production,
    concatenateModules: config.env.production,
    sideEffects: config.env.production,
    runtimeChunk: 'single',
    moduleIds: 'hashed',

    splitChunks: {
      chunks: 'async',
      name: !config.env.production,

      cacheGroups: {
        vendor: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendor',
          chunks: 'all',
        },
      },
    },
  },
  plugins: [
    new CleanWebpackPlugin({
      verbose: false,
    }),
    new MinifyPlugin(),
    new webpack.LoaderOptionsPlugin({
      test: /\.js$/,
      options: {
        eslint: {
          failOnWarning: false,
          failOnError: true,
        },
      },
    }),
    new StyleLintPlugin({
      failOnError: true,
      syntax: 'scss',
    }),
    new CircularDependencyPlugin({
      exclude: /node_modules/,
      failOnError: false,
    }),
  ],
};

if (config.enabled.optimize) {
  const imageminMozjpeg = require('imagemin-mozjpeg');
  const ImageminPlugin = require('imagemin-webpack-plugin').default;

  webpackProd = merge(webpackProd, {
    plugins: [
      new ImageminPlugin({
        plugins: [imageminMozjpeg({ quality: 80 })],
      }),
    ],
  });
}

webpackCommon = merge(webpackCommon, webpackProd);

if (config.enabled.cacheBusting) {
  const WebpackAssetsManifest = require('webpack-assets-manifest');

  webpackCommon.plugins.push(
    new WebpackAssetsManifest({
      output: 'assets.json',
      space: 2,
      writeToDisk: false,
      assets: config.manifest,
      customize: (entry) => {
        const { key, value } = entry;
        const sourcePath = path.basename(path.dirname(key));
        const targetPath = path.basename(path.dirname(value));
        if (sourcePath === targetPath) {
          return entry;
        }

        return {
          key: `${targetPath}/${key}`,
          value,
        };
      },
    })
  );
}

module.exports = function () {
  return webpackCommon;
};
