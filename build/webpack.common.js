'use strict'; // eslint-disable-line

const webpack = require('webpack');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const FriendlyErrorsWebpackPlugin = require('friendly-errors-webpack-plugin');
const FilterWarningsPlugin = require('webpack-filter-warnings-plugin');
const CopyGlobsPlugin = require('copy-globs-webpack-plugin');
const ESLintPlugin = require('eslint-webpack-plugin');

// config
const config = require('./config');

const assetsFilenames = config.enabled.cacheBusting
  ? config.cacheBusting
  : '[name]';

let webpackConfig = {
  mode: config.env.production ? 'production' : 'development',
  context: config.paths.assets,
  entry: config.entry,
  devtool: config.enabled.sourceMaps ? '#source-map' : undefined,
  output: {
    path: config.paths.dist,
    publicPath: '.' + config.publicPath,
    filename: `scripts/${assetsFilenames}.js`,
  },
  stats: {
    hash: false,
    version: false,
    timings: false,
    children: false,
    errors: true,
    errorDetails: true,
    warnings: false,
    chunks: false,
    modules: false,
    reasons: false,
    source: false,
    publicPath: false,
  },
  module: {
    rules: [
      {
        enforce: 'pre',
        test: /\.js$/,
        include: config.paths.assets,
        loader: ESLintPlugin.loader,
      },
      {
        enforce: 'pre',
        test: /\.(js|s?[ca]ss)$/,
        include: config.paths.assets,
        loader: 'import-glob',
      },
      {
        test: /\.(sa|sc|c)ss$/,
        include: config.paths.assets,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../',
            },
          },
          {
            loader: 'css-loader',
            options: { sourceMap: config.enabled.sourceMaps },
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: config.enabled.sourceMaps,
              postcssOptions: {
                parser: 'postcss-safe-parser',
                plugins: {
                  cssnano: {
                    preset: [
                      'default',
                      { discardComments: { removeAll: true } },
                    ],
                  },
                  autoprefixer: true,
                },
              },
            },
          },
          {
            loader: 'resolve-url-loader',
            options: { sourceMap: config.enabled.sourceMaps },
          },
          {
            loader: 'sass-loader',
            options: {
              sassOptions: {
                sourceMap: config.enabled.sourceMaps,
                sourceMapContents: false,
                outputStyle: 'compressed',
              },
            },
          },
        ],
      },
      {
        test: /\.(ttf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
        include: config.paths.assets,
        loader: 'url-loader',
        options: {
          limit: 4096,
          name: `[path]${assetsFilenames}.[ext]`,
        },
      },
      {
        test: /\.(ttf|eot|woff2?|png|jpe?g|gif|svg|ico)$/,
        include: /node_modules/,
        loader: 'url-loader',
        options: {
          limit: 4096,
          outputPath: 'vendor/',
          name: `${assetsFilenames}.[ext]`,
        },
      },
    ],
  },
  resolve: {
    modules: [config.paths.assets, 'node_modules'],
    enforceExtension: false,
  },
  externals: {
    jquery: 'jQuery',
  },
  optimization: {
    minimize: config.env.production,
  },
  plugins: [
    new CopyGlobsPlugin({
      pattern: config.copy,
      output: `[path][name]${
        config.enabled.cacheBusting ? '_[hash]' : ''
      }.[ext]`,
      manifest: config.manifest,
    }),
    new ESLintPlugin(),
    new MiniCssExtractPlugin({
      filename: `./styles/${assetsFilenames}.css`,
      ignoreOrder: true,
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery',
    }),
    new webpack.LoaderOptionsPlugin({
      minimize: config.enabled.optimize,
      debug: config.enabled.watcher,
      stats: { colors: true },
    }),
    new webpack.LoaderOptionsPlugin({
      test: /\.s?css$/,
      options: {
        output: { path: config.paths.dist },
        context: config.paths.assets,
      },
    }),
    new FilterWarningsPlugin({
      exclude: [
        /System\.import\(\) is deprecated and will be removed soon\. Use import\(\) instead\./,
      ],
    }),
    new FriendlyErrorsWebpackPlugin({
      clearConsole: false,
    }),
  ],
};

module.exports = webpackConfig;
